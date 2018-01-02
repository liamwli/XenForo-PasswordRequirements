<?php

class LiamW_PasswordRequirements_Extend_DataWriter_User extends XFCP_LiamW_PasswordRequirements_Extend_DataWriter_User
{
	public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false)
	{
		// Allow admins to set unauthorised password, allow NoPassword method (cannot login with any password) and allow users' with bypass permission.
		if ($this->getOption(self::OPTION_ADMIN_EDIT) || ($auth instanceof XenForo_Authentication_NoPassword) || ($this->isUpdate() && XenForo_Visitor::getInstance()->hasPermission('general', 'lw_bypassPr')))
		{
			return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
		}

		$passwordCriteria = XenForo_Application::getOptions()->liam_passwordCriteria;

		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $forcePasswordChangeModel */
		$forcePasswordChangeModel = XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange');

		$errors = array();

		if ($this->_getPasswordBlacklistModel()->isPasswordBlacklisted($password))
		{
			$errors[] = new XenForo_Phrase('liam_passwordRequirements_requested_password_blacklisted');
		}

		// Check if the user is changing their password to their current password.
		$testAuth = XenForo_Authentication_Abstract::create($this->getExisting('scheme_class'));
		$testAuth->setData($this->get('data', 'xf_user_authenticate'));

		if ($testAuth->authenticate($this->get('user_id'), $password))
		{
			$errors[] = new XenForo_Phrase('liam_passwordRequirements_you_cannot_set_your_new_password_same_as_old');
		}

		// Check historic passwords
		if ($historicPasswordCount = $passwordCriteria['password_history'])
		{
			$userId = $this->get('user_id');

			$historicPasswords = $forcePasswordChangeModel->getHistoricPasswordDataForUser($userId, $historicPasswordCount);

			foreach ($historicPasswords AS $historicPassword)
			{
				try
				{
					$testAuth = XenForo_Authentication_Abstract::create($historicPassword['scheme_class']);
				} catch (XenForo_Exception $e)
				{
					//TODO: Check another valid old password?
					continue;
				}

				$testAuth->setData($historicPassword['data']);

				if ($testAuth->authenticate($userId, $password))
				{
					$errors[] = new XenForo_Phrase('liam_passwordRequirements_this_password_has_been_used_before_cannot_use_previous_x_passwords', array(
						'count' => $historicPasswordCount
					));
					break;
				}
			}
		}

		if ($passwordCriteria['min_age'] && $this->isUpdate() && !$forcePasswordChangeModel->isPasswordChangeRequired($null))
		{
			if (($this->get('password_date') + ($passwordCriteria['min_age'] * 24 * 60 * 60)) > XenForo_Application::$time)
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_wait_x_days_to_change_password', array(
					'remaining' => ceil(((($this->get('password_date') + ($passwordCriteria['min_age'] * 24 * 60 * 60)) - XenForo_Application::$time) / (24 * 60 * 60)))
				));
			}
		}

		if ($passwordCriteria['min_length'])
		{
			if (mb_strlen($password) < $passwordCriteria['min_length'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_be_at_least_x_characters', array('count' => $passwordCriteria['min_length']));
			}
		}

		if ($passwordCriteria['max_length'])
		{
			if (mb_strlen($password) > $passwordCriteria['min_length'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_be_less_than_x_characters', array('count' => $passwordCriteria['min_length']));
			}
		}

		if ($passwordCriteria['min_special'] && !$passwordCriteria['max_special'])
		{
			if (!preg_match_all('#[^\w]#iu', $password, $matches) || count($matches[0]) < $passwordCriteria['min_special'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_contain_at_least_x_special_characters', array(
					'count' => $passwordCriteria['min_special']
				));
			}
		}
		else if (!$passwordCriteria['min_special'] && $passwordCriteria['max_special'])
		{
			if (preg_match_all('#[^\w]#iu', $password, $matches) && count($matches[0]) > $passwordCriteria['max_special'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_contain_no_more_than_x_special_characters', array(
					'count' => $passwordCriteria['max_special']
				));
			}
		}
		else if ($passwordCriteria['min_special'] && $passwordCriteria['max_special'])
		{
			if (!preg_match_all('#[^\w]#iu', $password, $matches) || count($matches[0]) < $passwordCriteria['min_special'] || count($matches[0]) > $passwordCriteria['max_special'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_contain_between_x_and_x_special_characters', array(
					'min' => $passwordCriteria['min_special'],
					'max' => $passwordCriteria['max_special']
				));
			}
		}

		try
		{
			if ($passwordCriteria['regex'])
			{
				if (!preg_match($passwordCriteria['regex'], $password))
				{
					$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_be_correct_format');
				}
			}
		} catch (ErrorException $e)
		{
			XenForo_Error::logException($e, false, 'Error while checking password regex match: ');
		}

		if ($passwordCriteria['complex'])
		{
			if ($this->get('username') && !preg_match('/[A-Z]+/', $password) || !preg_match('/[0-9]+/', $password) || mb_stripos($password, $this->get('username')) !== false || mb_strlen($password) < 8)
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_not_meet_complexity_requirements');
			}
		}

		if ($errors)
		{
			$this->error(array_shift($errors), 'password');

			foreach ($errors AS $error)
			{
				$this->error($error);
			}

			return false;
		}

		return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
	}

	protected function _postSave()
	{
		parent::_postSave();

		if ($this->isChanged('data', 'xf_user_authenticate'))
		{
			$this->_db->delete('liam_pr_force_change', 'user_id = ' . $this->get('user_id') . ' AND initiation_date < ' . XenForo_Application::$time);
		}
	}

	protected function _postSaveAfterTransaction()
	{
		parent::_postSaveAfterTransaction();

		if ($this->isChanged('data', 'xf_user_authenticate'))
		{
			$method = ($this->isInsert() ? 'getNew' : 'getExisting');

			$this->_db->insert('liam_pr_password_history', array(
				'user_id'      => $this->get('user_id'),
				'change_date'  => XenForo_Application::$time,
				'scheme_class' => $this->$method('scheme_class'),
				'data'         => $this->$method('data', 'xf_user_authenticate')
			));
		}
	}

	/**
	 * @return LiamW_PasswordRequirements_Model_PasswordBlacklist
	 */
	protected function _getPasswordBlacklistModel()
	{
		return $this->getModelFromCache('LiamW_PasswordRequirements_Model_PasswordBlacklist');
	}
}

if (false)
{
	class XFCP_LiamW_PasswordRequirements_Extend_DataWriter_User extends XenForo_DataWriter_User
	{
	}
}
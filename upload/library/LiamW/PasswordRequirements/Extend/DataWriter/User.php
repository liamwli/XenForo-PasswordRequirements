<?php

class LiamW_PasswordRequirements_Extend_DataWriter_User extends XFCP_LiamW_PasswordRequirements_Extend_DataWriter_User
{
	public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false)
	{
		if (XenForo_Visitor::getUserId() != $this->get('user_id') && $this->isUpdate())
		{
			return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
		}

		$passwordCriteria = XenForo_Application::getOptions()->liam_passwordCriteria;

		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $forcePasswordChangeModel */
		$forcePasswordChangeModel = XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange');

		$errors = array();
		if ($passwordCriteria['min_age'] && $this->isUpdate() && !$forcePasswordChangeModel->isPasswordChangeRequired($null))
		{
			if (($this->get('password_date') + ($passwordCriteria['min_age'] * 24 * 60 * 60)) > XenForo_Application::$time)
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_wait_x_days_to_change_password', array(
					'remaining' =>
						ceil(((($this->get('password_date') + ($passwordCriteria['min_age'] * 24 * 60 * 60)) - XenForo_Application::$time) / (24 * 60 * 60)))
				));
			}
		}

		if ($passwordCriteria['min_length'])
		{
			if (mb_strlen($password) < $passwordCriteria['min_length'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_be_at_least_x_characters',
					array('count' => $passwordCriteria['min_length']));
			}
		}

		if ($passwordCriteria['max_length'])
		{
			if (mb_strlen($password) > $passwordCriteria['min_length'])
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_must_be_less_than_x_characters',
					array('count' => $passwordCriteria['min_length']));
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
			XenForo_Error::logException($e);
		}

		if ($passwordCriteria['complex'])
		{
			if (!preg_match('/[A-Z]+/', $password) || !preg_match('/[0-9]+/', $password) || mb_stripos($password,
					$this->get('username')) !== false || mb_strlen($password) < 8
			)
			{
				$errors[] = new XenForo_Phrase('liam_passwordRequirements_not_meet_complexity_requirements');
			}
		}

		if ($errors)
		{
			$this->error(reset($errors));

			return false;
		}

		return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
	}
}

if (false)
{
	class XFCP_LiamW_PasswordRequirements_Extend_DataWriter_User extends XenForo_DataWriter_User
	{
	}
}
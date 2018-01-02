<?php

class LiamW_PasswordRequirements_Model_ForcePasswordChange extends XenForo_Model
{
	/**
	 * Inserts a record to force a password change for a user or all users.
	 *
	 * @param int $userId The user id of the user to force a password on, or 0 for all users.
	 *
	 * @throws \Zend_Db_Adapter_Exception
	 */
	public function forcePasswordChange($userId = 0)
	{
		$this->_getDb()->insert('liam_pr_force_change', array(
			'user_id'         => $userId,
			'initiation_date' => XenForo_Application::$time
		));
	}

	/**
	 * Gets the user ids and initiation dates of users who've had a forced password change initiated.
	 *
	 * @return array [user_id => initiation_date]
	 */
	public function getUserBasedForcedChanges()
	{
		return $this->_getDb()->fetchPairs('SELECT user_id,initiation_date FROM liam_pr_force_change WHERE user_id > 0');
	}

	/**
	 * Performs the logic to check if a password change is required of a user. Checks force password changes (both user
	 * specific and global), as well as expired passwords.
	 *
	 * @param string     $errorPhraseKey Passed by reference. The error message phrase key to display to the user when
	 *                                   they're changing their password.
	 * @param array|null $viewingUser
	 *
	 * @return bool
	 */
	public function isPasswordChangeRequired(&$errorPhraseKey, array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if (!$viewingUser['user_id'])
		{
			return false;
		}

		$changeRequired = false;

		if ($this->_getDb()->fetchOne("SELECT COUNT(*) FROM liam_pr_force_change WHERE (user_id=? OR user_id=0) AND initiation_date>?", array(
			$viewingUser['user_id'],
			$viewingUser['password_date']
		))
		)
		{
			$changeRequired = true;
			$errorPhraseKey = 'liam_passwordRequirements_password_must_be_changed';
		}

		if ($maxAge = XenForo_Application::getOptions()->liam_passwordCriteria['max_age'])
		{
			$visitor = XenForo_Visitor::getInstance();

			if ($visitor['password_date'] + ($maxAge * 24 * 60 * 60) < XenForo_Application::$time)
			{
				$changeRequired = true;
				$errorPhraseKey = 'liam_passwordRequirements_password_expired_must_be_changed';
			}
		}

		return $changeRequired;
	}

	public function getHistoricPasswordDataForUser($userId, $limit)
	{
		return $this->_getDb()->fetchAll($this->limitQueryResults('SELECT scheme_class,data FROM liam_pr_password_history WHERE user_id=? ORDER BY change_date DESC', $limit), $userId);
	}

	/**
	 * Changes a user's password method to NoPassword preventing login.
	 *
	 * @param int  $userId         ID of user to reset password.
	 * @param bool $sendResetEmail If true, send password reset email.
	 */
	public function forcePasswordReset($userId, $sendResetEmail = false)
	{
		/** @var XenForo_DataWriter_User $userDw */
		$userDw = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$userDw->setExistingData($userId);
		$userDw->setPassword(false, false, new XenForo_Authentication_NoPassword());
		$userDw->save();

		if ($sendResetEmail)
		{
			/** @var XenForo_Model_UserConfirmation $userConfirmationModel */
			$userConfirmationModel = $this->getModelFromCache('XenForo_Model_UserConfirmation');
			$userConfirmationModel->sendPasswordResetRequest($userDw->getMergedData());
		}
	}
}
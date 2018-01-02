<?php

class LiamW_PasswordRequirements_Model_ForcePasswordChange extends XenForo_Model
{
	public function forcePasswordChange($userId = 0)
	{
		$this->_getDb()->insert(
			'liam_pr_force_change', array(
				'user_id' => $userId,
				'initiation_date' => XenForo_Application::$time
			)
		);
	}

	public function getUserBasedForcedChanges()
	{
		return $this->_getDb()
			->fetchPairs('SELECT user_id,initiation_date FROM liam_pr_force_change WHERE user_id > 0');
	}

	public function isPasswordChangeRequired(&$errorPhraseKey, array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if (!$viewingUser['user_id'])
		{
			return false;
		}

		$changeRequired = false;

		if ($this->_getDb()
			->fetchOne("SELECT COUNT(*) FROM liam_pr_force_change WHERE (user_id=? OR user_id=0) AND initiation_date>?",
				array(
					$viewingUser['user_id'],
					$viewingUser['password_date']
				)
			)
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
		return $this->_getDb()
			->fetchAll($this->limitQueryResults('SELECT scheme_class,data FROM liam_pr_password_history WHERE user_id=? ORDER BY change_date DESC',
				$limit), $userId);
	}
}
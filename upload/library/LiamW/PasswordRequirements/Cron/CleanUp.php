<?php

class LiamW_PasswordRequirements_Cron_CleanUp
{
	public static function run()
	{
		self::_cleanUpHistoryTable();
		self::_cleanUpForceResetTable();
	}

	protected static function _cleanUpHistoryTable()
	{
		$db = XenForo_Application::getDb();

		$maxHistory = XenForo_Application::getOptions()->liam_passwordCriteria['password_history'];

		$userIds = $db->fetchCol("
			SELECT user_id AS user_id
				FROM
			(SELECT user_id, COUNT(*) AS count FROM liam_pr_password_history GROUP BY user_id) AS tbl
				WHERE count > ?
		", $maxHistory);

		foreach ($userIds AS $userId)
		{
			$values = $db->fetchCol("SELECT change_date FROM liam_pr_password_history WHERE user_id=? ORDER BY change_date DESC LIMIT ?",
				array(
					$userId,
					$maxHistory
				));

			$min = end($values);

			$db->query("DELETE FROM liam_pr_password_history WHERE user_id=? AND change_date < ?", array(
				$userId,
				$min
			));
		}
	}

	protected static function _cleanUpForceResetTable()
	{
		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $forceChangeModel */
		$forceChangeModel = XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange');

		/** @var XenForo_Model_User $userModel */
		$userModel = XenForo_Model::create('XenForo_Model_User');

		$forcedUsers = $forceChangeModel->getUserBasedForcedChanges();

		$db = XenForo_Application::getDb();

		foreach ($forcedUsers as $userId => $initiationDate)
		{
			$user = $userModel->getUserById($userId);

			if (isset($user['password_date']) && $user['password_date'] > $initiationDate)
			{
				$db->delete('liam_pr_force_change', 'user_id=' . $userId);
			}
		}
	}
}
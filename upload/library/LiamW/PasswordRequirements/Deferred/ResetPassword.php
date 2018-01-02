<?php

class LiamW_PasswordRequirements_Deferred_ResetPassword extends XenForo_Deferred_Abstract
{
	public function execute(array $deferred, array $data, $targetRunTime, &$status)
	{
		$data = array_merge(array(
			'position'          => 0,
			'batch'             => 1,
			'send_reset_email'  => false,
			'initiator_user_id' => 0
		), $data);
		$data['batch'] = max(1, $data['batch']);

		$startTime = microtime(true);

		/** @var XenForo_Model_User $userModel */
		$userModel = XenForo_Model::create(XenForo_Model_User::class);
		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $passwordChangeModel */
		$passwordChangeModel = XenForo_Model::create(LiamW_PasswordRequirements_Model_ForcePasswordChange::class);

		$userIds = $userModel->getUserIdsInRange($data['position'], $data['batch']);
		if (count($userIds) == 0)
		{
			$passwordChangeModel->forcePasswordReset($data['initiator_user_id'], $data['send_reset_email']);

			return false;
		}

		foreach ($userIds AS $userId)
		{
			$data['position'] = $userId;

			if ($userId == $data['initiator_user_id'])
			{
				// Don't reset the password of the initiating user until the very last moment to prevent logout and interruption.
				continue;
			}

			$passwordChangeModel->forcePasswordReset($userId, $data['send_reset_email']);

			if ($targetRunTime && microtime(true) - $startTime > $targetRunTime)
			{
				break;
			}
		}

		$actionPhrase = new XenForo_Phrase('xfliam_passwordRequirements_resetting_passwords');
		$status = sprintf('%s... (User %s)', $actionPhrase, XenForo_Locale::numberFormat($data['position']));

		return $data;
	}

	public function canCancel()
	{
		return true;
	}
}
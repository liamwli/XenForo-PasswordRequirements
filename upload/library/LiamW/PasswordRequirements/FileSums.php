<?php

class LiamW_PasswordRequirements_FileSums
{
	public static function addHashes(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
	{
		$hashes += self::getHashes();
	}

	/**
	 * @return array
	 */
	public static function getHashes()
	{
		return array(
			'library/LiamW/PasswordRequirements/ControllerAdmin/PasswordBlacklist.php'   => 'dc4d5cd684326f9850105699676d9966',
			'library/LiamW/PasswordRequirements/Cron/CleanUp.php'                        => '4bac671c997334fbc1aa47694f58445f',
			'library/LiamW/PasswordRequirements/DataWriter/PasswordBlacklist.php'        => '80e40f731e21971cddd94b2ed46d41c5',
			'library/LiamW/PasswordRequirements/Deferred/ResetPassword.php'              => 'b9161588065c8e15e09ee751a36cd9aa',
			'library/LiamW/PasswordRequirements/Extend/ControllerAdmin/User.php'         => 'e6264092a188a73242e3fbd6f9efa5f6',
			'library/LiamW/PasswordRequirements/Extend/ControllerPublic/Account.php'     => 'ed35ca49d3332c1ad5956cff515a7dd4',
			'library/LiamW/PasswordRequirements/Extend/DataWriter/User.php'              => 'd935defdd0520c0416a1582cc58a25d8',
			'library/LiamW/PasswordRequirements/Installer.php'                           => '6fd204f91d8106066300b77760bc0f6e',
			'library/LiamW/PasswordRequirements/Listener.php'                            => '01d6fee3d9127a84af17cdf2760718d3',
			'library/LiamW/PasswordRequirements/Model/ForcePasswordChange.php'           => 'a6903a92f769b2c329f06e5e274cf5c6',
			'library/LiamW/PasswordRequirements/Model/PasswordBlacklist.php'             => 'b09c27e19b83eb07b74b9988d7734d94',
			'library/LiamW/PasswordRequirements/Option/PasswordCriteria.php'             => '6dadd71022e56a7aecd2bd492c1613f2',
			'library/LiamW/PasswordRequirements/Route/PrefixAdmin/PasswordBlacklist.php' => 'eb24a048373cbe122bc6f9a874ec7d3e',
		);
	}
}
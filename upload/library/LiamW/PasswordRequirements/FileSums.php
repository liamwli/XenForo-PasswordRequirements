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
			'library/LiamW/PasswordRequirements/ControllerAdmin/PasswordBlacklist.php' => 'dc4d5cd684326f9850105699676d9966',
			'library/LiamW/PasswordRequirements/Cron/CleanUp.php' => '4bac671c997334fbc1aa47694f58445f',
			'library/LiamW/PasswordRequirements/DataWriter/PasswordBlacklist.php' => '80e40f731e21971cddd94b2ed46d41c5',
			'library/LiamW/PasswordRequirements/Extend/ControllerAdmin/User.php' => 'f3c17186874a8e12a4467283544e8f07',
			'library/LiamW/PasswordRequirements/Extend/DataWriter/User.php' => 'c03d5e80f56718537b824a9687ee0e89',
			'library/LiamW/PasswordRequirements/Installer.php' => '4c184379a285c6e0bd50008dc72e8730',
			'library/LiamW/PasswordRequirements/Listener.php' => '217626be85d9e39bb0897ad9d7ce2434',
			'library/LiamW/PasswordRequirements/Model/ForcePasswordChange.php' => '9211745af9f716eda8a02a27685d9abe',
			'library/LiamW/PasswordRequirements/Model/PasswordBlacklist.php' => 'b09c27e19b83eb07b74b9988d7734d94',
			'library/LiamW/PasswordRequirements/Option/PasswordCriteria.php' => '2d7e62c21e151b9ec9d288a953c395b7',
			'library/LiamW/PasswordRequirements/Route/PrefixAdmin/PasswordBlacklist.php' => 'eb24a048373cbe122bc6f9a874ec7d3e',
		);
	}
}
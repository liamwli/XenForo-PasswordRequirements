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
			'library/LiamW/PasswordRequirements/Cron/CleanUp.php' => '4bac671c997334fbc1aa47694f58445f',
			'library/LiamW/PasswordRequirements/Extend/ControllerAdmin/User.php' => 'f3c17186874a8e12a4467283544e8f07',
			'library/LiamW/PasswordRequirements/Extend/DataWriter/User.php' => 'd50df04ad91147119f57e2e4438ebb94',
			'library/LiamW/PasswordRequirements/Installer.php' => '89c3bbc0711c755e378ca4bba84280ac',
			'library/LiamW/PasswordRequirements/Listener.php' => 'bb638dd0122e596e6e82e90fdf76169b',
			'library/LiamW/PasswordRequirements/Model/ForcePasswordChange.php' => '9211745af9f716eda8a02a27685d9abe',
		);
	}
}
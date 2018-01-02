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
			'library/LiamW/PasswordRequirements/Extend/ControllerAdmin/User.php' => 'f3c17186874a8e12a4467283544e8f07',
			'library/LiamW/PasswordRequirements/Extend/DataWriter/User.php' => 'e1a4446e0e9a537c60b76e4b0e3506e8',
			'library/LiamW/PasswordRequirements/Installer.php' => '7c0104dcc6ca0aadaafab09d6c567765',
			'library/LiamW/PasswordRequirements/Listener.php' => 'bb638dd0122e596e6e82e90fdf76169b',
			'library/LiamW/PasswordRequirements/Model/ForcePasswordChange.php' => '319a2f1d09d3871f42859a23b66532eb',
		);
	}
}
<?php

class LiamW_PasswordRequirements_Installer
{
	protected static $_coreAlters = array();

	protected static $_tables = array(
		'liam_pr_force_change' => "
			CREATE TABLE IF NOT EXISTS liam_pr_force_change (
				entry_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INT(10) UNSIGNED NOT NULL,
				initiation_date INT(10) UNSIGNED NOT NULL
			) ENGINE=InnoDB
		",
		'liam_pr_password_history' => "
			CREATE TABLE IF NOT EXISTS liam_pr_password_history (
				user_id INT(10) UNSIGNED NOT NULL,
				change_date INT(10) UNSIGNED NOT NULL,
				scheme_class VARCHAR(75) NOT NULL,
				data MEDIUMBLOB NOT NULL,
				INDEX user_id(user_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		"
	);

	protected static function _canBeInstalled(&$error)
	{
		if (XenForo_Application::$versionId < 1030070)
		{
			$error = 'XenForo 1.3.0+ is required. Please upgrade and then install.';

			return false;
		}

		$errors = XenForo_Helper_Hash::compareHashes(LiamW_PasswordRequirements_FileSums::getHashes());

		if ($errors)
		{
			$error = "The following files could not be found or contain unexpected contents: <ul>";

			foreach ($errors as $file => $errorStr)
			{
				$error .= "<li>$file - " . ($errorStr == 'mismatch' ? 'File contains unexpected contents' : 'File not found') . "</li>";
			}

			$error .= '</ul>';

			return false;
		}

		return true;
	}

	public static function install($installedAddon)
	{
		if (!self::_canBeInstalled($error))
		{
			throw new XenForo_Exception($error, true);
		}

		self::_installTables();
		self::_installCoreAlters();
	}

	public static function uninstall()
	{
		self::_uninstallTables();
		self::_uninstallCoreAlters();
	}

	protected static function _installTables()
	{
		foreach (self::$_tables AS $tableName => $installSql)
		{
			self::_query($installSql);
		}
	}

	protected static function _uninstallTables()
	{
		foreach (self::$_tables AS $tableName => $installSql)
		{
			self::_query("DROP TABLE $tableName");
		}
	}

	protected static function _installCoreAlters()
	{
		foreach (self::$_coreAlters as $table => $coreAlters)
		{
			foreach ($coreAlters as $columnName => $installSql)
			{
				self::_query($installSql);
			}
		}
	}

	protected static function _uninstallCoreAlters()
	{
		foreach (self::$_coreAlters as $table => $coreAlters)
		{
			foreach ($coreAlters as $columnName => $installSql)
			{
				self::_query("ALTER TABLE $table DROP $columnName");
			}
		}
	}

	protected static function _query($sql)
	{
		$db = XenForo_Application::getDb();

		try
		{
			$db->query($sql);
		} catch (Zend_Db_Exception $e)
		{
		}
	}
}
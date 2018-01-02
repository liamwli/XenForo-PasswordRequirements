<?php

class LiamW_PasswordRequirements_Model_PasswordBlacklist extends XenForo_Model
{
	public function getAllBlacklistedPasswords()
	{
		return $this->fetchAllKeyed("SELECT * FROM liam_pr_password_blacklist", 'word_id');
	}

	public function getBlacklistedPasswordById($wordId)
	{
		return $this->_getDb()->fetchRow("SELECT * FROM liam_pr_password_blacklist WHERE word_id=?", $wordId);
	}

	public function getActiveBlacklistedWords()
	{
		return $this->_getDb()->fetchCol("SELECT word FROM liam_pr_password_blacklist WHERE active=1");
	}

	public function blacklistedWordUnique($word)
	{
		return ($this->_getDb()
				->fetchOne("SELECT word FROM liam_pr_password_blacklist WHERE word = ?", $word) !== $word);
	}

	public function importMultiple($wordString)
	{
		$words = preg_split('/\r?\n/', $wordString, -1,
			PREG_SPLIT_NO_EMPTY);

		$words = array_map('trim', $words);

		$errors = array();

		foreach ($words as $word)
		{
			$dw = XenForo_DataWriter::create('LiamW_PasswordRequirements_DataWriter_PasswordBlacklist',
				XenForo_DataWriter::ERROR_ARRAY);
			$dw->set('word', $word);
			$dw->preSave();

			if ($dw->hasErrors())
			{
				$errors += $dw->getErrors();
				continue;
			}

			$dw->save();
		}

		return $errors;
	}

	public function isPasswordBlacklisted($password)
	{
		$words = array_map('utf8_strtolower', $this->getActiveBlacklistedWords());

		return in_array(strtolower($password), $words);
	}
}
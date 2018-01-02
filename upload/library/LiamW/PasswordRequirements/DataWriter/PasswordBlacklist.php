<?php

class LiamW_PasswordRequirements_DataWriter_PasswordBlacklist extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'liam_pr_password_blacklist' => array(
				'word_id' => array(
					'type' => self::TYPE_UINT,
					'autoIncrement' => true
				),
				'word' => array(
					'type' => self::TYPE_STRING,
					'required' => true,
					'verification' => array(
						$this,
						'_verifyWord'
					)
				),
				'active' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 1
				)
			)
		);
	}

	protected function _verifyWord($word)
	{
		if (!$this->_getPasswordBlacklistModel()->blacklistedWordUnique($word))
		{
			$this->error(new XenForo_Phrase('liam_passwordRequirements_words_must_be_unique_word_already_blacklisted'), 'word');

			return false;
		}

		return true;
	}

	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}

		return array(
			'liam_pr_password_blacklist' => $this->_getPasswordBlacklistModel()->getBlacklistedPasswordById($id)
		);
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'word_id = ' . $this->_db->quote($this->getExisting('word_id'));
	}

	/**
	 * @return LiamW_PasswordRequirements_Model_PasswordBlacklist
	 */
	protected function _getPasswordBlacklistModel()
	{
		return $this->getModelFromCache('LiamW_PasswordRequirements_Model_PasswordBlacklist');
	}
}
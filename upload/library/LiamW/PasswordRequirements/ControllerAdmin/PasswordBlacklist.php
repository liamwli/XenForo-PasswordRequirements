<?php

class LiamW_PasswordRequirements_ControllerAdmin_PasswordBlacklist extends XenForo_ControllerAdmin_Abstract
{
	protected function _preDispatch($action)
	{
		$this->assertAdminPermission('manageUsers');
	}

	public function actionIndex()
	{
		$viewParams = array(
			'words' => $this->_getPasswordBlacklistModel()->getAllBlacklistedPasswords()
		);

		return $this->responseView('LiamW_PasswordRequirements_ViewAdmin_PasswordBlacklist_Index',
			'liam_pr_password_blacklist_index', $viewParams);
	}

	public function actionAdd()
	{
		return $this->responseView('LiamW_PasswordRequirements_ViewAdmin_PasswordBlacklist_Add',
			'liam_pr_password_blacklist_edit');
	}

	public function actionEdit()
	{
		$viewParams = array(
			'word' => $this->_getWordOrError()
		);

		return $this->responseView('LiamW_PasswordRequirements_ViewAdmin_PasswordBlacklist_Edit',
			'liam_pr_password_blacklist_edit', $viewParams);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		if ($this->_input->filterSingle('single',
				XenForo_Input::BOOLEAN) || $wordId = $this->_input->filterSingle('word_id', XenForo_Input::UINT)
		)
		{
			$data = $this->_input->filter(array(
				'word' => XenForo_Input::STRING,
				'active' => XenForo_Input::BOOLEAN
			));

			$dw = XenForo_DataWriter::create('LiamW_PasswordRequirements_DataWriter_PasswordBlacklist');
			if (!empty($wordId))
			{
				$dw->setExistingData($wordId);
			}
			$dw->bulkSet($data);
			$dw->save();
		}
		else
		{
			$value = $this->_input->filterSingle('multi_import', XenForo_Input::STRING);

			$this->_getPasswordBlacklistModel()->importMultiple($value);
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('password-blacklist'));
	}

	public function actionDelete()
	{
		$word = $this->_getWordOrError();

		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('LiamW_PasswordRequirements_DataWriter_PasswordBlacklist');
			$dw->setExistingData($word, true);
			$dw->delete();

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('password-blacklist'));
		}
		else
		{
			$viewParams = array(
				'word' => $word
			);

			return $this->responseView('LiamW_PasswordRequirements_ViewAdmin_PasswordBlacklist_DeleteConfirm',
				'liam_pr_password_blacklist_delete_confirm', $viewParams);
		}
	}

	public function actionToggle()
	{
		return $this->_getToggleResponse($this->_getPasswordBlacklistModel()->getAllBlacklistedPasswords(),
			'LiamW_PasswordRequirements_DataWriter_PasswordBlacklist', 'password-blacklist');
	}

	protected function _getWordOrError($wordId = null)
	{
		if ($wordId === null)
		{
			$wordId = $this->_input->filterSingle('word_id', XenForo_Input::UINT);
		}

		$word = $this->_getPasswordBlacklistModel()->getBlacklistedPasswordById($wordId);

		if (!$word)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('liam_pr_password_blacklist_word_could_not_be_found'),
				404));
		}

		return $word;
	}

	/**
	 * @return LiamW_PasswordRequirements_Model_PasswordBlacklist
	 */
	protected function _getPasswordBlacklistModel()
	{
		return $this->getModelFromCache('LiamW_PasswordRequirements_Model_PasswordBlacklist');
	}
}
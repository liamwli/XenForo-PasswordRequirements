<?php

class LiamW_PasswordRequirements_Extend_ControllerAdmin_User extends XFCP_LiamW_PasswordRequirements_Extend_ControllerAdmin_User
{
	public function actionForcePasswordChange()
	{
		$userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);

		if ($userId)
		{
			$user = $this->_getUserOrError($userId);

			if ($this->isConfirmedPost())
			{
				XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange')
					->forcePasswordChange($user['user_id']);

				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
					$this->getDynamicRedirect());
			}
			else
			{
				$viewParams = array(
					'user' => $user
				);

				return $this->responseView('', 'liam_passwordRequirements_force_confirm', $viewParams);
			}
		}
		else
		{
			if ($this->isConfirmedPost())
			{
				XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange')->forcePasswordChange();

				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
					$this->getDynamicRedirect());
			}
			else
			{
				return $this->responseView('', 'liam_passwordRequirements_force_confirm');
			}
		}
	}
}

if (false)
{
	class XFCP_LiamW_PasswordRequirements_Extend_ControllerAdmin_User extends XenForo_ControllerAdmin_User
	{
	}
}
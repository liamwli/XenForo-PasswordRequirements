<?php

class LiamW_PasswordRequirements_Extend_ControllerPublic_Account extends XFCP_LiamW_PasswordRequirements_Extend_ControllerPublic_Account
{
	public function actionSecuritySave()
	{
		$response = parent::actionSecuritySave();

		if (XenForo_Application::getSimpleCacheData('liam_passwordRequirements_required_redirect') && $response instanceof XenForo_ControllerResponse_Redirect)
		{
			$response->redirectTarget = XenForo_Application::getSimpleCacheData('liam_passwordRequirements_required_redirect');

			XenForo_Application::setSimpleCacheData('liam_passwordRequirements_required_redirect', false);
		}

		return $response;
	}
}

if (false)
{
	class XFCP_LiamW_PasswordRequirements_Extend_ControllerPublic_Account extends XenForo_ControllerPublic_Account
	{
	}
}
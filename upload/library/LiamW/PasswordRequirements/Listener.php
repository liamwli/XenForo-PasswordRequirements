<?php

class LiamW_PasswordRequirements_Listener
{
	public static function extendUserController($class, array &$extend)
	{
		$extend[] = 'LiamW_PasswordRequirements_Extend_ControllerAdmin_User';
	}

	public static function extendUserDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_PasswordRequirements_Extend_DataWriter_User';
	}

	public static function controllerPreDispatch(XenForo_Controller $controller, $action)
	{
		if ($controller instanceof XenForo_ControllerAdmin_Abstract || $controller instanceof XenForo_ControllerPublic_Error || ($controller instanceof XenForo_ControllerPublic_Account && ($action == 'Security' || $action = 'SecuritySave')))
		{
			return;
		}

		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $forcePasswordChangeModel */
		$forcePasswordChangeModel = XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange');

		if ($forcePasswordChangeModel->isPasswordChangeRequired($errorPhraseKey))
		{
			throw $controller->responseException(
				$controller->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildPublicLink('account/security'),
					new XenForo_Phrase($errorPhraseKey))
			);
		}
	}

	public static function controllerPostDispatch(XenForo_Controller $controller, $controllerResponse, $controllerName, $action)
	{
		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $forcePasswordChangeModel */
		$forcePasswordChangeModel = XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange');

		if ($action == 'Security' && $controllerResponse instanceof XenForo_ControllerResponse_View && $forcePasswordChangeModel->isPasswordChangeRequired($errorPhraseKey)
		)
		{
			$controllerResponse->subView->params['forceText'] = new XenForo_Phrase($errorPhraseKey);
		}
	}
}
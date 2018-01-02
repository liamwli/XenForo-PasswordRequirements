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

	public static function extendAccountController($class, array &$extend)
	{
		$extend[] = 'LiamW_PasswordRequirements_Extend_ControllerPublic_Account';
	}

	public static function controllerPreDispatch(XenForo_Controller $controller, $action)
	{
		/** @var LiamW_PasswordRequirements_Model_ForcePasswordChange $forcePasswordChangeModel */
		$forcePasswordChangeModel = XenForo_Model::create('LiamW_PasswordRequirements_Model_ForcePasswordChange');

		// If this is true we don't need an error message. Save the query.
		if ($controller instanceof XenForo_ControllerPublic_Error || $controller->getRequest()->isPost() || $controller->getInput()->filterSingle('_xfNoRedirect', XenForo_Input::UINT))
		{
			return;
		}

		if ($forcePasswordChangeModel->isPasswordChangeRequired($errorPhraseKey))
		{
			// We need to change the password, but they're on the change password page. Stop the redirection from happening, and set the error 'global'
			if (($controller instanceof XenForo_ControllerPublic_Account) && ($action == 'Security' || $action == 'SecuritySave'))
			{
				XenForo_Application::set('liam_forceChangeRequired', $errorPhraseKey);

				return;
			}

			$root = XenForo_Application::getFc()->getDependencies()->getRouter()->getRoutePath($controller->getRequest());

			$method = ($controller instanceof XenForo_ControllerPublic_Abstract) ? 'buildPublicLink' : 'buildAdminLink';

			XenForo_Application::setSimpleCacheData('liam_passwordRequirements_required_redirect', XenForo_Link::$method($root, $action));

			throw $controller->responseException($controller->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('account/security'), new XenForo_Phrase($errorPhraseKey)));
		}
	}

	public static function controllerPostDispatch(XenForo_Controller $controller, $controllerResponse, $controllerName, $action)
	{
		if ($action == 'Security' && $controllerResponse instanceof XenForo_ControllerResponse_View && XenForo_Application::isRegistered('liam_forceChangeRequired'))
		{
			$controllerResponse->subView->params['forceText'] = new XenForo_Phrase(XenForo_Application::get('liam_forceChangeRequired'));
		}
	}
}
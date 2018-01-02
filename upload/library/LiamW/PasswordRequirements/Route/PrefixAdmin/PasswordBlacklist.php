<?php

class LiamW_PasswordRequirements_Route_PrefixAdmin_PasswordBlacklist implements XenForo_Route_Interface, XenForo_Route_BuilderInterface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'word_id');

		return $router->getRouteMatch('LiamW_PasswordRequirements_ControllerAdmin_PasswordBlacklist', $action, 'users');
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'word_id',
			'word');
	}
}
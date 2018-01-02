<?php

class LiamW_PasswordRequirements_Option_PasswordCriteria
{
	public static function verifyCriteria(array &$criteria, XenForo_DataWriter $dw, $fieldName)
	{
		if ($regex = $criteria['regex'])
		{
			if (preg_match('/\W[\s\w]*e[\s\w]*$/', $regex))
			{
				$dw->error(new XenForo_Phrase('please_enter_valid_regular_expression'), $fieldName);

				return false;
			}
			else
			{
				try
				{
					preg_match($regex, '');
				} catch (Exception $e)
				{
					$dw->error(new XenForo_Phrase('please_enter_valid_regular_expression'), $fieldName);

					return false;
				}
			}
		}

		if ($criteria['max_age'] && $criteria['min_age'])
		{
			if ($criteria['min_age'] > $criteria['max_age'])
			{
				$dw->error(new XenForo_Phrase('liam_passwordRequirements_min_age_must_smaller_max_age'), $fieldName);

				return false;
			}
		}

		if ($criteria['min_length'] && $criteria['max_length'])
		{
			if ($criteria['min_length'] > $criteria['max_length'])
			{
				$dw->error(new XenForo_Phrase('liam_passwordRequirements_min_length_must_smaller_max_length'),
					$fieldName);

				return false;
			}
		}

		if ($criteria['min_special'] && $criteria['max_special'])
		{
			if ($criteria['min_special'] > $criteria['max_special'])
			{
				$dw->error(new XenForo_Phrase('liam_passwordRequirements_min_special_must_smaller_max_special'),
					$fieldName);

				return false;
			}
		}

		return true;
	}
}

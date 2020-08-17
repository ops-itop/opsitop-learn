<?php

class AttributeJira extends AttributeUrl {
	public function __construct($sCode, $aParams)
	{
		parent::__construct($sCode, $aParams);
	}
	
	public static function ListExpectedParams()
	{
		return array();
	}

	public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
	{
		$sPrefix = $this->Get("default_value");
		$sLabel = Str::pure2html($sValue);
		if (strlen($sLabel) > 128)
		{
			// Truncate the length to 128 characters, by removing the middle
			$sLabel = substr($sLabel, 0, 100).'.....'.substr($sLabel, -20);
		}

		$sHref = $sPrefix . $sValue;

		return "<a target=\"_blank\" href=\"$sPrefix\">$sLabel</a>";
	}
}

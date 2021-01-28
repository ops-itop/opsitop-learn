<?php
/*
 * 此扩展需要修改 setup/compiler.class.inc.php, 搜索 AttributeCustomFields ，改为
 * elseif (strpos($sAttType,'AttributeCustomFields') === 0)
class AttributeCustomFieldsOps extends AttributeCustomFields {
	// application/ui.linkswidget.class.inc.php 中，编辑 linkset 时，如果 lnk 类本身有 AttributeCustomFields 类型的属性
	// 会导致报错 Uncaught Error: Object of class ormCustomFieldsValue could not be converted to string
	// 因此自定义一个属性类型，标记为不可写，就可以避免此问题
	function IsWritable() {
		return false;
	}
}
 */

class TestHandler extends CustomFieldsHandler {
	public function BuildForm(DBObject $oHostObject, $sFormId) {
		$this->oForm = new \Combodo\iTop\Form\Form($sFormId);
		$oField = new Combodo\iTop\Form\Field\HiddenField('test');
		$this->oForm->AddField($oField);
		$this->oForm->Finalize();
	}

	public function GetForm() {
		return $this->oForm;
	}

	public function GetForTemplate($aValues, $sVerb, $bLocalize = true) {
		return 'template...verb='.$sVerb.' sur "'.json_encode($aValues).'"';
	}

	public function GetAsHTML($aValues, $bLocalize = true) {
		return "<b>Test</b>";
	}

	public function GetAsXML($aValues, $bLocalize = true) {}

	public function GetAsCSV($aValues, $sSeparator = ',', $sTextQualifier = '"', $bLocalize = true) {
		return "Test";
	}

	public function ReadValues(DBObject $oHostObject) {
		return array("name"=>"Test");
	}

	public function WriteValues(DBObject $oHostObject, $aValues) {
		return;
	}

	public function DeleteValues(DBObject $oHostObject) {
		return;
	}

	public function CompareValues($aValuesA, $aValuesB) {
		return true;
	}

	public function GetValueFingerprint() {
		return json_encode($this->aValues);
	}
}

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

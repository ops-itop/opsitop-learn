<?php
class CustomDashletTable {
	public function array2table($array, $title="", $highlight="")
	{
		$table = "<table class=\"listResults\">";
		$caption = "<caption>$title</caption>";
		$thead = "<thead><tr><th></th><th>";
		$tr = "<tr>";
		foreach($array as $k => $v)
		{
			$td = "<td><span style=\"font-weight:bold\">$k</span></td>";
			$th = implode("</th><th>", array_keys($v));
			foreach($v as $key => $value)
			{
				$td .= "<td>$value</td>";
			}
			$tr = $tr . $td . "</tr>";
		}
		$thead = $thead . $th . "</th></tr></thead>";
		$table = $table . $caption . $thead . $tr . "</table>";
		return $table;
	}
}

class DashletTable extends Dashlet
{
	private $aEnumAttrs;

	public function __construct($oModelReflection, $sId)
	{
		parent::__construct($oModelReflection, $sId);
		$this->aProperties['oql'] = 'SELECT Server';
		$this->aProperties['class'] = 'Server';
		$this->aProperties['axisx'] = 'brand_name';
		$this->aProperties['axisy'] = 'location_name';
		$this->aCSSClasses[] = 'dashlet-block';
		$this->aEnumAttrs = array();
	}

	public function GetEnumAttr() {
		$aAttrDefs = MetaModel::ListAttributeDefs($this->aProperties['class']);
		foreach ($aAttrDefs as $attr => $def) {
			if (get_class($def) == "AttributeEnum") {
				$this->aEnumAttrs[] = $attr;
			}
		}
	}

	public function GetAttrTranslation($attr, $val) {
		if($val == "未定义") return $val;
		$sOriginClass = MetaModel::GetAttributeOrigin($this->aProperties['class'], $attr);
		if(in_array($attr, $this->aEnumAttrs)) {
			return Dict::S("Class:" . $sOriginClass . "/Attribute:" . $attr . "/Value:" . $val);
		} else {
			return $val;
		}
	}

	public function Render($oPage, $bEditMode = false, $aExtraParams = array())
	{
		$sOql = $this->aProperties['oql'];
		$sClass = $this->aProperties['class'];
		$sAxisx = $this->aProperties['axisx'];
		$sAxisy = $this->aProperties['axisy'];

		$this->GetEnumAttr();

		$iTopApi = new iTopClient();
		$sData = $iTopApi->coreGet($sClass, $sOql, "$sAxisx,$sAxisy");

		$aData = json_decode($sData, true);

		//$iCount = $aData['Found'];
		$aXCount = array();
		foreach($aData['objects'] as $k => $val) {
			$x = $val['fields'][$sAxisx];
			if($x == "") $x = "未定义";
			$x = $this->GetAttrTranslation($sAxisx, $x);
			array_push($aXCount, $x);
		}

		$aStatis = array();

		foreach($aData['objects'] as $k => $val) {
			$y = $val['fields'][$sAxisy];
			$x = $val['fields'][$sAxisx];
			if($y == "") $y = "未定义";
			if($x == "") $x = "未定义";

			$y = $this->GetAttrTranslation($sAxisy, $y);
			$x = $this->GetAttrTranslation($sAxisx, $x);
			if(!array_key_exists($y, $aStatis)) {
				$aStatis[$y] = array();
			}
			if(!array_key_exists($x, $aStatis[$y])) {
				$aStatis[$y][$x] = 0;
			}
			$aStatis[$y][$x]++;
		}

		// 数组需要按键名排序，否则生成的表格有误
		foreach($aStatis as $k => $val) {
			$aKeys = array_keys($val);
			$aDiff = array_diff($aXCount, $aKeys);
			if(count($aDiff) > 0) {
				foreach($aDiff as $v) {
					$aStatis[$k][$v] = 0;
				}
			}
			ksort($aStatis[$k]);
		}

		ksort($aStatis);
		
		$oTable = new CustomDashletTable();
		$sTable = $oTable->array2table($aStatis);

		$oPage->add('<div class="dashlet-content">');

		$sId = utils::GetSafeId('dashlet_table_'.($bEditMode? 'edit_' : '').$this->sId);
		$oPage->add($sTable);
        $oPage->add('</div>');
	}

	public function GetPropertiesFields(DesignerForm $oForm)
	{
		$oField = new DesignerLongTextField('oql', Dict::S('UI:DashletTable:Prop-OQL'), $this->aProperties['oql']);
		$oField->SetMandatory();
		$oForm->AddField($oField);

		$oField = new DesignerTextField('class', Dict::S('UI:DashletTable:Prop-Class'), $this->aProperties['class']);
		$oField->SetMandatory();
		$oForm->AddField($oField);
		
		$oField = new DesignerTextField('axisx', Dict::S('UI:DashletTable:Prop-Axis-X'), $this->aProperties['axisx']);
		$oField->SetMandatory();
		$oForm->AddField($oField);
		
		$oField = new DesignerTextField('axisy', Dict::S('UI:DashletTable:Prop-Axis-Y'), $this->aProperties['axisy']);
		$oField->SetMandatory();
		$oForm->AddField($oField);
	}

	static public function GetInfo()
	{
		return array(
				'label' => Dict::S('UI:DashletTable:Label'),
				'icon' => 'env-'.utils::GetCurrentEnvironment().'/custom-dashlets/images/table.png',
				'description' => Dict::S('UI:DashletTable:Description'),
		);
	}
}

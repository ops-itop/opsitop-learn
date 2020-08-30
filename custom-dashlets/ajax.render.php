<?php

require_once('../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/itopwebpage.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');
require_once(APPROOT.'/application/loginwebpage.class.inc.php');
LoginWebPage::DoLogin(false); // false，不需要管理员权限

class TableBlockAsync {
	private $aEnumAttrs;
	private $sClass;
	private $sOql;
	private $sAxisx;
	private $sAxisy;

	public function __construct($sClass = 'Server', $sOql = 'SELECT Server', $sAxisx = 'brand_name', $sAxisy = 'location_name')
	{
		$this->sClass = $sClass;
		$this->sOql = $sOql;
		$this->sAxisx = $sAxisx;
		$this->sAxisy = $sAxisy;
		
		$this->GetEnumAttr();
	}

	public function Array2Table($array, $title="", $highlight="")
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

	public function GetEnumAttr() {
		$aAttrDefs = MetaModel::ListAttributeDefs($this->sClass);
		foreach ($aAttrDefs as $attr => $def) {
			if (get_class($def) == "AttributeEnum") {
				$this->aEnumAttrs[] = $attr;
			}
		}
	}

	public function GetAttrTranslation($attr, $val) {
		if($val == "未定义") return $val;
		$sOriginClass = MetaModel::GetAttributeOrigin($this->sClass, $attr);
		if(in_array($attr, $this->aEnumAttrs)) {
			return Dict::S("Class:" . $sOriginClass . "/Attribute:" . $attr . "/Value:" . $val);
		} else {
			return $val;
		}
	}

	public function Render()
	{
		$oPage = new ajax_page("");
		$oPage->no_cache();
		$oPage->SetContentType('text/html');

		$sAxisx = $this->sAxisx;
		$sAxisy = $this->sAxisy;

		$iTopApi = new iTopClient();
		$sData = $iTopApi->coreGet($this->sClass, $this->sOql, "$sAxisx,$sAxisy");

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
		
		$sTable = $this->Array2Table($aStatis);

		$oPage->add($sTable);
		$oPage->output();
	}
}


$sClass = utils::ReadParam('class', 'Server');
$sOql = utils::ReadParam('oql', 'SELECT Server');
$sAxisx = utils::ReadParam('axisx', 'brand_name');
$sAxisy = utils::ReadParam('axisy', 'location_name');

$oTableDashlet = new TableBlockAsync($sClass, $sOql, $sAxisx, $sAxisy);
$oTableDashlet->Render();

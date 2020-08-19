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
	public function __construct($oModelReflection, $sId)
	{
		parent::__construct($oModelReflection, $sId);
		$this->aProperties['oql'] = 'SELECT Server';
		$this->aProperties['axisx'] = 'brand_name';
		$this->aProperties['axisy'] = 'location_name';
		$this->aCSSClasses[] = 'dashlet-block';
		
	}

	public function Render($oPage, $bEditMode = false, $aExtraParams = array())
	{
		$sOql = $this->aProperties['oql'];
		$sAxisx = $this->aProperties['axisx'];
		$sAxisy = $this->aProperties['axisy'];

		$iTopApi = new iTopClient();
		$sData = $iTopApi->coreGet('Server', $sOql, "$sAxisx,$sAxisy");
		$aData = json_decode($sData, true);

		$iCount = $aData['Found'];
		$aXCount = array();
		foreach($aData['objects'] as $k => $val) {
			$x = $val['fields'][$sAxisx];
			if($x == "") $x = "empty";
			array_push($aXCount, $x);
		}

		$aStatis = array();

		foreach($aData['objects'] as $k => $val) {
			$y = $val['fields'][$sAxisy];
			$x = $val['fields'][$sAxisx];
			if($y == "") $y = "empty";
			if($x == "") $x = "empty";
			if(!array_key_exists($y, $aStatis)) {
				$aStatis[$y] = array();
			}
			if(!array_key_exists($x, $aStatis[$y])) {
				$aStatis[$y][$x] = 0;
			}
			$aStatis[$y][$x]++;
		}

		foreach($aStatis as $k => $val) {
			$aKeys = array_keys($val);
			$aDiff = array_diff($aXCount, $aKeys);
			if(count($aDiff) > 0) {
				foreach($aDiff as $v) {
					$aStatis[$k][$v] = 0;
				}
			}
		}
		
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
		
		$oField = new DesignerIntegerField('axisx', Dict::S('UI:DashletTable:Prop-Axis-X'), $this->aProperties['axisx']);
		$oField->SetMandatory();
		$oForm->AddField($oField);
		
		$oField = new DesignerIntegerField('axisy', Dict::S('UI:DashletTable:Prop-Height'), $this->aProperties['axisy']);
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

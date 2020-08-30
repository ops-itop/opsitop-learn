<?php
class DashletTable extends Dashlet
{

	public function __construct($oModelReflection, $sId)
	{
		parent::__construct($oModelReflection, $sId);
		$this->aProperties['oql'] = 'SELECT Server';
		$this->aProperties['class'] = 'Server';
		$this->aProperties['axisx'] = 'brand_name';
		$this->aProperties['axisy'] = 'location_name';
		$this->aCSSClasses[] = 'dashlet-block';
	}


	public function Render($oPage, $bEditMode = false, $aExtraParams = array())
	{
		$sHtml = "";
		$aParams = array(
			'class' => $this->aProperties['class'],
			'oql' => $this->aProperties['oql'],
			'axisx' => $this->aProperties['axisx'],
			'axisy' => $this->aProperties['axisy']
		);
		
		$sParam = http_build_query($aParams);
		$sEnv = utils::GetCurrentEnvironment();
		$sRender = "env-" . $sEnv . "/custom-dashlets/ajax.render.php";
		$sUrl = rtrim(MetaModel::GetConfig()->Get('app_root_url'), "/") . "/" . $sRender;

 
		$oPage->add('<div class="dashlet-content">');
		$sId = utils::GetSafeId('dashlet_table_'.($bEditMode? 'edit_' : '').$this->sId);

		$sHtml .= "<div id=\"$sId\" class=\"display_block loading\">\n";
		$sHtml .= $oPage->GetP("<img src=\"../images/indicator_arrows.gif\"> ".Dict::S('UI:Loading'));
		$sHtml .= "</div>\n";

		$oPage->add($sHtml);

		if($bEditMode) {
			$oPage->add('<div class="dashlet-blocker"></div>');		
		}
        $oPage->add('</div>');

		$oPage->add_script('
			$.post("' . $sUrl . '?' . $sParam . '",
			   { operation: "ajax" },
			   function(data){
				 $("#'.$sId.'")
				    .empty()
				    .append(data)
				    .removeClass("loading")
                 ;
				}
			 );
		');
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

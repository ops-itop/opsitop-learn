<?php

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
if (!defined('APPROOT')) require_once(__DIR__.'/../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/itopwebpage.class.inc.php');
require_once(APPROOT.'/application/loginwebpage.class.inc.php');
require_once(APPROOT.'/application/startup.inc.php');

LoginWebPage::DoLogin(); // Check user rights and prompt if needed

function GetContacts($aContacts) {
	$aContactsTmp = array();
	foreach ($aContacts as $aContact) {
		$aContactsTmp[] = $aContact['lnkContactToFunctionalCI.contact_id_friendlyname'];
	}
	return implode(",", $aContactsTmp);
}

function GetNode($oObj) {
	$sClass = get_class($oObj);
	switch ($sClass) {
		case 'Organization':
			if($oObj->Get("parent_id") > 0) {
				$sParent = "Organization:" . $oObj->Get("parent_id");
			} else {
				$sParent = "#";
			}
			break;
		case 'BusinessProcess':
			$sParent = "Organization:" . $oObj->Get("org_id");
			break;
		case 'ApplicationSolution':
			$sParent = "BusinessProcess:" . $oObj->Get("businessprocess_id");
			break;
		default:
			$sParent = "#";
			break;
	}

	$aNode = array(
		"id" => $sClass . ":" . $oObj->GetKey(),
		"text" => $oObj->GetName(),
		"state" => array("opened"=>True),
		"parent" => $sParent,
		"a_attr" => array(
			"href" => utils::GetAbsoluteUrlAppRoot() . "pages/UI.php?operation=details&class=" . $sClass . "&id=" . $oObj->GetKey(),
			"tooltiptitle" => MetaModel::GetName($sClass) . "：" . $oObj->GetName()
		)
	);

	$sContent = '<table width="200px">';
	if($sClass != "Organization") {
		$sContent .= "<tr><td>负责人</td><td>" . GetContacts($oObj->Get("contacts_list")->ToDBObjectSet()->ToArrayOfValues()) . "</td></tr>";
	}
	$sContent .= "<tr><td>状态</td><td>" . $oObj->Get("status") . "</td></tr>";
	$sContent .= "<tr><td>编码</td><td>" . $oObj->Get("code") . "</td></tr></table>";
	$aNode['a_attr']['content'] = $sContent;
	if($sClass == "BusinessProcess") {
		$aNode['icon'] = "fa fa-list";
	}
	if($sClass == "ApplicationSolution") {
		$aNode['icon'] = "fa fa-cog";
	}
	return $aNode;
}
function GetAppTree($sOrg = NULL) {
	$aTree = array();
	foreach(array("Organization", "BusinessProcess", "ApplicationSolution") as $sClass) {
		$sOQL = "SELECT " . $sClass;
		if($sOrg) {
			if($sClass == "Organization") {
				$sOQL .= " WHERE id=" . $sOrg;
			} else {
				$sOQL .= " WHERE org_id=" . $sOrg;
			}
		}

		$oSearch = DBObjectSearch::FromOQL_AllData($sOQL);
		$oSet = new DBObjectSet($oSearch, array(), array());
	
		while ($oObj = $oSet->Fetch()) {
			$aTree[] = GetNode($oObj);
		}		
	}
	return $aTree;
}

$sOrg = utils::ReadParam('org', '', false, 'raw_data');

$oP = new ajax_page('');
$oP->SetContentType('application/json');
$oP->add(json_encode(GetAppTree($sOrg)));
$oP->output();
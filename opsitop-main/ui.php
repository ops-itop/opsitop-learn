<?php

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
if (!defined('APPROOT')) require_once(__DIR__.'/../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/itopwebpage.class.inc.php');
require_once(APPROOT.'/application/loginwebpage.class.inc.php');
require_once(APPROOT.'/application/startup.inc.php');
LoginWebPage::DoLogin(); // Check user rights and prompt if needed

$oP = new iTopWebPage(Dict::S('UI:AppTree:Title'));
$oP->set_base(utils::GetAbsoluteUrlAppRoot().'pages/');
$oP->SetBreadCrumbEntry('ui-tool-org', Dict::S('Menu:AppTree'), Dict::S('Menu:AppTree+'), '', utils::GetAbsoluteUrlAppRoot().'images/wrench.png');

$oP->add_linked_stylesheet(utils::GetAbsoluteUrlModulesRoot() . "opsitop-main/libs/jstree/themes/default/style.min.css");
$oP->add_linked_script(utils::GetAbsoluteUrlModulesRoot() . "opsitop-main/libs/jstree/jstree.min.js");

$oP->add('<h1>' . Dict::S('UI:AppTree:Title') . '</h1>');
$oP->add('<div id="app-jstree" class="demo"></div>');

$oP->add_ready_script('
$("#app-jstree").jstree({
	"core" :{
		"data" : {
			"url" : "' . utils::GetAbsoluteUrlModulesRoot() . 'opsitop-main/ajax.render.php",
			"dataType" : "json"
		}
	}
}).bind(
	"select_node.jstree", function(e, data) {window.open(data.node.a_attr.href);}
).bind(
	"ready.jstree", function() {
		$(".jstree-anchor").each(function(){
			$(this).qtip({
				content : {
					text: $(this).attr("content"),
					title: $(this).attr("tooltiptitle")
				}
			});
		});
	}
);
');

$oP->output();
<?php

// PHP Data Model definition file

// WARNING - WARNING - WARNING
// DO NOT EDIT THIS FILE (unless you know what you are doing)
//
// If you use supply a datamodel.xxxx.xml file with your module
// the this file WILL BE overwritten by the compilation of the
// module (during the setup) if the datamodel.xxxx.xml file
// contains the definition of new classes or menus.
//
// The recommended way to define new classes (for iTop 2.0) is via the XML definition.
// This file remains in the module's template only for the cases where there is:
// - either no new class or menu defined in the XML file
// - or no XML file at all supplied by the module

class OpsPasswordExpiration extends AbstractLoginFSMExtension {
	public function ListSupportedLoginModes() { return ['form']; }

	protected function OnUsersOK(&$iErrorCode) {
		$oUser = UserRights::GetUserObject();
		if($oUser->Get('expiration') == 'force_expire') {
			$this->DisplayChangePwdForm();

			$sNewPwd = utils::ReadPostParam('new_pwd', '', 'raw_data');
			$oUser->Set('password', $sNewPwd);
			$oUser->Set('expiration', 'can_expire');
			$oUser->DBUpdate();
		}
		return LoginWebPage::LOGIN_FSM_CONTINUE;
	}

	public function DisplayChangePwdForm($bFailedLogin = false, $sIssue = null) {
		$oTwigContext = new LoginTwigRenderer();
		$aVars = $oTwigContext->GetDefaultVars();
		$aVars['bFailedLogin'] = $bFailedLogin;
		$aVars['sIssue'] = $sIssue;
		$oTwigContext->Render($this, 'changepwdform.html.twig', $aVars);
	}
}

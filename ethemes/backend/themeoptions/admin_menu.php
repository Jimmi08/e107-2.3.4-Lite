<?php
if (!defined('e107_INIT'))
{
	exit;
}

require_once("../../../class2.php");


e107::themeLan('admin', 'backend', true);

$code = '$(document).ready(function(){
	   $("#etrigger-submit").html("Save Settings");
	   $("h4.caption").html("' . 'Dashboard Colors' . '");
	});';

e107::js('inline', $code);

class leftmenu_adminArea extends e_admin_dispatcher
{

	protected $modes = array(

		'custom_css'	=> array(
			'controller' 	=> 'themeoptions_ui',
			'path' 			=> null,
			'ui' 			=> 'themeoptions_form_ui',
			'uipath' 		=> null
		),
		'dashboard'	=> array(
			'controller' 	=> 'themeoptions_ui',
			'path' 			=> null,
			'ui' 			=> 'themeoptions_form_ui',
			'uipath' 		=> null
		),
		'main'	=> array(
			'controller' 	=> 'adminconfig_ui',
			'path' 			=> null,
			'ui' 			=> 'adminconfig_form_ui',
			'uipath' 		=> null
		)

	);
	protected $adminMenu = array(
		/*	'main/prefs'			=> array('caption'=> LAN_PREFS, 'perm' => '0', 'url'=>'admin_config.php'),       */
	
		'dashboard/prefs'	    => array('caption' => 'Dashboard Colors', 'perm' => '0', 'link' => 'admin_dashboard.php', 'action' => 'edit'),
		'custom_css/edit'	    => array('caption' => LAN_JM_THEMEOPTIONS_02, 'perm' => '0', 'url' => 'admin_custom_css.php', 'action' => 'edit'),
		
		/*	'teammemberclass/edit'	    => array('caption'=> LAN_JM_THEMEOPTIONS_04, 'perm' => '0', 'url'=>'admin_teammembers.php', 'action' => 'edit'),	*/
		'main/main'	    => array(
			'caption' => 'Back to Theme Manager', 'perm' => '0',
			'link' => e_ADMIN . 'theme.php?mode=main&action=admin' 
		),
	);

	protected $adminMenuAliases = array();

	protected $menuTitle = LAN_JM_THEMEOPTIONS;
}

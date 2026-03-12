<?php

if (!defined('e107_INIT')) { exit; }

e107::lan('theme', 'admin',true);

// Theme Configuration File.
class theme_config implements e_theme_config
{

	var $sitetheme;
	var $helpLinks = array();

	public function __construct()
	{
	 
	}
	public function config()
	{
	}

	public function help()
	{

		$themeoptions['custom_css'] = e_THEME . THEME . "themeoptions/admin_dashboard.php";

		$buttons = e107::getNav()->renderAdminButton($themeoptions['custom_css'], "<b>" . LAN_JM_THEMEOPTIONS_01 . "</b><br>", LAN_JM_THEMEOPTIONS_01_HELP, "P", '<i class="S32 e-themes-32"></i>', "div");

		//$ns->setStyle('flexpanel');
		$mainPanel = '<div class="panel panel-default" >
					  <div class="panel-heading ui-sortable-handle">
					    <h3 class="panel-title">' . LAN_CONFIGURE . '</h3>
					  </div>
					  <div class="panel-body">
					  ';

		$mainPanel .= " <style>a.core-mainpanel-link-icon { height: 100px; }</style>";
		$mainPanel .= " ";
		$mainPanel .= $buttons;
		$mainPanel .= "</div>";

		return $mainPanel;
 
	}
}
 
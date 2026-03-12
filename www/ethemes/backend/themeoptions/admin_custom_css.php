<?php

// Generated e107 Plugin Admin Area

define('e_ADMIN_AREA', true);

require_once('../../../class2.php');
if (!getperms('P'))
{
	e107::redirect('admin');
	exit;
}

$sitetheme = e107::getPref('sitetheme');

e107::lan('theme', 'admin', true);

e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/lib/codemirror.js', 'jquery');
e107::css('url', e_THEME . $sitetheme . '/themeoptions/js/codemirror/lib/codemirror.css');
e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/mode/javascript/javascript.js', 'jquery');
e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/mode/css/css.js', 'jquery');
e107::css('url', e_THEME . $sitetheme . '/themeoptions/js/codemirror/theme/xq-light.css');



e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/addon/hint/show-hint.js', 'jquery');
e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/addon/hint/css-hint.js', 'jquery');

e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/addon/lint/css-lint.js', 'jquery');
e107::css('url', e_THEME . $sitetheme . '/themeoptions/js/codemirror/addon/lint/lint.css');

e107::js('footer', e_THEME . $sitetheme . '/themeoptions/js/codemirror/addon/edit/matchbrackets.js', 'jquery');

// mode: "text/html", edit/matchtags.js
$inline_code = ' 
    const codemirrorEditor = CodeMirror.fromTextArea(document.getElementById("custom-css"), {
	lineNumbers: true,
	mode: "text/css",
	theme: "xq-light",
	gutters: ["CodeMirror-lint-markers"],
	lint: false,
	indentUnit: 4,
	matchBrackets: true,
	autoCloseBrackets: true,
	csslint: {"errors":true,"box-model":true,"display-property-grouping":true,"duplicate-properties":true,"known-properties":true,"outline-none":true}
  }); ';

e107::js('footer-inline', $inline_code);

$inline_css = '
    textarea, pre {
	box-shadow: none!important;
	transition: none!important;
 
}';

e107::css('inline', $inline_css);

//it is done this way because multiple prefs action 
require_once("admin_menu.php");

class themeoptions_adminArea extends leftmenu_adminArea
{
	function init()
	{
		$this->defaultMode = 'custom_css';
	 
	}
}

$code = '$(document).ready(function(){ 
	   $("#etrigger-submit").html("Save Settings");
	   $("h4.caption").html("' . LAN_JM_THEMEOPTIONS_02 . '");
	});';

e107::js('inline', $code);

class themeoptions_ui extends e_admin_ui
{
 

	protected $table = NULL;
	protected $fieldpref = array();
	protected $prefs = array();


	protected $fields = array(
		'pref_custom_css' => array(
			'title' => "<b>" . LAN_JM_THEMEOPTIONS_02_01 . '</b><br /><small>' . LAN_JM_THEMEOPTIONS_02_01_HELP . '</small>',
			'tab' => 0,
			'type' => 'method',
			'data' => false,
			'help' => '',
			'writeParms' => array('size' => 'block-level')
		),

	);

	protected $afterSubmitOptions = array('edit');

	public function init()
	{
		$this->setDefaultAction('edit');
	}



	public function beforeCreate($new_data, $old_data)
	{
		$this->beforeUpdate($new_data, $old_data, NULL);
	}

	// ------- Customize Update --------

	public function beforeUpdate($new_data, $old_data, $id)
	{
		$pref = e107::getThemeConfig();
		$theme_pref = array();
		$theme_pref['custom_css'] = $new_data['custom_css'];

		$pref->setPref($theme_pref)->save(true, true, false);

		$new_data['custom_css'] = '';
		return false;
	}
}

class themeoptions_form_ui extends e_admin_form_ui
{

	function pref_custom_css($curVal, $mode)
	{

		$custom_theme_prefs = e107::pref('theme');

		switch ($mode)
		{
			case 'read': // Edit Page
				$text = "Are you cheating?";
				return $text;
				break;

			case 'write': // Edit Page
				$name = 'custom_css';
				$value = $custom_theme_prefs[$name];
				$attributes = array('type' => 'textarea', 'data' => 'str', 'width' => 'auto');
				$text = $this->renderElement($name, $value, $attributes);
				return $text;
				break;
		}
	}
}

require_once "admin_menu.php";

new themeoptions_adminArea();

require_once e_ADMIN . "auth.php";
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;

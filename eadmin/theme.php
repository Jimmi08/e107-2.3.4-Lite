<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * blankd under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * e107 blank Plugin
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/blank/admin_config.php,v $
 * $Revision$
 * $Date$
 * $Author$
 *
*/

require_once(__DIR__.'/../class2.php');

if (!getperms("1|TMP"))
{
	e107::redirect('admin');
	exit;
}

e107::coreLan('theme', true);

if(!empty($_GET['iframe']))
{
	define('e_IFRAME', true);
}


//e107::js('core','bootstrap-suggest/dist/bootstrap-suggest.min.js');
//e107::css('core','bootstrap-suggest/dist/bootstrap-suggest.css');
//e107::js('core','bootstrap-suggest/bootstrap-suggest.js');
//e107::css('core','bootstrap-suggest/bootstrap-suggest.css');
e107::library('load', 'bootstrap-suggest');
/*
e107::js('footer-inline', "
$('textarea').suggest(':', {
  data: function(q, lookup) {
 
      $.getJSON('theme.php', {q : q }, function(data) {
			console.log(data);
			console.log(lookup);
			lookup.call(data);
      });

      // we aren't returning any

  }
  
});


");*/


e107::js('footer-inline', "

$('textarea.input-custompages').suggest(':', {
	
  data: function() {
  
  var i = $.ajax({
		type: 'GET',
		url: 'theme.php',
		async: false,
		data: {
			action: 'route'
		}
		}).done(function(data) {
		//	console.log(data);
			return data; 
		}).responseText;		
    	
	try
	{
		var d = $.parseJSON(i);
	} 
	catch(e)
	{
		// Not JSON.
		return;
	}
	
	return d;   
  },
  filter: {
  	casesensitive: false,
  	limit: 300
	},
	endKey: \"\\n\",
  map: function(item) {
    return {
      value: item.value,
      text: item.value
    }
  }
})

");



class theme_admin extends e_admin_dispatcher
{
	/**
	 * Format: 'MODE' => array('controller' =>'CONTROLLER_CLASS'[, 'index' => 'list', 'path' => 'CONTROLLER SCRIPT PATH', 'ui' => 'UI CLASS NAME child of e_admin_ui', 'uipath' => 'UI SCRIPT PATH']);
	 * Note - default mode/action is autodetected in this order:
	 * - $defaultMode/$defaultAction (owned by dispatcher - see below)
	 * - $adminMenu (first key if admin menu array is not empty)
	 * - $modes (first key == mode, corresponding 'index' key == action)
	 * @var array
	 */
	protected $modes = array(
		'main'		=> array(
						'controller' => 'theme_admin_ui',
						'path' 		=> null,
						'ui' 		=> 'theme_admin_form_ui',
						'uipath' => null
		 ),
		// 'convert'		=> array(
		// 				'controller' => 'theme_builder',
		// 				'path' 		=> null,
		// 				'ui' 		=> 'theme_admin_form_ui',
		// 				'uipath' => null
		// ),
	);


	protected $adminMenu = array(
		'main/main'			=> array('caption'=> TPVLAN_33, 'perm' => '0|1|TMP', 'icon'=>'fas-home'),
		'main/admin' 		=> array('caption'=> TPVLAN_34, 'perm' => '0', 'icon'=>'fas-tachometer-alt'),
		'main/choose' 		=> array('caption'=> TPVLAN_51, 'perm' => '0', 'icon'=>'fas-exchange-alt'),
	//	'main/online'		=> array('caption'=> TPVLAN_62, 'perm' => '0', 'icon'=>'fas-search'),
	//	'main/upload'		=> array('caption'=> TPVLAN_38, 'perm' => '0'),
	//	'convert/main'		=> array('caption'=> ADLAN_CL_6, 'perm' => '0', 'icon'=>'fas-toolbox')
	);


	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'
	);

	protected $adminMenuIcon = 'e-themes-24';

	protected $menuTitle = TPVLAN_26;

	function init()
	{

		if((e_AJAX_REQUEST) && varset($_GET['action']) === 'route')
		{
			$newRoutes = $this->getAllRoutes();
			echo json_encode($newRoutes);
			exit;
		}

	}



	function handleAjax()
	{
		if(empty($_GET['action']))
		{
			return null;
		}


		require_once(e_HANDLER."theme_handler.php");
		$themec = new themeHandler;

		switch ($_GET['action'])
		{
			case 'login':
				$mp = $themec->getMarketplace();
				echo $mp->renderLoginForm();
				exit;
			break;

				/*
				case 'download':
					$string =  base64_decode($_GET['src']);
					parse_str($string, $p);
					$mp = $themec->getMarketplace();
					$mp->generateAuthKey($e107SiteUsername, $e107SiteUserpass);
					// Server flush useless. It's ajax ready state 4, we can't flush (sadly) before that (at least not for all browsers)
					echo "<pre>Connecting...\n"; flush();
					// download and flush
					$mp->download($p['id'], $p['mode'], $p['type']);
					echo "</pre>"; flush();
					exit;
				break;
				*/

				case 'info':
					if(!empty($_GET['src']))
					{
						$string =  base64_decode($_GET['src']);
						parse_str($string,$p);
						$themeInfo = e107::getSession()->get('thememanager/online/'.intval($p['id']));
						echo $themec->renderThemeInfo($themeInfo);
					}
				break;

				case 'preview':
					// Theme Info Ajax
					$tm = (string) $_GET['id'];
					$data = e107::getTheme($tm)->get(); // $themec->getThemeInfo($tm);
					echo $themec->renderThemeInfo($data);
				//	exit;
				break;

			}
		/*
			if(vartrue($_GET['src'])) // Process Theme Download.
			{
				$string =  base64_decode($_GET['src']);
				parse_str($string,$p);

				if(vartrue($_GET['info']))
				{
					echo $themec->renderThemeInfo($p);
				//	print_a($p);
					exit;
				}

				$remotefile = $p['url'];

				e107::getFile()->download($remotefile,'theme');
				exit;

			}
		*/
			// Theme Info Ajax
			// FIXME  addd action=preview to the url, remove this block
			if(!empty($_GET['id']))
			{
				$tm = (string) $_GET['id'];
				$data = e107::getTheme($tm)->get(); // $themec->getThemeInfo($tm);
				echo $themec->renderThemeInfo($data);
			}

			require_once(e_ADMIN."footer.php");
			exit;

		}

	/**
	 * @return array
	 */
	private function getAllRoutes()
	{

		$legacy = array(
			'gallery/index/category',
			'gallery/index/list',
			'news/list/items',
			'news/list/category',
			'news/list/all',
			'news/list/short',
			'news/list/day',
			'news/list/month',
			'news/list/tag',
			'news/list/author',
			'news/view/item',
			'page/chapter/index',
			'page/book/index',
			'page/view/index',
			'page/view/other',
			'page/list/index',
			'search/index/index',
			'system/error/notfound',
			'user/myprofile/view',
			'user/myprofile/edit',
			'user/profile/list',
			'user/profile/view',
			'user/login/index',
			'user/register/index'
		);


		$newRoutes = e107::getUrlConfig('route');

		foreach($legacy as $v)
		{
			$newRoutes[$v] = $v;
		}

		ksort($newRoutes);

		$ret = [];
		foreach($newRoutes as $k => $v)
		{
			$ret[] = array('value' => $k, 'label' => $k);
		}

		return $ret;
	}


}



class theme_admin_ui extends e_admin_ui
{
		// required
		protected $pluginTitle      = TPVLAN_26;
		protected $pluginName       = 'core';
		protected $table            = false;
		protected $listQry          = false;
		protected $pid              = "id";
		protected $perPage          = 10;
		protected $batchDelete      = false;

	//	protected \$sortField		= 'somefield_order';
	//	protected \$sortParent      = 'somefield_parent';
	//	protected \$treePrefix      = 'somefield_title';
		protected $grid             = array('price'=>'price', 'version'=>'version','title'=>'name', 'image'=>'thumbnail',  'body'=>'',  'class'=>'col-md-2 col-sm-3', 'perPage'=>12, 'carousel'=>true, 'toggleButton'=>false);


    	protected  $fields = array(
			'checkboxes'				=> array('title'=> '', 					'type' => null,			'data' => null,			'width'=>'5%', 		'thclass' =>'center', 'forced'=> TRUE,  'class'=>'center', 'toggle' => 'e-multiselect'),
			'id'					    => array('title'=> LAN_ID, 				'type' => 'number',		'data' => 'int',		'width'=>'5%',		'thclass' => '',  'class'=>'center',	'forced'=> TRUE, 'primary'=>TRUE/*, 'noedit'=>TRUE*/), //Primary ID is not editable
           	'name'				        => array('title'=> LAN_TITLE, 			'type' => 'text',		'data' => 'str',		'width'=>'5%',		'thclass' => '',	'forced'=> TRUE, 'primary'=>TRUE/*, 'noedit'=>TRUE*/), //Primary ID is not editable
            'thumbnail'	   			    => array('title'=> LAN_IMAGE, 			'type' => 'image',      'readParms'=>array('thumb'=>1,'w'=>300,'h'=>169,'crop'=>1, 'link'=>false, 'fallback'=>'{e_IMAGE}admin_images/nopreview.png'),	'data' => 'str',		'width'=>'auto',	'thclass' => '', 'batch' => TRUE, 'filter'=>TRUE),
			'folder' 				    => array('title'=> 'Folder', 			'type' => 'text', 		'data' => 'str',		'width' => 'auto',	'thclass' => ''),
			'category' 				    => array('title'=> LAN_CATEGORY, 		'type' => 'dropdown', 		'data' => 'str', 'filter'=>true,		'width' => 'auto',	'thclass' => '', 'writeParms'=>array()),
			'version' 			        => array('title'=> 'Version',			'type' => 'text', 		'data' => 'str',		'width' => 'auto',	'thclass' => ''),
			'price' 				    => array('title'=> LAN_AUTHOR,			'type' => 'method', 		'data' => 'str',		'width' => 'auto',	'thclass' => 'left'),
     //    	'blank_authorURL' 			=> array('title'=> "Url", 				'type' => 'url', 		'data' => 'str',		'width' => 'auto',	'thclass' => 'left'),
     //       'blank_date' 				=> array('title'=> LAN_DATE, 			'type' => 'datestamp', 	'data' => 'int',		'width' => 'auto',	'thclass' => '', 'readParms' => 'long', 'writeParms' => 'type=datetime'),
	//		'blank_compatibility' 		=> array('title'=> 'Compatible',			'type' => 'text', 		'data' => 'str',		'width' => '10%',	'thclass' => 'center' ),
		//	'blank_url' 				=> array('title'=> LAN_URL,		'type' => 'file', 		'data' => 'str',		'width' => '20%',	'thclass' => 'center',	'batch' => TRUE, 'filter'=>TRUE, 'parms' => 'truncate=30', 'validate' => false, 'help' => 'Enter blank URL here', 'error' => 'please, ener valid URL'),
	//		'test_list_1'				=> array('title'=> 'test 1',			'type' => 'boolean', 		'data' => 'int',		'width' => '5%',	'thclass' => 'center',	'batch' => TRUE, 'filter'=>TRUE, 'noedit' => true),
			'options' 					=> array('title'=> LAN_OPTIONS, 		'type' => 'method', 		'data' => null,			'width' => '10%',	'thclass' => 'center last', 'class' => 'center last', 'forced'=>TRUE)
		);

		//required - default column user prefs
	//	protected $fieldpref = array('checkboxes', 'blank_id', 'blank_type', 'blank_url', 'blank_compatibility', 'options');

		// optional, if $pluginName == 'core', core prefs will be used, else e107::getPluginConfig($pluginName);
		protected $prefs = array();

		protected $themeObj;

		public function __construct($request,$response,$params=array())
		{
			require_once(e_HANDLER."theme_handler.php");
			$this->themeObj = new themeHandler; // handles POSTed data.
			$this->fields['category']['writeParms']['optArray'] = e107::getTheme()->getCategoryList(); // array('plugin_category_0','plugin_category_1', 'plugin_category_2'); // Example Drop-down array.

			parent::__construct($request,$response,$params);
		}

		// optional
		public function init()
		{

			e107::css('inline', '


				.admin-ui-grid .price {
				position: absolute;
			/*	bottom: 68px;*/
				top:0;
				right: 18px;
				}

				.overlay-title { padding-bottom:7px }

			');




			$this->themeObj ->postObserver();

				$this->grid['template'] = '

				 <div class="panel panel-primary">
					<div class="e-overlay" >{IMAGE}
						<div class="e-overlay-content">
						<div class="overlay-title">{TITLE} v{VERSION}</div>
						{OPTIONS}
						</div>
					</div>
					<div class="panel-footer"><small>{TITLE}</small>{PRICE}</div>
				</div>


				';
		}

		public function _setTreeModel()
		{
			if($this->getAction() === 'online')
			{
				//$this->_tree_model = new theme_admin_online_tree_model;
			}
			else
			{
				$this->_tree_model = new theme_admin_tree_model;// new theme_model_admin_tree();
			}

			return $this;
		}

		public function ChooseObserver() // action = choose
		{
			$mes = e107::getMessage();
			$tp = e107::getParser();

			if(!empty($_POST['selectmain']))
			{
				$id = key($_POST['selectmain']);
				$message = $tp->lanVars(TPVLAN_94,$id);

				if($this->themeObj->setTheme($id))
				{
					$mes->addSuccess($message);

					// clear infopanel in admin dashboard.
					e107::getCache()->clear('Infopanel_theme', true);
					e107::getSession()->clear('addons-update-status');
					e107::getSession()->set('addons-update-checked',false); // set to recheck it.
				}
				else
				{
					$mes->addError($message);
				}

				$this->redirectAction('main');
			}

			if(!empty($_POST['selectadmin']))
			{
				$id = key($_POST['selectadmin']);
				$this->setAdminTheme($id);
				$this->redirectAction('admin');
			}



			$param = array();
			$this->perPage = 0;
			$param['limitFrom'] = (int) $this->getQuery('from', 0);
			$param['limitTo']   = 0 ; // (int) $this->getPerPage();
			$param['searchqry'] = $this->getQuery('searchquery', '');

			$this->getTreeModel()->setParams($param)->loadBatch(); // load the tree model above from the class below.
		}

		private	function setAdminTheme($folder)
		{

		//	$adminCSS = file_exists(e_THEME.$pref['admintheme'].'/admin_dark.css') ? 'admin_dark.css' : 'admin_light.css';

			$cfg = e107::getConfig();
			$cfg->set('admintheme',$folder);
		//	$cfg->set('admincss',$adminCSS);  //todo get the default from theme.xml
			$cfg->save(true,true,true);

			e107::getCache()->clear_sys();

	/*		if(save_prefs())
			{
				// Default Message
				$mes->add(TPVLAN_40." <b>'".$themeArray[$this->id]."'</b>", E_MESSAGE_SUCCESS);
				$this->theme_adminlog('02', $pref['admintheme'].', '.$pref['admincss']);
			}*/

			//	$ns->tablerender("Admin Message", "<br /><div style='text-align:center;'>".TPVLAN_40." <b>'".$themeArray[$this -> id]."'</b>.</div><br />");
			//  $this->showThemes('admin');
		}

		public function OnlineObserver()
		{
			unset($this->fields['checkboxes']);
			$this->perPage = 500;

		}
		
		public function ChooseAjaxObserver()
		{
			$this->ChooseObserver();
		}

		public function MainPage()
		{
			if(empty($_POST) && deftrue('e_DEVELOPER') || deftrue('e_DEBUG')) // check for new theme media and import.
			{
				$name = e107::getPref('sitetheme');
				e107::getMedia()->import('_common_image', e_THEME.$name, '', array('min-size'=>10000));
				e107::getMessage()->addInfo('Developer/Debug Mode: Scanning theme images folder for new media to import.');
			}

			$message = e107::getMessage()->render();
			return $message.$this->renderThemeConfig('front');
		}

		public function AdminPage()
		{
			return $this->renderThemeConfig('admin');
		}

		private function search($name, $searchVal, $submitName, $filterName='', $filterArray=false, $filterVal=false)
		{
			$frm = e107::getForm();

			return $frm->search($name, $searchVal, $submitName, $filterName, $filterArray, $filterVal);

		}
 
		public function InfoPage()
		{
			if(!empty($_GET['src'])) // online mode.
			{
				$string =  base64_decode($_GET['src']);
				parse_str($string,$p);
				$themeInfo = e107::getSession()->get('thememanager/online/'.intval($p['id']));
				return $this->themeObj->renderThemeInfo($themeInfo);
			}


			if(empty($_GET['id']))
			{
				echo "invalid URL";
				return null;
			}

			$tm = (string) $this->getId();
			$themeMeta  = e107::getTheme($tm)->get();
			echo $this->themeObj->renderThemeInfo($themeMeta);

		}

		public function DownloadPage()
		{
			if(empty($_GET['e-token']))
			{
				return e107::getMessage()->addError('Invalid Token')->render('default', 'error');
			}


			$frm = e107::getForm();
			$mes = e107::getMessage();
			$string =  base64_decode($_GET['src']);
			parse_str($string, $data);

			if(!empty($data['price']))
			{
				e107::getRedirect()->go($data['url']);
				return true;
			}

			if(deftrue('e_DEBUG_MARKETPLACE'))
			{
				echo "<b>DEBUG MODE ACTIVE (no downloading)</b><br />";
				echo '$_GET: ';
				print_a($_GET);

				echo 'base64 decoded and parsed as $data:';
				print_a($data);
				return false;
			}

			require_once(e_HANDLER.'e_marketplace.php');

			$mp 	= new e_marketplace(); // autodetect the best method

		    $mes->addSuccess(TPVLAN_85);

			if($mp->download($data['id'], $data['mode'], 'theme')) // download and unzip theme.
			{
				// Auto install?
			//	$text = e107::getPlugin()->install($data['plugin_folder']);
			//	$mes->addInfo($text);

				e107::getTheme()->clearCache();
				return $mes->render('default', 'success');
			}
			else
			{
				return $mes->addError('Unable to continue')->render('default', 'error');
			}

			/*echo $mes->render('default', 'debug');
				echo "download page";*/


		}

		// public function UploadPage()
		// {

		// 	$frm = e107::getForm();

		// 	if(!is_writable(e_THEME))
		// 	{
		// 		return e107::getMessage()->addWarning(TPVLAN_15)->render();
		// 	}
		// 	else
		// 	{
		// 		require_once(e_HANDLER.'upload_handler.php');
		// 		$max_file_size = get_user_max_upload();

		// 		$text = "
		// 		<form enctype='multipart/form-data' action='".e_SELF."' method='post'>
		// 			<table class='table adminform'>
		// 				<colgroup>
		// 					<col class='col-label' />
		// 					<col class='col-control' />
		// 				</colgroup>
		// 			<tr>
		// 				<td>".TPVLAN_13."</td>
		// 				<td>
		// 					<input type='hidden' name='MAX_FILE_SIZE' value='{$max_file_size}' />
		// 					<input type='hidden' name='ac' value='".md5(ADMINPWCHANGE)."' />
		// 					<input class='tbox' type='file' name='file_userfile[]' size='50' />
		// 				</td>
		// 			</tr>
	    //             <tr>
		// 				<td>".TPVLAN_10."</td>
		// 				<td><input type='checkbox' name='setUploadTheme' value='1' /></td>
		// 			</tr>
		// 			</table>

		// 		<div class='buttons-bar center'>".$frm->admin_button('upload', 1, 'submit', LAN_UPLOAD)."</div>
		// 		</form>
		// 		";
		// 	}

		// 	return $text;

		// }


		/**
		 * Check theme.php code for methods incompatible with PHP7.
		 * @param $code
		 * @return bool
		 */
		private function containsErrors($code)
		{
			if(PHP_MAJOR_VERSION < 6)
			{
				return false;
			}

			$dep = array('call_user_method(', 'call_user_method_array(', 'define_syslog_variables', 'ereg(','ereg_replace(',
			'eregi(', 'eregi_replace(', 'set_magic_quotes_runtime(', 'magic_quotes_runtime(', 'session_register(', 'session_unregister(', 'session_is_registered(',
			'set_socket_blocking(', 'split(', 'spliti(', 'sql_regcase(', 'mysql_db_query(', 'mysql_escape_string(');

			foreach($dep as $test)
			{
				if(strpos($code, $test) !== false)
				{
					e107::getMessage()->addDebug("Incompatible function <b>".rtrim($test,"(")."</b> found in theme.php");
					return true;
				}

			}

			return false;

		}




		private function renderThemeConfig($type = 'front')
		{
			$frm = e107::getForm();
			$themeMeta  = e107::getTheme($type)->get();

			$themeFileContent = file_get_contents(e_THEME.$themeMeta['path']."/theme.php");


			if($this->containsErrors($themeFileContent))
			{
				e107::getMessage()->setTitle("Incompatibility Detected", E_MESSAGE_ERROR)->addError("This theme is not compatible with your version of PHP.");
			}

			$this->addTitle("<span class='text-warning'>".$themeMeta['name']."</span>");

			$mode = ($type == 'front') ? 1 : 2;

			$text = $frm->open($type.'-form', 'post');
			$text .= $this->themeObj->renderTheme($mode, $themeMeta);
			$text .= $frm->close();

			return $text;
		}


		public function ChoosePage()
		{
			e107::getTheme('front', true); // clear cache and reload from disk.
			return $this->GridPage();
		}

		public function ChooseAjaxPage()
		{
			return $this->GridAjaxPage();
		}

		// public function OnlinePage()
		// {

		// 	if(!function_exists('curl_init'))
		// 	{
		// 		e107::getMessage()->addWarning(TPVLAN_79);
		// 	}

		// //	$this->setThemeData();

		// 	return $this->GridPage();
		// }

		public function OnlineAjaxPage()
		{
			unset($this->fields['checkboxes']);
			$this->perPage = 500;
		//	$this->setThemeData();
			return $this->GridAjaxPage();
		}
 

		public function renderHelp()
		{

			$tp = e107::getParser();

			$type= $this->getMode()."/".$this->getAction();

			switch($type)
			{
				case "main/main":
					$text = '<b>'.TPVLAN_56.'</b><br />'; // Visbility Filter
					$text .= '<br />'.$tp->toHTML(TPVLANHELP_03,true);
					$text .= '<ul style="padding-left:10px;margin-top:10px">
						<li>'.$tp->toHTML(TPVLANHELP_06,true).'</li>
						<li>'.$tp->toHTML(TPVLANHELP_04,true).'</li>
						<li>'.$tp->toHTML(TPVLANHELP_05,true).'</li>
						</ul>';

					break;

				case "label2":
					//  code
					break;

				default:
					$text = TPVLANHELP_01.'<br /><br />'.TPVLANHELP_02;
			}




			return array('caption'=>LAN_HELP, 'text'=>$text);




		}


}


class theme_admin_tree_model extends e_tree_model
{

	/**
	 * Load data from theme meta file.
	 * @param bool $force
	 */
	function loadBatch($force=false)
	{
		$themeList  = e107::getTheme()->getList();
		$newArray   = array();
		$parms      = $this->getParams();
		$siteTheme  = e107::getPref('sitetheme');

		if($parms['limitFrom'] == 0 && empty($parms['searchqry'])) // place the sitetheme first.
		{

			$newArray[] = $themeList[$siteTheme];
		}

		foreach($themeList as $k=>$v)
		{

			if(!empty($parms['searchqry']) && stripos($v['info'],$parms['searchqry']) === false && stripos($v['folder'],$parms['searchqry']) === false && stripos($v['name'],$parms['searchqry']) === false)
			{
				continue;
			}

			if($v['path'] == $siteTheme)
			{
				continue;
			}

			$newArray[] = $v;
		}

		if(!empty($parms['limitTo']) && empty($parms['searchqry']))
		{
			$arr = array_slice($newArray, $parms['limitFrom'], $parms['limitTo']);
		}
		else
		{
			$arr = $newArray;
		}


		foreach($arr as $k=>$v)
		{

			$v['id'] = $k;

			$v['thumbnail'] = !empty($v['thumbnail']) ? '{e_THEME}'.$v['path'].'/'.$v['thumbnail'] : null;
			$tmp = new e_model($v);
			$this->setNode($k,$tmp);

		}

		$this->setTotal(count($newArray));
	}


}



class theme_admin_online_tree_model extends e_tree_model
{


}






class theme_admin_form_ui extends e_admin_form_ui
{

	private $approvedAdminThemes = array('backend');


	function price($curVal)
	{
		if($this->getController()->getAction() == 'choose')
		{
			$sitetheme = e107::getPref('sitetheme');
			$path = $this->getController()->getListModel()->get('path');

			if($sitetheme == $path)
			{
				return "<span class='pull-right text-warning'><i class='fa fa-home'></i></span>";
			}

			return '';
		}

		$text =(!empty($curVal)) ? "<span class='label label-primary'><i class='fa fa-shopping-cart icon-white'></i> ".$curVal."</span>" : "<span class='label label-success'>".TPVLAN_76."</span>";

		return '<span class="price pull-right">'.$text.'</span>';
	}


/*
	function renderFilter($current_query = array(), $location = '', $input_options = array())
	{
		if($this->getController()->getAction() == 'choose')
		{
			return parent::renderFilter($current_query,$location,$input_options);
		}
		//	print_a($text);

	//	return $text;
			$text = "<form class='form-search' action='".e_SELF."' id='core-plugin-list-form' method='get'>
			<fieldset id='admin-ui-list-filter' class='e-filter'>
			<div class='col-md-12'>";
		//	$text .= '<div id="myCarousel"  class="carousel slide" data-interval="false">';
			$text .= "<div class='form-inline clearfix row-fluid'>";
			$text .= $this->search('srch', $_GET['srch'], 'go');

			$gets = $this->getController()->getQuery();

			foreach($gets as $k=>$v)
			{
				if($k == 'srch' || $k == 'go')
				{
					continue;
				}
				$text .= $this->hidden($k,$v);
			}

			$text .= $this->renderPagination();
			$text .= "</div>
					</div></fieldset></form>";

		return $text;
	}*/

	function options()
	{

		$theme = $this->getController()->getListModel()->getData();

		if($this->getController()->getAction() === 'online')
		{
			return $this->onlineOptions($theme);
		}
		else
		{
			return $this->chooseOptions($theme);

		}

	}

	private function chooseOptions($theme)
	{
		$pref = e107::getPref();
		$tp = e107::getParser();

		$infoPath       = e_SELF."?mode=".$_GET['mode']."&id=".$theme['path']."&action=info&iframe=1";
		$previewPath    = $tp->replaceConstants($theme['thumbnail'],'abs');

		$disabled = '';
		$mainTitle = TPVLAN_10;
 
		if(!e107::isCompatible($theme['compatibility'], 'theme'))
		{
			$disabled = 'disabled';
			$mainTitle = defset('TPVLAN_97', "This theme requires a newer version of e107.");
		}

		$main_icon 		= ($pref['sitetheme'] !== $theme['path']) ? "<button  ".$disabled." class='btn btn-default btn-secondary btn-small btn-sm btn-inverse' type='submit'   name='selectmain[".$theme['path']."]' alt=\"".$mainTitle."\" title=\"".$mainTitle."\" >".$tp->toGlyph('fa-home',array('size'=>'2x'))."</button>" : "<button class='btn btn-small btn-default btn-secondary btn-sm btn-inverse' type='button'>".$tp->toGlyph('fa-check',array('size'=>'2x'))."</button>";
		$info_icon 		= "<a class='btn btn-default btn-secondary btn-small btn-sm btn-inverse e-modal'  data-modal-caption=\"".$theme['name']." ".$theme['version']."\" href='".$infoPath."'  title='".TPVLAN_7."'>".$tp->toGlyph('fa-info-circle',array('size'=>'2x'))."</a>";
		$admin_icon     = '';

		if(in_array($theme['path'], $this->approvedAdminThemes))
		{
			$main_icon = '';
			$admin_icon 	= ($pref['admintheme'] !== $theme['path'] ) ? "<button class='btn btn-default btn-secondary btn-small btn-sm btn-inverse' type='submit'   name='selectadmin[".$theme['path']."]' alt=\"".TPVLAN_32."\" title=\"".TPVLAN_32."\" >".$tp->toGlyph('fa-gears',array('size'=>'2x'))."</button>" : "<button class='btn btn-small btn-default btn-secondary btn-sm btn-inverse' type='button'>".$tp->toGlyph('fa-check',array('size'=>'2x'))."</button>";
		}
 

		$preview_icon 	= "<a class='e-modal btn btn-default btn-secondary btn-sm btn-small btn-inverse' title=' ".TPVLAN_70." ".$theme['name']."' data-modal-caption=\"".$theme['name']." ".$theme['version']."\" rel='external'  href='".$previewPath."'>".$tp->toGlyph('fa-search',array('size'=>'2x'))."</a>";

		return $main_icon.$admin_icon.$info_icon.$preview_icon;
	}


	private function onlineOptions($theme)
	{
		$tp = e107::getParser();
		$preview_icon = '';

		$srcData = array(
				'id'    => $theme['id'],
				'url'   => $theme['url'],
				'mode'  => $theme['mode'],
				'price' => $theme['price']
		);

		e107::getSession()->set('thememanager/online/'.$theme['id'], $theme);

		$d = http_build_query($srcData,false);
		$base64 = base64_encode($d);

		$id = $this->name2id($theme['name']);

		if(!empty($theme['price'])) // Premium Theme
		{
			$LAN_DOWNLOAD = LAN_PURCHASE."/".LAN_DOWNLOAD;
			$downloadUrl = e_SELF.'?mode=main&action=download&e-token='.e_TOKEN.'&src='.base64_encode($d); // no iframe.
			$mainTarget = '_blank';
			$mainClass = '';
			$modalCaption = ' '.LAN_PURCHASE.' '.$theme['name']." ".$theme['version'];
		}
		else // Free Theme
		{
			$LAN_DOWNLOAD = LAN_DOWNLOAD;
			$downloadUrl = e_SELF.'?mode=main&iframe=1&action=download&e-token='.e_TOKEN.'&src='.base64_encode($d);//$url.'&amp;action=download';
			$mainTarget = '_self';
			$mainClass = 'e-modal';
			$modalCaption =  ' '.LAN_DOWNLOADING.' '.$theme['name']." ".$theme['version'];
		}

	//	$url = e_SELF."?src=".$base64;
		$infoUrl = e_SELF.'?mode=main&iframe=1&action=info&src='.$base64;
	//	$viewUrl = $theme['url'];
		$main_icon = "<a class='".$mainClass." btn-default btn-secondary btn btn-sm btn-small btn-inverse' target='".$mainTarget."' data-modal-caption=\"".$modalCaption."\"  href='{$downloadUrl}' data-cache='false' title='".$LAN_DOWNLOAD."' >".$tp->toGlyph('fa-download',array('size'=>'2x'))."</a>";
		$info_icon 	= "<a class='btn btn-default btn-secondary btn-sm btn-small btn-inverse e-modal' data-toggle='modal' data-bs-toggle='modal' data-modal-caption=\"".$theme['name']." ".$theme['version']."\" href='".$infoUrl."' data-cache='false'  title='".TPVLAN_7."'>".$tp->toGlyph('fa-info-circle',array('size'=>'2x'))."</a>";

		if(!empty($theme['preview'][0]))
		{
			$previewPath = $theme['preview'][0];

			if(!empty($theme['livedemo']))
			{
				$previewPath = $theme['livedemo'];
			}

			$preview_icon 	= "<a class='e-modal btn btn-default btn-secondary btn-sm btn-small btn-inverse' title=' ".TPVLAN_70." ".$theme['name']."' data-modal-caption=\"".$theme['name']." ".$theme['version']."\" rel='external'  href='".$previewPath."'>".$tp->toGlyph('fa-search',array('size'=>'2x'))."</a>";
		}

		return $main_icon.$info_icon.$preview_icon;

	}
	
}

 
/*
 * After initialization we'll be able to call dispatcher via e107::getAdminUI()
 * so this is the first we should do on admin page.
 * Global instance variable is not needed.
 * NOTE: class is auto-loaded - see class2.php __autoload()
 */
/* $dispatcher = */

new theme_admin();

/*
 * Uncomment the below only if you disable the auto observing above
 * Example: $dispatcher = new theme_admin(null, null, false);
 */
//$dispatcher->runObservers(true);

require_once(e_ADMIN."auth.php");

/*
 * Send page content
 */
e107::getAdminUI()->runPage();




require_once(e_ADMIN."footer.php");

/* OBSOLETE - see admin_shortcodes::sc_admin_menu()
function admin_config_adminmenu() 
{
	//global $rp;
	//$rp->show_options();
	e107::getRegistry('admin/blank_dispatcher')->renderMenu();
}
*/

/* OBSOLETE - done within header.php
function headerjs() // needed for the checkboxes - how can we remove the need to duplicate this code?
{
	return e107::getAdminUI()->getHeader();
}
*/

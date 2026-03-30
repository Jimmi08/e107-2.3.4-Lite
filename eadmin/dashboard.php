 <?php

/**
 * @file
 * Flexpanel dashboard style.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// Get "Apply dashboard preferences to all administrators" setting.
$adminPref = e107::getConfig()->get('adminpref', 0);
$flepanelEnabled = true;

// If not Main Admin and "Apply dashboard preferences to all administrators" is checked.
if(!getperms('1') && $adminPref == 1)
{
	$flepanelEnabled = false;
}

define('FLEXPANEL_ENABLED', $flepanelEnabled);


// Save rearranged menus to user.
if(e_AJAX_REQUEST)
{
	if(FLEXPANEL_ENABLED && varset($_POST['core-flexpanel-order'], false))
	{
		 /*
			$message = date('r') . "\n" . $message . "\n";
			$message .= "\n_POST\n";
			$message .= print_r($_POST, true);
			$message .= "\n_GET\n";
			$message .= print_r($_GET, true);

			$message .= '---------------';

			file_put_contents(e_LOG . 'uiAjaxFlexDashboard.log', $message . "\n\n", FILE_APPEND);
		  */

		// If "Apply dashboard preferences to all administrators" is checked.
		if($adminPref == 1)
		{
			e107::getConfig()
				->setPosted('core-flexpanel-order', $_POST['core-flexpanel-order'])
				->save();
		}
		else
		{
			e107::getUser()
				->getConfig()
				->set('core-flexpanel-order', $_POST['core-flexpanel-order'])
				->save();
		}
		exit;
	}
}

// Dashboard uses infopanel's methods to avoid code duplication.
// not used directly flexpanel - intention 
e107_require_once(e_ADMIN . 'includes/infopanel.php');

/* Notes
core-flexpanel-layout is not used because layout is replaced with dashboard template 

*/

/**
 * Class adminstyle_flexpanel.
 */
class adminstyle_dashboard extends adminstyle_infopanel
{


		private $iconlist = array();
		public $positions	= array();
		static $userAdminPanelArray = array();

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			parent::__construct();

			$this->iconlist = $this->getIconList();

			$this->positions = e107::getTemplate(false, 'dashboard', 'positions');
			self::$userAdminPanelArray = $this->getUserAdminPanelArray();

			if (FLEXPANEL_ENABLED)
			{
				e107::css('inline', '.draggable-panels .panel-heading { cursor: move; }');
				e107::js('core', 'core/admin.flexpanel.js', 'jquery', 4);

				if (varset($_GET['mode']) == 'customize')
				{
					e107::css('inline', '.layout-container { display: table; margin-left: auto; margin-right: auto; }');
					e107::css('inline', '.layout-container label.radio { float: left; padding: 0; width: 120px; margin: 7px; cursor: pointer; text-align: center; }');
					e107::css('inline', '.layout-container label.radio img { margin-left: auto; margin-right: auto; display: block; }');
					e107::css('inline', '.layout-container label.radio input { width: 100%; margin-left: auto; margin-right: auto; display: block; }');
					e107::css('inline', '.layout-container label.radio p { width: 100%; text-align: center; display: block; margin: 20px 0 0 0; }');
				}

				// Save posted Layout type.
				if (varset($_POST['e-flexpanel-layout']))
				{
					$user_pref = $this->getUserPref();

					// If Layout has been changed, we clear previous arrangement in order to use defaults.
					if ($user_pref['core-flexpanel-layout'] != $_POST['e-flexpanel-layout'])
					{
						$this->savePref('core-flexpanel-order', array());
					}

					$this->savePref('core-flexpanel-layout', $_POST['e-flexpanel-layout']);
				}
			}
		}


		/**
		 * Allow to set your own admin panels.
		 */
		function getUserAdminPanelArray()
		{
			$user_pref = $this->getUserPref();
			$myE107 = varset($user_pref['core-infopanel-mye107'], array());
			if (empty($myE107)) // Set default icons.
			{
				$user_pref['core-infopanel-mye107'] = e107::getNav()->getDefaultAdminPanelArray();
			}
			$this->userAdminPanelArray = $user_pref['core-infopanel-mye107'];
		}

 
		/**
		 * Get selected area and position for a menu item.
		 *
		 * @param $id
		 *  Menu ID.
		 * @return array
		 *  Contains menu area and weight.
		 */
		function getMenuPosition($id)
		{
			$user_pref = $this->getUserPref();

			if (!empty($user_pref['core-flexpanel-order'][$id]))
			{
				return $user_pref['core-flexpanel-order'][$id];
			}

			$default = array(
					'area'   => 'menu-area-02',
					'weight' => 1000,
				);

			$positions = $this->getDefaultPositions();

			$layout = varset($user_pref['core-flexpanel-layout'], 'default');
		 
			if (!empty($positions[$layout][$id]))
			{
				return $positions[$layout][$id];
			}

			/*if (strpos($id, 'plug-infopanel-') === 0) // addon dashboards default to area 2.
			{
				$default = array(
					'area'   => 'menu-area-02',
					'weight' => 1000,
				);
			}
			*/
			return $default;
		}


		/**
		 * Get default menu positions.
		 *
		 * @return array
		 */
		function getDefaultPositions()
		{
			return array( 
				'default'           => array(
					'core-infopanel-mye107'         => array(
						'area'   => 'menu-area-07',
						'weight' => 0,
					),
					'core-infopanel-news'           => array(
						'area'   => 'menu-area-08',
						'weight' => 0,
					) 
				),
			);
		}

		public function core_infopanel_news($options = array()) 
		{
			// $ns = e107::getRender();
		 
			// $dashboardUniqueId 	= varset($options['uniqueId'], time());
			// $dashboardStyle		= varset($options['style'], 'flexbox');

			// $newsTabs = array();
			// $newsTabs['coreFeed'] = array('caption' => LAN_GENERAL, 'text' => "<div id='e-adminfeed' style='min-height:300px'></div><div class='right'><a rel='external' href='" . ADMINFEEDMORE . "'>" . LAN_MORE . "</a></div>");
			// $newsTabs['pluginFeed'] = array('caption' => LAN_PLUGIN, 'text' => "<div id='e-adminfeed-plugin'></div>");
			// $newsTabs['themeFeed'] = array('caption' => LAN_THEMES, 'text' => "<div id='e-adminfeed-theme'></div>");

			// $code = "
			// jQuery(function($){
			// 	$('#e-adminfeed').load('" . e_ADMIN . "admin.php?mode=core&type=feed');
			// 	$('#e-adminfeed-plugin').load('" . e_ADMIN . "admin.php?mode=addons&type=plugin');
			// 	$('#e-adminfeed-theme').load('" . e_ADMIN . "admin.php?mode=addons&type=theme');	
			// });
			// ";
			// e107::js('inline', $code, 'jquery');

			// $ns->setStyle( $dashboardStyle);
			// $ns->setUniqueId($dashboardUniqueId);
		 
			// $coreInfoPanelNews = $ns->tablerender(LAN_LATEST_e107_NEWS, e107::getForm()->tabs($newsTabs, array('active' => 'coreFeed')), $dashboardUniqueId, true);
			
			// return $coreInfoPanelNews;
		}

		/**
		 * Display icons
		 */
		function core_infopanel_icons($options = array())
		{
 
			$ns = e107::getRender();

			$dashboardUniqueId 	= varset($options['uniqueId'], time());
			$dashboardStyle		= varset($options['style'], 'flexbox');
			$dashboardLinks     = varset($options['links'], '');
			$dashboardCaption     = varset($options['caption'], '');

			$newarray = e107::getNav()->adminLinks($dashboardLinks);

			$adminPanel = "<div id='.$dashboardUniqueId.' >";

			foreach ($newarray as $key => $val)
			{
				//if (in_array($key, $this->userAdminPanelArray))  - so confusing for not advanced main admin
				if (true)
				{
					if ($tmp = e107::getNav()->renderAdminButton($val['link'], $val['title'], $val['caption'], $val['perms'], $val['icon_32'], "div"))
					{
						$adminPanel .= $tmp;
					}
				}
			}
			$adminPanel .= "</div>";

			$ns->setStyle($dashboardStyle);
			$ns->setUniqueId($dashboardUniqueId);

			$coreInfoPanelAdmin = $ns->tablerender($dashboardCaption, $adminPanel, $dashboardUniqueId, true);

			return $coreInfoPanelAdmin;
		}

		function renderLatestComments($type = "blocked")
		{
			$sql = e107::getDb();
			$tp = e107::getParser();

			//if(!check_class('B')) // XXX problems?
			//	{
			//		return;
			//	}
			$where = '';
			switch ($type)
			{
				case "blocked":
					$where = "comment_blocked=2";
			}


			if (!$rows = $sql->retrieve('comments', '*', $where .' ORDER BY comment_id DESC LIMIT 25', true))
			{
				return null;
			}

			switch($type) {
				case "blocked": 
					$where = "comment_blocked=2";
			}

			$sc = e107::getScBatch('comment');

			$text = '
		  <ul class="media-list unstyled list-unstyled">';
			// <button class='btn btn-mini'><i class='icon-pencil'></i> Edit</button> 

			//XXX Always keep template hardcoded here - heavy use of ajax and ids. 
			$count = 1;

			$lanVar = array('x' => '{USERNAME}', 'y' => '{TIMEDATE=relative}');

			foreach ($rows as $row)
			{
				$hide = ($count > 3) ? ' hide' : '';

				$TEMPLATE = "{SETIMAGE: w=40&h=40}
				<li id='comment-" . $row['comment_id'] . "' class='media" . $hide . "'>
				<span class='media-object pull-left'>{USER_AVATAR=" . $row['comment_author_id'] . "}</span> 
				<div class='btn-group pull-right'>
	            	<button data-target='" . e_BASE . "comment.php' data-comment-id='" . $row['comment_id'] . "' data-comment-action='delete' class='btn btn-sm btn-mini btn-danger'><i class='fa fa-remove'></i> " . LAN_DELETE . "</button>
	            	<button data-target='" . e_BASE . "comment.php' data-comment-id='" . $row['comment_id'] . "' data-comment-action='approve' class='btn btn-sm btn-mini btn-success'><i class='fa fa-check'></i> " . LAN_APPROVE . "</button>
	            </div>
				<div class='media-body'>
					<small class='muted smalltext'>" . $tp->lanVars(LAN_POSTED_BY_X, $lanVar) . "</small><br />
					<p>{COMMENT}</p> 
				</div>
				</li>";

				$sc->setVars($row);
				$text .= $tp->parseTemplate($TEMPLATE, true, $sc);
				$count++;
			}


			$text .= '
     		</ul>
		    <div class="right">
		      <a class="btn btn-xs btn-mini btn-primary text-right" href="' . e_ADMIN . 'comment.php?searchquery=&filter_options=comment_blocked__2">' . LAN_VIEW_ALL . '</a>
		    </div>
		 ';
			// $text .= "<small class='text-center text-warning'>Note: Not fully functional at the moment.</small>";

			$ns = e107::getRender();
			return $text;
		}
	

		/* Comments */
		function core_infopanel_comments($options = array())
 		{
			$ns = e107::getRender();

			$dashboardUniqueId 	= varset($options['uniqueId'], time());
			$dashboardStyle		= varset($options['style'], 'flexbox');
			//$dashboardLinks     = varset($options['links'], '');
			$dashboardCaption     = varset($options['caption'], '');
 
			$ns->setStyle($dashboardStyle);
			$ns->setUniqueId($dashboardUniqueId);

			$content = 	$this->renderLatestComments();
			$coreInfoPanelAdmin = $ns->tablerender($dashboardCaption, $content, $dashboardUniqueId, true);

			//return $this->renderLatestComments(); trying to change core code for now (2x rendered pannel)
			return $coreInfoPanelAdmin;
		}
		

		/**
		 * Display Module Icons
		 */
		function addons_module_icons($options = array())
		{

			$ns = e107::getRender();

			$dashboardUniqueId 	= varset($options['uniqueId'], time());
			$dashboardStyle		= varset($options['style'], 'flexbox');
			$dashboardKey     = varset($options['key'], '');
			$dashboardCaption     = varset($options['caption'], '');

			$fullarray = e107::getNav()->adminLinks('plugin'); //all plugins
	 
			if ($plugs = e107::getAddonConfig('e_dashboard', null, $dashboardKey))
			{
	 
				foreach($plugs AS $key => $plug) {
					//check if is key
					$newarray["p-".$key] = $fullarray["p-" . $key];
				 }

			}	
			$adminPanel = "<div id='.$dashboardUniqueId.' >";

			foreach ($newarray as $key => $val)
			{
				if ($tmp = e107::getNav()->renderAdminButton($val['link'], $val['title'], $val['caption'], $val['perms'], $val['icon_32'], "div"))
				{

					$adminPanel .= $tmp;
				}
				 
			}
			$adminPanel .= "</div>";

			$ns->setStyle($dashboardStyle);
			$ns->setUniqueId($dashboardUniqueId);

			$coreInfoPanelAdmin = $ns->tablerender($dashboardCaption, $adminPanel, $dashboardUniqueId, true);

			return $coreInfoPanelAdmin;
		}
 
		/*****************************************/
		/* multi dashboard for e_dashboard in tabs, chart is hardcoded in parent
		/*****************************************/

		public function addons_chart_tabs($options = array()) {
			$ns = e107::getRender();
			// THIS IS CORRECT APPROACH FOR NOW
			// --------------------- Plugin Addon Dashboards ---------------------- eg. e107_plugin/user/e_dashboard.php
			// each plugin renders its own panel - chart method is needed 
			$dashboards = $this->getAddonDashboards();
			if (!empty($dashboards))
			{
				$ns->setStyle('flexpanel');
				foreach ($dashboards as $val)
				{
					$id = $val['mode'];
					$id = str_replace('_', '-', $id); // TODO fix this if they solve #4940 different way
					$ns->setUniqueId($id);
					$inc = $ns->tablerender($val['caption'], $val['text'], $val['mode'], true);
					$info = $this->getMenuPosition($id);

					if (!isset($this->positions[$info['area']][$info['weight']]))
					{
						$this->positions[$info['area']][$info['weight']] = '';
					}
					$this->positions[$info['area']][$info['weight']] .= $inc;
				}
			}

			return false ;
		}

		/**
		 * Render contents.
		 */
		public function render()
		{
 
			$mes = e107::getMessage();
	 
			$frm = e107::getForm();
 

			if (varset($_GET['mode']) == 'customize')
			{
				echo $frm->open('infopanel', 'post', e_SELF);
				//echo $ns->tablerender(LAN_DASHBOARD_LAYOUT, $this->renderLayoutPicker(), 'personalize', true);
				echo '<div class="clear">&nbsp;</div>';
				echo $this->render_infopanel_options(true);
				echo $frm->close();
				return;
			}
 
			/* LOGIC CHANGE SET EVERYTHING IN TEMPLATE *************************************
			/* TODO FIXME template name - for now trying to avoid to use admin_template   */

			$supported_panels = e107::getTemplate(false, 'dashboard', 'panels');
 
			foreach($supported_panels AS $key => $panel) {
				
 
				$params  = $panel;
				$perm = varset($params['perm'], false);
				$multi = varset($params['multi'], false);
 
				if(!getperms($perm)) continue;

				$params['uniqueId'] 	= $key; 
				$params['style'] 		= varset($params['style'], 'flexpanel');
				//	$params['options'] 		= $panel ;

				//e107::callMethod("adminstyle_dashboard", $panel_type, $params);  this is not working for multi dashboard 
				$method_name = varset($panel['method_name'], $key);
				$method_name = str_replace('-', '_', $method_name); // TODO fix this if they solve #4940 different way
			 
				$text = '';
				if( method_exists('adminstyle_dashboard', $method_name ))  {
					
					$text = $this->$method_name($params); 
				}
 
				if($text ) {
				 
					$info = $this->getMenuPosition($key);
	 
					if (!isset($this->positions[$info['area']][$info['weight']]))
					{
						$this->positions[$info['area']][$info['weight']] = '';
					}
					$this->positions[$info['area']][$info['weight']] .= $text;
					
				}
			 
			} 
  
			/*  END OF CHANGE **************************************************************/
 
 /*
			// --------------------- User Selected Menus ----------------------
			if (varset($user_pref['core-infopanel-menus']))
			{
				$ns->setStyle('flexpanel');
				foreach ($user_pref['core-infopanel-menus'] as $val)
				{
					// Custom menu.
					if (is_numeric($val))
					{
						$menu = e107::getDb()->retrieve('page', 'menu_name', 'page_id = ' . (int) $val);
						$id = 'cmenu-' . $menu;
						$inc = e107::getMenu()->renderMenu($val, null, null, true);
					}
					else
					{
						$id = $frm->name2id($val);
						$inc = $tp->parseTemplate("{PLUGIN=$val|TRUE}");
					}
					$info = $this->getMenuPosition($id);
					if (!isset($this->positions[$info['area']][$info['weight']]))
					{
						$this->positions[$info['area']][$info['weight']] = '';
					}
					$this->positions[$info['area']][$info['weight']] .= $inc;
				}
			}
*/
			
 
			// Sorting panels.
			foreach ($this->positions as $key => $value)
			{
				ksort($this->positions[$key]);
			}

			$FLEXPANEL_LAYOUT = e107::getCoreTemplate("dashboard", 'layout');
 
			$template = varset($FLEXPANEL_LAYOUT);
			$template = str_replace('{MESSAGES}', $mes->render(), $template);

			foreach ($this->positions as $key => $value)
			{
				$token = '{' . strtoupper(str_replace('-', '_', $key)) . '}';
				$template = str_replace($token, implode("\n", $value), $template);
			}

			echo $template;
			 
		}

}

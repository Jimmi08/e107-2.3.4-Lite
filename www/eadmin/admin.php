<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 */

define('e_ADMIN_HOME', true); // used by some admin shortcodes and class2.

require_once(__DIR__.'/../class2.php');

if(varset($_GET['mode']) == 'customize')
{
	$adminPref = e107::getConfig()->get('adminpref', 0);

	// If not Main Admin and "Apply dashboard preferences to all administrators"
	// is checked in admin theme settings.
	if(!getperms("1") && $adminPref == 1)
	{
		e107::redirect('admin');
		exit;
	}
}

// check that the bootstrap library path is up-to-date before the header is loaded.
/*if($info = e107::getLibrary()->load('bootstrap'))
{
    if($info['path'] !== '3')
    {
        e107::getCache()->clearAll('library');
        e107::getCache()->clearAll('browser');
    }
}*/

e107::getDebug()->logTime('[admin.php: Loading admin_icons]');
//include_once(e107::coreTemplatePath('admin_icons'));
e107::loadAdminIcons(); // Needs to be loaded before infopanel AND in boot.php

/*if(vartrue($_GET['iframe']) == 1)
{
	define('e_IFRAME', true);
}*/



$e_sub_cat = 'main';
 
//$pref['adminstyle'] = "infopanel";
require_once(e_ADMIN . 'includes/' . $pref['adminstyle'] . '.php');

$_class = 'adminstyle_' . $pref['adminstyle'];
if(class_exists($_class, false))
{
	$adp = new $_class;
}
else
{
	$adp = new adminstyle_infopanel;
}
 

// DEBUG THE ADDON_UPDATED INFOPANEL
//e107::getCache()->clear('Infopanel_plugin', true);
//e107::getSession()->clear('addons-update-status');
//e107::getSession()->set('addons-update-checked',false); // set to recheck it.



require_once(e_ADMIN.'boot.php');
require_once(e_HANDLER.'upload_handler.php');

new admin_start;

require_once(e_ADMIN.'auth.php');


e107::getDebug()->logTime('(Start Admin Checks)');


e107::getDebug()->logTime('(After Admin Checks)');
$mes = e107::getMessage();

if (!isset($pref['adminstyle'])) $pref['adminstyle'] = 'infopanel';		// Shouldn't be needed - but just in case
 
class admin_start
{

	private $incompat = array(
			array('banhelper',      1.5),
			array('banhelper',      1.7),
			array('slir_admin',     1.0),
			array('facebook_like',  0.7),
			array('unanswered',     1.4),
			array('lightwindow',    '1.0b'),
			array('aa_jquery',      1.2),
			array('aa_jquery',      1.4),
			array('who',            1.0),
			array('ratings',        4.2),
			array('lightbox',       1.5),
			array('e107slider',     0.1),
			array('forumthanks',    0.5),
			array('eclassifieds',   1.11),
			array('jshelpers',      '0.3b'),
			array('akismet',        7.0),
			array('newforumposts_main', 1),
			array('fancybox',       '2.06b'),
	);


	private $allowed_types = null;
	private $refresh  = false;
	private $exit = false;

	private $deprecated = array();
	private $upgradeRequiredFirst = false;
	
	function __construct()
	{

		if(e_AJAX_REQUEST || !getperms('0') || varset($_GET['mode']) === 'customize') // don't display this tuff to regular admins only main admin.
		{
			return null;
		}

		if(!e107::getDb()->isTable('admin_log')) // Upgrade from v1.x to v2.x required.
		{
		    $this->upgradeRequiredFirst = true;
        }

		// Files that can cause comflicts and problems.
        $fileInspector = e107::getFileInspector();
		$this->deprecated = $fileInspector::getCachedDeprecatedFiles();

		$this->checkCoreVersion();

		if(!empty($_POST['delete-deprecated']))
		{
			$this->deleteDeprecated();
		}

		unset($_SESSION['lancheck']);


		e107::getDebug()->logTime('Check Paths');
		$this->checkPaths();

		e107::getDebug()->logTime('Check Timezone');
		$this->checkTimezone();

		e107::getDebug()->logTime('Check Writable');
		$this->checkWritable();

		e107::getDebug()->logTime('Check Incompatible Plugins');
		$this->checkIncompatiblePlugins();

		e107::getDebug()->logTime('Check Filetypes');
		$this->checkFileTypes();

		e107::getDebug()->logTime('Check Suspect Files');
		$this->checkSuspiciousFiles();

		e107::getDebug()->logTime('Check Deprecated');
		$this->checkDeprecated();

		//e107::getDebug()->logTime('Check HTMLArea');
		//$this->checkHtmlarea();

		e107::getDebug()->logTime('Check Htaccess');
		$this->checkHtaccess();

		e107::getDebug()->logTime('Check Core Update');
		$this->checkCoreUpdate();

		if($this->exit === true)
		{
			return null;
		}

		e107::getDebug()->logTime('Check New Install');
		$this->checkNewInstall();

	/*	e107::getDebug()->logTime('Check Plugin Update');
		$this->checkPluginUpdate();

		e107::getDebug()->logTime('Check Theme Update');
		$this->checkThemeUpdate();
		*/
		e107::getDebug()->logTime('Check Password Encryption');
		$this->checkPasswordEncryption();

		//Check if developer mode is enabled
		$this->checkDeveloperMode(); 


		if($this->refresh == true)
		{
			e107::getRedirect()->go(e_REQUEST_SELF);
		}

		// delete half-completed user accounts. (previously called in header.php )
		e107::getUserSession()->deleteExpired();

	}	

	private function checkPaths()
	{
		$create_dir = array(e_MEDIA,e_SYSTEM,e_CACHE,e_CACHE_CONTENT,e_CACHE_IMAGE, e_CACHE_DB, e_LOG, e_BACKUP, e_CACHE_URL, e_TEMP, e_IMPORT);

		$mes = e107::getMessage();

		foreach($create_dir as $dr)
		{
			if(!is_dir($dr))
			{
				if(mkdir($dr, 0755))
				{
					$this->refresh = true;
				}
				else
				{
					$message = e107::getParser()->lanVars(ADLAN_187,$dr,true);
					$mes->addWarning($message);
				}
			}
		}

	}



	private function checkTimezone()
	{
		$mes = e107::getMessage();
		$timezone = e107::pref('core','timezone');

		if(e107::getDate()->isValidTimezone($timezone) == false)
		{
			$message = e107::getParser()->lanVars(ADLAN_188, $timezone);
			$mes->addWarning($message, 'default', true);
			e107::getConfig()->set('timezone','UTC')->save(false,true,false);
			$this->refresh = true;
		}

	}


	private function checkCoreVersion()
	{

		$e107info = array();

		require(e_ADMIN."ver.php");

		if(!empty($e107info['e107_version']) && defined('e_VERSION') && (e_VERSION !==  $e107info['e107_version']))
		{
			e107::getConfig()->set('version', $e107info['e107_version'])->save(false,true,false);

			// When version has changed, clear plugin/theme version cache.
			e107::getPlug()->clearCache();
			e107::getTheme()->clearCache();

			e107::getDebug()->log("Updating core version pref");
		}

	}



	private function checkCoreUpdate()
	{
		// auto db update
		if ('0' != ADMINPERMS)
		{
			return null;
		}

        if($this->upgradeRequiredFirst)
        {
            $message = "<p><a class='btn btn-lg btn-primary alert-link' href='e107_update.php'>".LAN_CONTINUE." ".SEP."</a></p>";
            e107::getMessage()->addInfo($message);
        }


		return null;


		$checked = e107::getSession()->get('core-update-checked');

		if(!deftrue('e_DEBUG') &&  $checked === true && !deftrue('e_DEVELOPER'))
		{
			e107::getMessage()->addDebug("Skipping core update");
			return null;
		}

		//$sc = e107::getScBatch('admin');
		//echo $tp->parseTemplate('{ADMIN_COREUPDATE=alert}',true, $sc);



		global $dont_check_update, $e107info;
		global $dbupdate, $dbupdatep, $e107cache;

		require_once(e_ADMIN.'update_routines.php');

		e107::getSession()->set('core-update-checked',true);
		e107::getMessage()->addDebug("Checking for core updates");


		if(update_check() === true)
		{

			$JS = <<<TMPO
			$(function () {

                 $('[data-toggle="popover"]').popover('show');
                 $('.popover').on('click', function() {
                     $('[data-toggle="popover"]').popover('hide');
                  }
				);
			});

TMPO;

			e107::js('footer-inline', $JS);
			e107::css('inline', '.hide.e-popover { display:block!important }');

			if(e_DEBUG !== true)
			{
				$this->exit = true;
			}
		}



	}

/*
 *  // Moved to admin_shortcodes.php
	private function checkPluginUpdate()
	{
		require_once(e_HANDLER.'e_marketplace.php');
		$mp = new e_marketplace(); // autodetect the best method

		$versions = $mp->getVersionList('plugin');

		$plugins = e107::getPref('plug_installed');

		if(empty($plugins))
		{
			return null;
		}


		$tp = e107::getParser();

		foreach($plugins as $folder=>$version)
		{

			if(!empty($versions[$folder]['version']) && version_compare( $version, $versions[$folder]['version'], '<'))
			{
				$link = "<a rel='external' class='alert-link' href='".$versions[$folder]['url']."'>".$versions[$folder]['name']."</a>";

				$dl = $mp->getDownloadModal('plugin', $versions[$folder]);

				$caption = LAN_DOWNLOAD.": ".$versions[$folder]['name']." ".$versions[$folder]['version'];

				$lans = array('x'=>$link, 'y'=>LAN_PLUGIN);
				$message = $tp->lanVars(LAN_NEWER_VERSION_OF_X, $lans);
				$message .= " <a href='".$dl."' class='e-modal alert-link' data-modal-caption=\"".$caption."\" title=\"".LAN_DOWNLOAD."\">".$tp->toGlyph('fa-arrow-circle-o-down')."</a>";


				e107::getMessage()->addInfo($message);

			}

		}


	}*/
/*
 *  Moved to admin_shortcodes.php
	private function checkThemeUpdate()
	{
		require_once(e_HANDLER.'e_marketplace.php');
		$mp = new e_marketplace(); // autodetect the best method

		$versions = $mp->getVersionList('theme');

		$themes = scandir(e_THEME);

		if(empty($themes))
		{
			return null;
		}

		$tp = e107::getParser();

		$list = e107::getTheme()->getThemeList();

		foreach($list as $data)
		{

			$folder = $data['path'];
			$version = $data['version'];

			if(!empty($versions[$folder]['version']) && version_compare( $version, $versions[$folder]['version'], '<'))
			{
				$link = "<a rel='external' class='alert-link' href='".$versions[$folder]['url']."'>".$versions[$folder]['name']."</a>";

				$lans = array('x'=>$link, 'y'=>LAN_THEME);

				$dl = $mp->getDownloadModal('theme', $versions[$folder]);

				$caption = LAN_DOWNLOAD.": ".$versions[$folder]['name']." ".$versions[$folder]['version'];

				$message = $tp->lanVars(LAN_NEWER_VERSION_OF_X, $lans);
				$message .= " <a href='".$dl."' class='e-modal alert-link' data-modal-caption=\"".$caption."\" title=\"".LAN_DOWNLOAD."\">".$tp->toGlyph('fa-arrow-circle-o-down')."</a>";


				e107::getMessage()->addInfo($message);
				e107::getMessage()->addDebug("Local version: ".$version." Remote version: ".$versions[$folder]['version']);
			}

		}



	}*/

	/**
	 *
	 */
	private function checkNewInstall()
	{

		return false;

	}



	private function checkWritable()
	{
		$mes = e107::getMessage();
		
		if(deftrue('e_MEDIA') && is_dir(e_MEDIA) && !is_writable(e_MEDIA))
		{
			$message = str_replace("[x]", e_MEDIA, ADLAN_193);
			$mes->addWarning($message);			
		}	
		
		if(deftrue('e_SYSTEM') && is_dir(e_SYSTEM) && !is_writable(e_SYSTEM))
		{
			$message = str_replace("[x]", e_SYSTEM, ADLAN_193);
			$mes->addWarning($message);			
		}

		$files = e107::getFile()->scandir(e_IMAGE."avatars",'jpg,gif,png,jpeg');


		if(is_dir(e_IMAGE."avatars") && !is_writable(e_IMAGE."avatars") && !empty($files))
		{
			$message = str_replace("[x]", e_IMAGE, ADLAN_194);
			$mes->addWarning($message);
		}
		
	}


	
	/*
	private function checkHtmlarea()
	{
		$mes = e107::getMessage();
		if (is_dir(e_ADMIN.'htmlarea') || is_dir(e_HANDLER.'htmlarea'))
		{
			$mes->addWarning(e_HANDLER."htmlarea/<br />".e_ADMIN_ABS."htmlarea/");
		}	
	}		
	*/


	private function checkIncompatiblePlugins()
	{
	    if($this->upgradeRequiredFirst)
	    {
	        return null;
        }

		$mes = e107::getMessage();
		
		$installedPlugs = e107::getPref('plug_installed');
	
		$inCompatText = "";
		$incompatFolders = array_keys($this->incompat);
		
		foreach($this->incompat as $data)
		{
			$folder = $data[0];
			$version = $data[1];

			if(!empty($installedPlugs[$folder]) && ($version == $installedPlugs[$folder] || $version === '*'))
			{
				$inCompatText .= "<li><a title='".LAN_UNINSTALL."' href='".e_ADMIN."plugin.php?mode=installed&action=uninstall&path=".$folder."'>".$folder." v".$installedPlugs[$folder]."</a></li>";
			}	
		}
		
		if($inCompatText)
		{
			$text = "<ul>".$inCompatText."</ul>";
			$mes->addWarning(ADLAN_189."&nbsp;<br /><br />".$text);
		}	
		
	}


	private function checkPasswordEncryption()
	{
	    if($this->upgradeRequiredFirst)
	    {
	        return null;
        }

		$us = e107::getUserSession();
		$mes = e107::getMessage();

		if($us->passwordAPIExists() === true && $us->getDefaultHashType() !== PASSWORD_E107_PHP && e107::pref('core','password_CHAP')==0)
		{
			$message = LAN_PASSWORD_WARNING;
			$srch = array('[',']');
			$repl = array("<a class='alert-link' href='".e_ADMIN."prefs.php#nav-core-prefs-security'>","</a>");
			$mes->addWarning(str_replace($srch,$repl,$message));
		}

	}

	private function checkDeveloperMode()
	{
		$pref 	= e107::getPref();
		$tp 	= e107::getParser();

		if($pref['developer'] && (strpos(e_SELF,'localhost') === false) && (strpos(e_SELF,'127.0.0.1') === false))
		{
			e107::getMessage()->addWarning($tp->toHTML(LAN_DEVELOPERMODE_CHECK, true));
		}
	}



	private function checkDependencies()
	{


	}


	private function checkDeprecated()
	{
        if($this->upgradeRequiredFirst)
        {
            return null;
        }

		$found = array();
		foreach($this->deprecated as $path)
		{
			if(file_exists($path))
			{
				$found[] = str_replace(e_BASE, "", $path);
			}


		}

		if(!empty($found))
		{
			$frm = e107::getForm();

			$text = $frm->open('deprecatedFiles', 'post');
			$text .= ADLAN_186;
			$text .= "<ul><li>".implode("</li><li>", $found)."</li></ul>";

			$text .= $frm->button('delete-deprecated',LAN_DELETE,'delete');
			$text .= $frm->close();

			e107::getMessage()->addWarning($text);
		}

	}

	private function deleteDeprecated()
	{
		$mes = e107::getMessage();


        $error = 0;

		foreach($this->deprecated as $file)
		{

			if(!file_exists($file))
			{
				continue;
			}

			if(@unlink($file))
			{
				$message = e107::getParser()->lanVars(LAN_UI_FILE_DELETED, array('x'=>$file));
				$mes->addSuccess($message);
			}
			else
			{
				$message = e107::getParser()->lanVars(LAN_UI_FILE_DELETED_FAILED, array('x'=>$file));
				$mes->addError($message);
				$error++;
			}
		}

        $logFile = e_LOG."fileinspector/deprecatedFiles.log";

        if($error === 0 && file_exists($logFile))
        {
            @unlink($logFile);
        }

	}


	private function checkHtaccess() // upgrade scenario
	{
		if(!file_exists(e_BASE.".htaccess") && file_exists(e_BASE."e107.htaccess"))
		{
			if(rename(e_BASE."e107.htaccess", e_BASE.".htaccess")===false)
			{
				e107::getMessage()->addWarning("Please rename your <b>e107.htaccess</b> file to <b>.htaccess</b>");
			}
		}
	}



	
	private function checkFileTypes()
	{
		$mes = e107::getMessage();
		
		$this->allowed_types = get_filetypes();			// Get allowed types according to filetypes.xml or filetypes.php
		if (count($this->allowed_types) == 0)
		{
			$this->allowed_types = array('zip' => 1, 'gz' => 1, 'jpg' => 1, 'png' => 1, 'gif' => 1, 'pdf'=>1);
			$mes->addDebug("Setting default filetypes: ".implode(', ',array_keys($this->allowed_types)));
		
		}	
	}
	


	private function checkSuspiciousFiles()
	{
		$mes = e107::getMessage();
		$public = array(e_UPLOAD, e_AVATAR_UPLOAD);
		$tp = e107::getParser();
		$exceptions = array(".","..","/","CVS","avatars","Thumbs.db",".ftpquota",".htaccess","php.ini",".cvsignore",'e107.htaccess');
		
		//TODO use $file-class to grab list and perform this check. 
		foreach ($public as $dir)
		{
			if (is_dir($dir))
			{
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (is_dir($dir."/".$file) == FALSE && !in_array($file,$exceptions))
						{
							$fext = substr(strrchr($file, "."), 1);
							if (!array_key_exists(strtolower($fext),$this->allowed_types) )
							{
								if ($file == 'index.html' || $file == "null.txt")
								{
									if (filesize($dir.'/'.$file))
									{
										$potential[] = str_replace('../', '', $dir).'/'.$file;
									}
								}
								else
								{
									$potential[] = str_replace('../', '', $dir).'/'.$file;
								}
							}
						}
					}
					closedir($dh);
				}
			}
		}
		
		if (isset($potential))
		{
			//$text = ADLAN_ERR_3."<br /><br />";
			$mes->addWarning($tp->toHTML(ADLAN_ERR_3, true));
			$text = '<ul>';
			foreach ($potential as $p_file)
			{
				$text .= '<li>'.$p_file.'</li>';
			}
			$mes->addWarning($text);
			//$ns -> tablerender(ADLAN_ERR_1, $text);
		}	
		
		
		
		
	}



	
}



// ---------------------------------------------------------






// end auto db update

/*
if (e_QUERY == 'purge' && getperms('0'))
{
	$admin_log->purge_log_events(false);
}
*/

$td = 1;


// DEPRECATED 
function render_links($link, $title, $description, $perms, $icon = FALSE, $mode = FALSE)
{
	return e107::getNav()->renderAdminButton($link, $title, $description, $perms, $icon, $mode);
}


function render_clean() // still used by classis, tabbed etc. 
{
	global $td;
	$text = "";
	while ($td <= defset('ADLINK_COLS', 5))
	{
		$text .= "<td class='td' style='width:20%;'></td>";
		$td++;
	}
	$text .= "</tr>";
	$td = 1;
	return $text;
}



if(isset($adp) && is_object($adp))
{
	$adp->render();
}
else
{
	require_once(e_ADMIN.'includes/'.$pref['adminstyle'].'.php');	
}



function admin_info()
{
	global $tp;

	$width = (getperms('0')) ? "33%" : "50%";

	$ADMIN_INFO_TEMPLATE = "
	<div style='text-align:center'>
		<table style='width: 100%; border-collapse:collapse; border-spacing:0px;'>
		<tr>
			<td style='width: ".$width."; vertical-align: top'>
			{ADMIN_STATUS}
			</td>
			<td style='width:".$width."; vertical-align: top'>
			{ADMIN_LATEST}
			</td>";

    	if(getperms('0'))
		{
			$ADMIN_INFO_TEMPLATE .= "
			<td style='width:".$width."; vertical-align: top'>{ADMIN_LOG}</td>";
    	}

   	$ADMIN_INFO_TEMPLATE .= "
		</tr></table></div>";

	return $tp->parseTemplate($ADMIN_INFO_TEMPLATE);
}

function status_request()
{
	global $pref;
	if ($pref['adminstyle'] == 'classis'  || $pref['adminstyle'] == 'tabbed') {
		return TRUE;
	} else {
		return FALSE;
	}
}


function latest_request()
{
	return FALSE;
}

function log_request()
{
	return FALSE;
}

// getPlugLinks() - moved to sitelinks_class.php : pluginLinks();



require_once("footer.php");

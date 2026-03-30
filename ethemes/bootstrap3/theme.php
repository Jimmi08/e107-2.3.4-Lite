<?php

/**
 * e107 website system
 *
 * Copyright (C) 2008-2017 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @file
 * Bootstrap 3 Theme for e107 v2.3.x+.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// e107::lan('theme');

class theme implements e_theme_render
{

    public function init()
    {

        e107::meta('viewport',"width=device-width, initial-scale=1.0");
     
        e107::js("footer-inline", 	"$('.e-tip').tooltip({container: 'body'});"); // activate bootstrap tooltips.
 
    }


	/**
	 * Override how THEME_STYLE is loaded. Duplicates will be automatically removed.
	 * @return void
	 */
	function css()
	{


		e107::css('theme', 'style.css'); // always load style.css last.
		e107::css('theme', THEME_STYLE);

		e107::css('inline', '#carousel-hero.carousel {   margin-bottom: 80px; }');
	}

    /**
     * @param string $caption
     * @param string $text
     * @param string $id : id of the current render
     * @param array $info : current style and other menu data.
     * @return null
     */
    public function tablestyle($caption, $text, $id=null, $info=array())
	{



		$style = is_string($info['setStyle']) ? $info['setStyle'] : ''; //	global $style; // no longer needed.

	    echo "<!-- tablestyle: style=".$style." id=".$id." -->\n\n";

	    /*
	    if($id == 'wm') // Example - If rendered from 'welcome message'
	    {
			$style = '';
	    }

	    if($id == 'featurebox') // Example - If rendered from 'featurebox'
	    {
			$style = '';
	    }
	    */

		switch($style)
		{
			case "navdoc":
			case "none":

				echo $text;

				break;

			case "jumbotron":

				echo '<div class="jumbotron">
	            <div class="container">';

	            if(!empty($caption))
	            {
	                echo '<h1>'.$caption.'</h1>';
	            }

		        echo '
		        '.$text.'
		        </div>
	        	</div>';

				break;

			case "col-md-4":
			case "col-md-6":
			case "col-md-8":

				 echo ' <div class="col-xs-12 '.$style.'">';

			     if(!empty($caption))
			     {
			        echo '<h2>'.$caption.'</h2>';
			     }

			     echo '
			     '.$text.'
		         </div>';

				break;


			case "menu":

				echo '<div class="panel panel-default">
			    <div class="panel-heading">'.$caption.'</div>
				    <div class="panel-body">
				    '.$text.'
				    </div>
			    </div>';

				break;


			case "portfolio":

				 echo '
		         <div class="col-lg-4 col-md-4 col-sm-6">
		              '.$text.'
		        </div>';

				break;


			case 'default':
			case 'main':

			    if(!empty($caption))
			    {
			        echo '<h2 class="caption">'.$caption.'</h2>';
			    }

			    if($info['styleCount'] === 1) // add the breadcrumb the first time the 'default' style is rendered.
			    {
			        echo '{---BREADCRUMB---}';
			    }

			    echo $text;
			break;


			default:

			    if(!empty($caption))
			    {
			        echo '<h2 class="caption">'.$caption.'</h2>';
			    }

			    echo $text;
		}

	    return null;

	}

    
}

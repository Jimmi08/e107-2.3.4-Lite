<?php

// Generated e107 Plugin Admin Area

define('e_ADMIN_AREA', true);

require_once '../../../class2.php';
if (!getperms('P'))
{
    e107::redirect('admin');
    exit;
}


require_once "admin_menu.php";

class themeoptions_ui extends e_admin_ui
{
 
    protected $table = NULL;
    protected $fieldpref = array();

    protected $prefs = array(
        'panel-heading-bg'        =>
        array(
            'title'  => "Panel Heading Background color",
            'tab'           => 0,
            'type'           => 'text',
            'data'           => 'str',
            'help'           => 'Recommended #DDD',
            'writeParms'   => array(

                'pre'      => '<div class="col-md-2 ecp input-group colorpicker-component colorpicker-element">',
                'post'     => '<span class="input-group-addon"><i></i></span></div>'
            ),
        ),

        'panel-heading-color'        =>
        array(
            'title'  => "Panel Heading Text color",
            'tab'           => 0,
            'type'           => 'text',
            'data'           => NULL,
            'help'           => 'Recommended #222 ',
            'writeParms'   => array(

                'pre'      => '<div class="col-md-2 ecp input-group colorpicker-component colorpicker-element">',
                'post'     => '<span class="input-group-addon"><i></i></span></div>'
            ),
        ),

        'panel-icon-border'        =>
        array(
            'title'  => "Panel Icon Border color",
            'tab'           => 0,
            'type'           => 'text',
            'data'           => NULL,
            'help'           => 'Recommended #ccc ',
            'writeParms'   => array(

                'pre'      => '<div class="col-md-2 ecp input-group colorpicker-component colorpicker-element">',
                'post'     => '<span class="input-group-addon"><i></i></span></div>'
            ),
        ),

        'panel-icon-color'        =>
        array(
            'title'  => "Panel Icon color",
            'tab'           => 0,
            'type'           => 'text',
            'data'           => NULL,
            'help'           => 'Recommended #FFF ',
            'writeParms'   => array(

                'pre'      => '<div class="col-md-2 ecp input-group colorpicker-component colorpicker-element">',
                'post'     => '<span class="input-group-addon"><i></i></span></div>'
            ),
        ),



        'panel-icon-bg'        =>
        array(
            'title'  => "Panel Icon Background",
            'tab'           => 0,
            'type'           => 'text',
            'data'           => NULL,
            'help'           => 'Recommended #022643',
            'writeParms'   => array(

                'pre'      => '<div class="col-md-2 ecp input-group colorpicker-component colorpicker-element">',
                'post'     => '<span class="input-group-addon"><i></i></span></div>'
            ),
        ),


    );

    public function init()
    {

        $custom_theme_prefs = e107::getThemeConfig('backend')->getPref();
        $this->prefs['panel-heading-color']['writeParms']['default'] = $custom_theme_prefs['panel-heading-color'];
        $this->prefs['panel-heading-bg']['writeParms']['default'] = $custom_theme_prefs['panel-heading-bg'];
        $this->prefs['panel-icon-border']['writeParms']['default'] = $custom_theme_prefs['panel-icon-border'];
        $this->prefs['panel-icon-color']['writeParms']['default'] = $custom_theme_prefs['panel-icon-color'];
        $this->prefs['panel-icon-bg']['writeParms']['default'] = $custom_theme_prefs['panel-icon-bg'];
    }

    public function beforePrefsSave($new_data, $old_data)
    {

        $pref = e107::getThemeConfig('backend', true);
        $theme_pref = array();

        $theme_pref['panel-heading-bg'] = $new_data['panel-heading-bg'];
        $theme_pref['panel-heading-color'] = $new_data['panel-heading-color'];
        $theme_pref['panel-icon-border'] = $new_data['panel-icon-border'];
        $theme_pref['panel-icon-color'] = $new_data['panel-icon-color'];
        $theme_pref['panel-icon-bg'] = $new_data['panel-icon-bg'];

        $pref->setPref($theme_pref)->save(true, true, false);

        $new_data['header_class'] = '';
        return false;
    }
}

class themeoptions_form_ui extends e_admin_form_ui
{
}

new leftmenu_adminArea();

require_once e_ADMIN . "auth.php";
e107::getAdminUI()->runPage();
require_once e_ADMIN . "footer.php";
exit;

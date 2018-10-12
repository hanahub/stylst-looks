<?php
/*
   Plugin Name: Stylst Looks
   Plugin URI: https://www.stylst.com/plugin
   Description: Stylst Looks is a plugin that features new looks within a sidebar and updates automatically every week. We offer a monthly compensation to be part of our network. Email info@socialroot.co for more info.
   Version: 1.0.4
   Author: SocialRoot
   Author URI: https://www.stylst.com
   License: GPLv2 or later
*/

    define ('DLSL_PLUGIN_PATH', dirname( __FILE__ ));
    define ('DLSL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
    define ('DLSL_PLUGIN_BASENAME', plugin_basename(__FILE__));

    require_once DLSL_PLUGIN_PATH . '/inc/widget.php';

    function dlst_in_development()
    {
        return strtolower(substr($_SERVER['HTTP_HOST'], 0, 9)) == 'localhost';
    }

    function dlst_digitallylux_admin_init()
    {
        register_setting( 'dlst_stylst_options', 'dlst_stylst_options', 'dlst_stylst_options_validate' );
    }

    function dlst_digitallylux_admin_menu()
    {
        add_options_page(__('Stylst Looks Configuration'), __('Stylst'), 'manage_options', 'dlst_stylst_options', 'dlst_stylst_options_page');
    }

    function dlst_stylst_options_page()
    {
        require DLSL_PLUGIN_PATH . '/tpl/options.php';
    }

    function dlst_stylst_options_validate($input)
    {
        // Our first value is either 0 or 1
        $input['site_id'] = intval($input['site_id']);
        return $input;
    }

    add_action('admin_init', 'dlst_digitallylux_admin_init', 12);
    add_action('admin_menu', 'dlst_digitallylux_admin_menu', 12);

    function dlst_digitallylux_init()
    {
        $placement = dlst_stylst_options('placement');
        switch ($placement) {
            case 'top':
                //add_action('loop_start', 'dlst_digitallylux_slider_widget');
                break;
            case 'bottom':
                //add_action('loop_end', 'dlst_digitallylux_slider_widget');
                break;
            default:
                //add_action('loop_start', 'dlst_digitallylux_slider_widget');
                // nothing
        }

        $name = dlst_stylst_options('name');
        if ( !$name ) {
            $cj_filename = str_replace('.', '', $_SERVER['HTTP_HOST']);
        } else {
            $cj_filename = strtolower($name);
        }

        if (dlst_in_development())
        {
            $api_url = 'http://localhost:4567';
        }
        else
        {
            $api_url = 'https://home.digitallylux.com';
        }
    }

    function dlst_digitallylux_action_links($links, $file)
    {
        if ( $file == DLSL_PLUGIN_BASENAME ) {
            $link = '<a href="options-general.php?page=dlst_stylst_options">Settings</a>';
            array_unshift($links, $link);
        }
        return $links;
    }

    function dlst_stylst_options($key = null)
    {
        $options = get_option('dlst_stylst_options');
        return $key ? $options[$key] : $options;
    }

    function dlst_digitallylux_style_and_scripts()
    {
        wp_register_style('shopbop-css', DLSL_PLUGIN_URL . 'css/shopbop.css', array(), '0.0.1');
        wp_enqueue_style('shopbop-css');
    }

    add_action( 'init', 'dlst_digitallylux_init' );
    //add_action( 'admin_enqueue_scripts', 'dlst_digitallylux_style_and_scripts' );
    add_action( 'wp_enqueue_scripts', 'dlst_digitallylux_style_and_scripts' );
    add_filter( 'plugin_action_links', 'dlst_digitallylux_action_links', 10, 2 );
?>

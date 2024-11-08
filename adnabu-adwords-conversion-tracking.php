<?php

/*
Plugin Name: Adwords Conversion Tracking
Plugin URI: http://adnabu.com
Description: A woo-commerce conversion tracking pixel
Version: 2.0.0
Author: AdNabu
Author URI: http://adnabu.com
License: GPLv2 or later
Text Domain: adwords-conversion-tracking
*/

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );


if(!class_exists('AdNabuBasev2')){
    include_once 'includes/Base/AdNabuBasev2.php';
    if(!class_exists('AdNabuPixelBase')){
        include_once 'includes/Base/AdNabuPixelBase.php';
    }
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once 'includes/AdNabuAdwordsConversionTracking.php';

register_deactivation_hook( __FILE__, array( 'AdNabuBasev2', 'deactivate' ) );
register_uninstall_hook(__FILE__,array('AdNabuAdwordsConversionTracking','uninstall_app'));
if(!is_plugin_active('woocommerce/woocommerce.php' )){
    $instance = new AdNabuAdwordsConversionTracking();
    add_action( 'admin_notices', array( $instance, 'missing_woocommerce' ) );
}
else{
    $instance = new AdNabuAdwordsConversionTracking();
    add_action( 'admin_notices', array( $instance, 'activation_greeting' ) );
    add_action('wp_enqueue_scripts',  array( $instance, 'expose_parameters' ));
    add_action('admin_enqueue_scripts',array( $instance, 'enqueue_admin_assets' ));
    $filter_name = "plugin_action_links_" . plugin_basename( __FILE__ );
    add_filter($filter_name, array($instance, 'settings_link' ));
    add_action('admin_menu',  array( $instance, 'add_menu_page' ), 0);
    add_action('admin_menu', array( $instance, 'add_app_page' ), 1);
    register_activation_hook( __FILE__, array(  $instance, 'activate_app' ) );
}








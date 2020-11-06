<?php
/**
* Plugin Name: Discount Price For Woocommerce
* Description: This plugin allows create qty rules for products.
* Version: 1.0
* Copyright: 2019 
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
  die('-1');
}
if (!defined('DPFW_PLUGIN_NAME')) {
  define('DPFW_PLUGIN_NAME', 'Discount Price For Woocommerce');
}
if (!defined('DPFW_PLUGIN_VERSION')) {
  define('DPFW_PLUGIN_VERSION', '1.0');
}
if (!defined('DPFW_PLUGIN_FILE')) {
  define('DPFW_PLUGIN_FILE', __FILE__);
}
if (!defined('DPFW_PLUGIN_DIR')) {
  define('DPFW_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('DPFW_DOMAIN')) {
  define('DPFW_DOMAIN', 'dpfw');
}


if (!class_exists('DPFW')) {

    class DPFW {

        protected static $DPFW_instance;
        function __construct() {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            //check woocommerce plugin activted or not
            add_action('admin_init', array($this, 'DPFW_check_plugin_state'));
        }


        function DPFW_check_plugin_state(){
            if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
                set_transient( get_current_user_id() . 'dpfwerror', 'message' );
            }
        }


        function init() {
            add_action( 'admin_notices', array($this, 'DPFW_show_notice'));
            add_action( 'admin_enqueue_scripts', array($this, 'DPFW_load_admin'));
            add_action( 'wp_enqueue_scripts',  array($this, 'DPFW_load_front'));
        }


        function DPFW_show_notice() {
            if ( get_transient( get_current_user_id() . 'wqrerror' ) ) {

                deactivate_plugins( plugin_basename( __FILE__ ) );

                delete_transient( get_current_user_id() . 'wqrerror' );

                echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=woocommerce">WooCommerce</a> plugin installed and activated.</p></div>';
            }
        }

       
        function DPFW_load_admin() {
            wp_enqueue_style( 'WQR_admin_style', DPFW_PLUGIN_DIR . '/includes/css/wqr_back_style.css', false, '1.0.0' );
            wp_enqueue_script( 'WQR_admin_script', DPFW_PLUGIN_DIR . '/includes/js/wqr_back_script.js', false, '1.0.0' );
        }


        function DPFW_load_front() {
            wp_enqueue_style( 'WQR_front_style', DPFW_PLUGIN_DIR . '/includes/css/wqr_front_style.css', false, '1.0.0' );
        }

        
        function includes() {
            include_once('admin/wqr_admin_product.php');           
            include_once('front/wqr_front_product.php');
        }


        public static function DPFW_instance() {
            if (!isset(self::$DPFW_instance)) {
                self::$DPFW_instance = new self();
                self::$DPFW_instance->init();
                self::$DPFW_instance->includes();
            }
            return self::$DPFW_instance;
        }

    }
    add_action('plugins_loaded', array('DPFW', 'DPFW_instance'));
}

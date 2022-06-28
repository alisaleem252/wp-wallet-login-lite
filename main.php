<?php
/**
 * Plugin Name: WP Wallet Login Custom Lite
 * Plugin URI: https://gigsix.com/
 * Description: WP Wallet Login Custom, Login with Crypto Wallets.
 * Author: wpfixit
 * Version: 1.5.1
 * Author URI: https://gigsix.com
 * Text Domain: wpwalletlogincustom
 * Domain Path: /languages
 *
 * @package wpwalletlogincustom
 */
define('wpwlc_URL', plugin_dir_url(__FILE__));
define('wpwlc_PATH', dirname(__FILE__));

require_once wpwlc_PATH."/admin/admin.php";
require_once wpwlc_PATH."/public/hooks.php";
require_once wpwlc_PATH."/public/shortcode.php";
require_once wpwlc_PATH."/admin/page.php";



    if(function_exists("elementor_load_plugin_textdomain")){

        add_action( 'elementor/widgets/register', 'register_connect_wallet_custom_widgetsCBF' );
        function register_connect_wallet_custom_widgetsCBF( $widgets_manager ) {
            require_once( __DIR__ . '/public/elementor_element.php' );
            $widgets_manager->register( new \Connect_Wallet_Widget() );  

        }
    }



    add_action( 'login_enqueue_scripts', 'wpwlc_enqueue_scriptsCBF' );
	add_action('wp_enqueue_scripts', 'wpwlc_enqueue_scriptsCBF');
	function wpwlc_enqueue_scriptsCBF(){
		wp_enqueue_script('web3-axios.min-JS', plugins_url('js/axios.min.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-web3.min-JS', plugins_url('js/web3.min.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-web3modal-JS', plugins_url('js/web3modal.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-portis-JS', plugins_url('js/portis.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-torus.min-JS', plugins_url('js/torus.min.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-fortmatic-JS', plugins_url('js/fortmatic.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-walletconnect.min-JS', plugins_url('js/walletconnect.min.js', __FILE__ ),array('jquery'));

       
		wp_enqueue_script('web3-login-custom-JS', plugins_url('js/web3-login.js', __FILE__ ),array('jquery'));
		wp_enqueue_script('web3-modal-custom-JS', plugins_url('js/web3-modal.js', __FILE__ ),array('jquery'));
		//wp_enqueue_style('cfrd-salient-CSS', plugins_url('css/salient-wpbakery-addons-basic.css', __FILE__ ));

        
		
	} // function cfrd__enqueue_scriptsCBF() 

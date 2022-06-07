<?php
/**
 * Plugin Name: WP Wallet Login Custom
 * Plugin URI: https://gigsix.com/
 * Description: WP Wallet Login Custom, Login with Crypto Wallets.
 * Author: wpfixit
 * Version: 1.5.0
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
//require_once wpwlc_PATH."/public/shortcode.php";
require_once wpwlc_PATH."/admin/page.php";
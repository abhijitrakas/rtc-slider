<?php
/**
 * This is plugin to add slider in your website
 *
 * @package Rtc_Slider
 * @version 1.0
 *
 * @wordpress-plugin
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/*
Plugin Name:  Rtc Slider
Plugin URI:   http://codesip.com
Description:  The simple slider plugin
Version:      1.0.0
Author:       Abhijit Rakas
Author URI:   https://profiles.wordpress.org/abhijitrakas/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  rtc-sl
*/

require_once realpath( plugin_dir_path( __FILE__ ) ) . '/class-rtc-slider-query.php';

// load only for admin.
if ( is_admin() ) {
	include_once realpath( plugin_dir_path( __FILE__ ) ) . '/admin/class-rtc-option-setting.php';
	new Rtc_Option_Setting();
} else {
	include_once realpath( plugin_dir_path( __FILE__ ) ) . '/public/class-rtc-home-page.php';
	new Rtc_Home_Page();
}

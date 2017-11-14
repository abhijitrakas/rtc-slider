<?php
/**
 * Class MainPluginFile
 *
 * @package Rtc_Slider
 */

class MainPluginFile extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_file_exist() {
		$plugin_directory = plugin_dir_path( dirname( __FILE__ ) );
		// Replace this with some actual testing code.
		$this->assertFileExists( $plugin_directory . 'rtc-slider.php' );
	}

	/**
	 * Check home page class loading
	 */
	public function test_is_home_loading() {
		$this->assertTrue( is_object( new Rtc_Home_Page() ) );
		$this->assertTrue( is_object( new Rtc_Option_Setting() ) );
	}

}

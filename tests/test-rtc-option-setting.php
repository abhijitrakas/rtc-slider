<?php
/**
 * This file test plugin setting options
 */

class PluginSettingOptionTest extends WP_UnitTestCase {

	/**
	 * Variable to store plugin direstory path
	 *
	 * @var
	 */
	public $plugin_directory;

	/**
	 * Varible to store class object
	 *
	 * @var
	 */
	public $rtcMenuClsObj;

	/**
	 * Variable to store created images ids
	 *
	 * @var
	 */
	public $media = [];

	public function setUp() {
		parent::setUp();
		$this->plugin_directory = plugin_dir_path( dirname( __FILE__ ) );
		include_once $this->plugin_directory . 'admin/class-rtc-option-setting.php';
		$this->rtcMenuClsObj = new Rtc_Option_Setting();
		$this->media[] = $this->factory->attachment->create();
		$this->media[] = $this->factory->attachment->create();
		update_option( 'rtc_slider_images', wp_json_encode( $this->media ) );
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * A single example test.
	 */
	public function test_file_exist() {
		// Replace this with some actual testing code.
		$this->assertFileExists( $this->plugin_directory . 'admin/class-rtc-option-setting.php' );
	}

	/**
	 * Check boot function is present
	 */
	public function test_main_func_exist() {
		// menu option
		$this->assertTrue( method_exists( $this->rtcMenuClsObj, 'rtc_slider_add_menu_option' ) );
		// check menu not created yet.
		$this->assertFalse( ! empty( $GLOBALS['admin_page_hooks']['rtc-slider-setting.php'] ) );
		// adding menu to wordpress admin.
		$this->rtcMenuClsObj->rtc_slider_add_menu_option();
		// check menu created or not.
		$this->assertTrue( ! empty( $GLOBALS['admin_page_hooks']['rtc-slider-setting.php'] ) );
		// menu option page content
		$this->assertTrue( method_exists( $this->rtcMenuClsObj, 'rtc_slider_page' ) );
    }

    /**
     * test to check script load function exist
     */
    public function test_rtc_slider_load_required_script_exist() {
		// menu option
		$this->assertTrue( method_exists( $this->rtcMenuClsObj, 'rtc_slider_load_required_script' ) );
		$this->rtcMenuClsObj->rtc_slider_load_required_script();
		// check sortable ui loaded.
		$this->assertTrue( wp_script_is( 'rtc-sortable', 'enqueued' ) );
		// check toast lib loaded.
		$this->assertTrue( wp_script_is( 'rtc-toast-js', 'enqueued' ) );
		// check plugin js loaded.
		$this->assertTrue( wp_script_is( 'rtc-slider-js', 'enqueued' ) );
		// check toast style loaded properly.
		$this->assertTrue( wp_style_is( 'rtc-toast-noty', 'enqueued' ) );
		// check plugin style loaded properly.
		$this->assertTrue( wp_style_is( 'rtc-admin-css', 'enqueued' ) );
	}

	/**
	 * Test Rtc slider images loading on setting page properly
	 */
	public function test_rtc_slider_get_images_wrapper() {
		$this->assertEmpty( $this->rtcMenuClsObj->rtc_slider_get_images_wrapper('') );
	}

	/**
	 * Test function is exist
	 */
	public function test_rtc_get_image_wrapper() {
		$this->assertTrue( method_exists( $this->rtcMenuClsObj, 'rtc_get_image_wrapper' ) );
		$image_id = 100; $media_link = 'www.gstatic.com/webp/gallery/5.jpg';
		// check for empty string.
		$this->assertEmpty( $this->rtcMenuClsObj->rtc_get_image_wrapper('','') );
		$this->assertEmpty( $this->rtcMenuClsObj->rtc_get_image_wrapper($image_id,'') );
		$this->assertEmpty( $this->rtcMenuClsObj->rtc_get_image_wrapper('', $media_link) );
		// required data.
		$rquiredStr = '<li class="rtc-slide-thumb or-spacer" data-img-id="' . $image_id . '"><div class="rtc-slide-row"><input type="checkbox" class="rtc-slider-remove"></div><div class="rtc-slide-row"><img class="rtc-full-wd" src="' . $media_link . '"><div class="mask"></div><div></li>';
		// check not empty.
		$this->assertNotEmpty( $this->rtcMenuClsObj->rtc_get_image_wrapper($image_id, $media_link) );
		// check string equals.
		$this->assertEquals( $rquiredStr, $this->rtcMenuClsObj->rtc_get_image_wrapper($image_id, $media_link) );
		// check string not equals.
		$this->assertNotEquals( $rquiredStr, $this->rtcMenuClsObj->rtc_get_image_wrapper( 12, $media_link) );
	}

}

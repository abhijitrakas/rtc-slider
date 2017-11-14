<?php
/**
 * This file test plugin setting options
 */

class RtcAjaxCallTest extends WP_Ajax_UnitTestCase {

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
        $this->media[0] = $this->factory->attachment->create();
        $this->media[1] = $this->factory->attachment->create();
        $this->media[2] = $this->factory->attachment->create();
        $this->media[3] = $this->factory->attachment->create();
        // to call ajax to update images data.
		add_action( 'wp_ajax_rtc_slider_save_image_id', [ $this, 'rtc_slider_save_image_id' ] );
    }

    public function tearDown() {
        parent::tearDown();
        $_POST = [];
    }

    /**
	 * Test security nonce not valid
	 */
	public function test_rtc_slider_save_image_empty_post() {
		$_POST = [];
		try {
			$this->_handleAjax( 'rtc_slider_save_image_id' );
		} catch ( WPAjaxDieContinueException $e ) {

        } catch ( WPAjaxDieStopException $e ) {}

        $response = json_decode( $this->_last_response );
        // check ajax call done successfully.
        $this->assertTrue( $response->success );
        // check error class
        $this->assertEquals( 'showWarningToast', $response->data->class );
        // function responce success fail.
        $this->assertFalse( $response->data->success );
        // check message in responce.
    	$this->assertEquals( 'No change found in setting!', $response->data->message );
    }

    /**
	 * Test empty call of ajax to remove all image
	 */
	public function test_rtc_slider_save_image_id_empty() {
		$_POST = [
            'security'  => wp_create_nonce( 'rtc-jq-nonce' ),
            'rtcaction' => 'remove',
		];
		try {
			$this->_handleAjax( 'rtc_slider_save_image_id' );
		} catch ( WPAjaxDieContinueException $e ) {

        } catch ( WPAjaxDieStopException $e ) {}

        $response = json_decode( $this->_last_response );
        // check ajax call done successfully.
        $this->assertTrue( $response->success );
        // check error class
        $this->assertEquals( 'showSuccessToast', $response->data->class );
        // function responce success fail.
        $this->assertTrue( $response->data->success );
        // check message in responce.
    	$this->assertEquals( 'Image removed successfully.', $response->data->message );
    }

    /**
	 * Test security nonce not valid
	 */
	public function test_rtc_slider_save_image_invalid_nounce() {
		$_POST = [
            'security'  => 0000000,
            'rtcaction' => 'remove',
		];
		try {
			$this->_handleAjax( 'rtc_slider_save_image_id' );
		} catch ( WPAjaxDieContinueException $e ) {

        } catch ( WPAjaxDieStopException $e ) {}

        $response = json_decode( $this->_last_response );
        // check ajax call done successfully.
        $this->assertTrue( $response->success );
        // check error class
        $this->assertEquals( 'showWarningToast', $response->data->class );
        // function responce success fail.
        $this->assertFalse( $response->data->success );
        // check message in responce.
    	$this->assertEquals( 'No change found in setting!', $response->data->message );
    }

    /**
	 * Test empty call of ajax
	 */
	public function test_rtc_slider_save_image_id_success() {
		$_POST = [
			'security'  => wp_create_nonce( 'rtc-jq-nonce' ),
			'imagesIds' => $this->media,
		];
		try {
			$this->_handleAjax( 'rtc_slider_save_image_id' );
		} catch ( WPAjaxDieContinueException $e ) {

        } catch ( WPAjaxDieStopException $e ) {}

        $response = json_decode( $this->_last_response );
        // is response is object.
        $this->assertTrue( is_object( $response ) );
        // check success in response.
        $this->assertTrue( $response->success );
        // check response message
        $this->assertEquals( 'All slider images saved!', $response->data->message );
        // check success class in responce
        $this->assertEquals( 'showSuccessToast', $response->data->class );
        // check option save in database.
        $this->assertEquals( wp_json_encode( $this->media ), get_option( 'rtc_slider_images' ) );
        // check stored value is not empty.
        $this->assertNotEquals( wp_json_encode( [] ), get_option( 'rtc_slider_images' ) );
    }

    /**
	 * Test single image is remove
	 */
	public function test_rtc_slider_remove_img_n_save_remaining_images_id_success() {
        unset( $this->media[3] );
		$_POST = [
			'security'  => wp_create_nonce( 'rtc-jq-nonce' ),
            'imagesIds' => $this->media,
            'rtcaction' => 'remove',
		];
		try {
			$this->_handleAjax( 'rtc_slider_save_image_id' );
		} catch ( WPAjaxDieContinueException $e ) {

        } catch ( WPAjaxDieStopException $e ) {}

        $response = json_decode( $this->_last_response );
        // is response is object.
        $this->assertTrue( is_object( $response ) );
        // check success in response.
        $this->assertTrue( $response->success );
        // check response message
        $this->assertEquals( 'Image removed successfully.', $response->data->message );
        // check success class in responce
        $this->assertEquals( 'showSuccessToast', $response->data->class );
        // check option save in database.
        $this->assertEquals( wp_json_encode( $this->media ), get_option( 'rtc_slider_images' ) );
        // check stored value is not empty.
        $this->assertNotEquals( wp_json_encode( [] ), get_option( 'rtc_slider_images' ) );
	}

}

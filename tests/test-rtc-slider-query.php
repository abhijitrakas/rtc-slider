<?php
/**
 * Test class to test modal query
 *
 * @package Rtc_Slider
 * @version 1.0.0
 *
 * @WordPess_package
 */

/**
 * Class to test rtc modal class functions
 */
class RtcSliderQueryTest extends WP_UnitTestCase {

    /**
	 * Variable to store plugin path
	 *
	 * @var
	 */
	public $plugin_directory;

	/**
	 * Variable to store class instance
	 *
	 * @var
	 */
    public $rtcQueryBuilder;

    /**
	 * Variable to store added media ids
	 *
	 * @var
	 */
	public $media;

	/**
	 * Funciton to set up testing envoirment
	 */
	public function setUp() {
        parent::setUp();
		$this->plugin_directory = plugin_dir_path( dirname( __FILE__ ) );
        $this->rtcQueryBuilder = new Rtc_Slider_Query();
        $this->media = [
            $this->factory->attachment->create(),
            $this->factory->attachment->create(),
            $this->factory->attachment->create(),
            $this->factory->attachment->create(),
        ];
    }

    public function tearDown() {
		parent::tearDown();
	}

    /**
     * Test file present in setup
     */
    public function test_file_exist() {
        $this->assertFileExists( $this->plugin_directory . 'class-rtc-slider-query.php' );
    }

    /**
     * Test option updating properly
     */
    public function test_rtc_slider_update_slider_setting() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'rtc_slider_update_slider_setting' ) );
        // check item updating properly.
        $this->assertTrue( $this->rtcQueryBuilder->rtc_slider_update_slider_setting( $this->media ) );
        // should false as option setting as null.
        $this->assertFalse( $this->rtcQueryBuilder->rtc_slider_update_slider_setting( $this->media, '' ) );
        $this->assertTrue( $this->rtcQueryBuilder->rtc_slider_update_slider_setting() );
    }

    /**
     * Test function to get option values
     */
    public function test_rtc_slider_get_images_ids() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'rtc_slider_get_images_ids' ) );
        $this->rtcQueryBuilder->rtc_slider_update_slider_setting( $this->media );
        // check database.
        $this->assertEquals( $this->media, $this->rtcQueryBuilder->rtc_slider_get_images_ids() );
        // check unique ids array
        $this->assertEquals(
            array_unique( $this->media ),
            $this->rtcQueryBuilder->rtc_slider_get_images_ids(
                'rtc_slider_images',
                true
            )
        );
    }

    /**
     * Test function to get qequired path
     */
    public function test_get_file_url() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'get_file_url' ) );
        // check for empty.
        $this->assertEmpty( $this->rtcQueryBuilder->get_file_url( '' ) );
        $fullpath = plugins_url(
			        	'lib/jssor/assets/css/jssor-slider.css',
                        dirname( __FILE__ )
                    );
        // check required file path return.
        $this->assertEquals( $fullpath, $this->rtcQueryBuilder->get_file_url('lib/jssor/assets/css/jssor-slider.css') );
    }

    /**
     * Test wp_query working properly
     */
    public function test_rtc_slider_get_media_link() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'rtc_slider_get_media_link' ) );
        $this->assertFalse( $this->rtcQueryBuilder->rtc_slider_get_media_link() );
        $this->assertNotEmpty( $this->rtcQueryBuilder->rtc_slider_get_media_link( $this->media ) );
        // check query object
        $this->assertTrue(
            is_object(
                $this->rtcQueryBuilder->rtc_slider_get_media_link([
                        $this->factory->attachment->create(),
                        $this->factory->attachment->create()
                    ]
                )
            )
        );
    }

    /**
     * Test wp query object
     */
    public function test_rtc_slider_get_media_from_obj() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'rtc_slider_get_media_from_obj' ) );
        // check for empty.
        $this->assertEmpty( $this->rtcQueryBuilder->rtc_slider_get_media_from_obj( '' ) );
    }

    /**
     * Test to get all selected images list
     */
    public function test_rtc_slider_assign_url_to_ids() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'rtc_slider_assign_url_to_ids' ) );
        // check for empty.
        $this->assertEmpty( $this->rtcQueryBuilder->rtc_slider_assign_url_to_ids( '', '' ) );
        $this->assertEmpty( $this->rtcQueryBuilder->rtc_slider_assign_url_to_ids( 12, '' ) );
        $this->assertEmpty( $this->rtcQueryBuilder->rtc_slider_assign_url_to_ids( '', 'http://www.gstatic.com/webp/gallery/5.jpg' ) );
        // data need by functions.
        $images = ['2','3','4','5','4','5','2'];
        $wp_media = [
            '2' => 'http://www.gstatic.com/webp/gallery/2.jpg',
            '3' => 'http://www.gstatic.com/webp/gallery/3.jpg',
            '4' => 'http://www.gstatic.com/webp/gallery/4.jpg',
            '5' => 'http://www.gstatic.com/webp/gallery/5.jpg',
        ];
        // data need in response of functions
        $requiredArray = [
            [ 'link' => 'http://www.gstatic.com/webp/gallery/2.jpg', 'id' => 2],
            [ 'link' => 'http://www.gstatic.com/webp/gallery/3.jpg', 'id' => 3],
            [ 'link' => 'http://www.gstatic.com/webp/gallery/4.jpg', 'id' => 4],
            [ 'link' => 'http://www.gstatic.com/webp/gallery/5.jpg', 'id' => 5],
            [ 'link' => 'http://www.gstatic.com/webp/gallery/4.jpg', 'id' => 4],
            [ 'link' => 'http://www.gstatic.com/webp/gallery/5.jpg', 'id' => 5],
            [ 'link' => 'http://www.gstatic.com/webp/gallery/2.jpg', 'id' => 2],
        ];
        // checking data equals.
        $this->assertEquals(
            $requiredArray,
            $this->rtcQueryBuilder->rtc_slider_assign_url_to_ids(
                $images,
                $wp_media
            )
        );
        // making data durty to satisfy conditions
        $requiredArray[3]['id'] = 1;
        // cheking data not equals.
        $this->assertNotEquals(
            $requiredArray,
            $this->rtcQueryBuilder->rtc_slider_assign_url_to_ids(
                $images,
                $wp_media
            )
        );
    }

    /**
     * Test to check all admin panel slider links
     */
    public function test_rtc_slider_images_url() {
        // checking funciton is exist
        $this->assertTrue( method_exists( $this->rtcQueryBuilder, 'rtc_slider_images_url' ) );
        $this->assertEmpty( $this->rtcQueryBuilder->rtc_slider_images_url() );
    }

}

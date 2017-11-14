<?php
/**
 * Class RtcHomePageTest
 *
 * @package Rtc_Slider
 */

class RtcHomePageTest extends WP_UnitTestCase {

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
	public $homePage;

	/**
	 * Funciton to set up testing envoirment
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin_directory = plugin_dir_path( dirname( __FILE__ ) );
		$this->homePage = new Rtc_Home_Page();
	}

	public function tearDown() {
		parent::tearDown();
	}

    /**
	 * Test file exist
	 */
	public function test_file_exist() {
		// test file exist.
		$this->assertFileExists( $this->plugin_directory . 'public/class-rtc-home-page.php' );
    }

    /**
	 * Test shortcode function is available
	 */
	public function test_shortcode_available() {
		$this->assertTrue( method_exists( $this->homePage, 'rtc_load_slider' ) );
	}

	/**
	 * Test shortcodes is exist
     */
	public function test_rtc_shortcodes_exists() {
		$this->assertTrue( shortcode_exists( 'myslideshow' ) );
	}

	/**
	 * Test all required script loader function is exist or not
	 */
	public function test_rtc_load_slider_style_scripts() {
		// test script loader function is available.
		$this->assertTrue( method_exists( $this->homePage, 'rtc_load_slider_style_scripts' ) );
		// load all scripts
		$this->homePage->rtc_load_slider_style_scripts();
		// check style is loaded.
		$this->assertTrue( wp_style_is( 'rtc-jssor-slider-css', 'enqueued' ) );
		// check all script is loaded.
		$this->assertTrue( wp_script_is( 'rtc-jssor-slider', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'rtc-jssor-slider-setting', 'enqueued' ) );
	}

	/**
	 * Test function is exist and to test div tag workign properly
	 */
	public function test_rtc_slider_get_div() {
		// check file exist
		$this->assertTrue( method_exists( $this->homePage, 'rtc_slider_get_div' ) );
		$this->assertNotEmpty( $this->homePage->rtc_slider_get_div() );
		$requiredOutput = '<div></div>';
		// check responce is equal to reuired output
		$this->assertEquals( $requiredOutput, $this->homePage->rtc_slider_get_div() );
		$requiredOutput = '<div><image></div>';
		// check responce is equal to reuired output
		$this->assertEquals( $requiredOutput, $this->homePage->rtc_slider_get_div( '<image>' ) );
	}

	/**
	 * Test function is exist and checking image tag working as per required
	 */
	public function test_rtc_slider_img_tag() {
		// check file exist
		$this->assertTrue( method_exists( $this->homePage, 'rtc_slider_img_tag' ) );
		// should pass test and return empty as no parameter pass
		$this->assertEmpty( $this->homePage->rtc_slider_img_tag() );
		// test google image links
		$link = 'http://www.gstatic.com/webp/gallery/1.jpg';
		// required image tag
		$requiredOutput = '<img data-u="image" src="' . $link . '" />';
		// check responce is equal to reuired output
		$this->assertEquals( $requiredOutput, $this->homePage->rtc_slider_img_tag( $link ) );
		// noise data.
		$requiredOutput = '<img data-u="image" src="" />';
		// checking wrond data wtih response.
		$this->assertNotEquals( $requiredOutput, $this->homePage->rtc_slider_img_tag( $link ) );
	}

	/**
	 * Test to get whole slide div
	 */
	public function test_rtc_slider_img_html() {
		// check file exist
		$this->assertTrue( method_exists( $this->homePage, 'rtc_slider_img_html' ) );
		// should pass test and return empty as no parameter pass
		$this->assertEmpty( $this->homePage->rtc_slider_img_html() );
		// test google image links
		$link = 'http://www.gstatic.com/webp/gallery/1.jpg';
		// required image tag
		$requiredOutput = '<div><img data-u="image" src="' . $link . '" /></div>';
		// check responce is equal to reuired output
		$this->assertEquals( $requiredOutput, $this->homePage->rtc_slider_img_html( $link ) );
	}

	/**
	 * Test to get proper list of all slider images
	 */
	public function test_rtc_slider_load_list() {
		// check file exist.
		$this->assertTrue( method_exists( $this->homePage, 'rtc_slider_load_list' ) );
		// list of all slide images.
		$imgArray[] 	= [ 'link' => 'http://www.gstatic.com/webp/gallery/1.jpg' ];
		$imgArray[] 	= [ 'link' => 'http://www.gstatic.com/webp/gallery/2.jpg' ];
		$imgArray[] 	= [ 'link' => 'http://www.gstatic.com/webp/gallery/3.jpg' ];
		$imgArray[] 	= [ 'link' => 'http://www.gstatic.com/webp/gallery/4.jpg' ];
		$requiredOutput = '';
		// get required output.
		foreach ( $imgArray as $link ) {
			$requiredOutput .= '<div><img data-u="image" src="' . $link['link'] . '" /></div>';
		}
		$this->assertEquals( $requiredOutput, $this->homePage->rtc_slider_load_list( $imgArray ) );
	}

	/**
	 * Test function is exist and return html data
	 */
	public function test_load_all_images() {
		// check file exist
		$this->assertTrue( method_exists( $this->homePage, 'load_all_images' ) );
		// check not empty.
		$this->assertNotEmpty( $this->homePage->load_all_images() );
	}

}

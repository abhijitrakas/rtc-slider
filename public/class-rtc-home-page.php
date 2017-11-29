<?php
/**
 * This file will add slider to site using sort codes
 *
 * @package Rtc_Slider
 * @version 1.0
 *
 * @wordpress-plugin
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class to
 */
class Rtc_Home_Page extends Rtc_Slider_Query {

	/**
	 * Function to load all required dependancies
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// action to load shortcodes.
		add_shortcode( 'myslideshow', [ $this, 'rtc_load_slider' ] );
		// loading scripts.
		add_action( 'wp_enqueue_scripts', [ $this, 'rtc_load_slider_style_scripts' ] );
	}

	/**
	 * Function to load slider html
	 *
	 * @access public
	 * @return text
	 */
	public function rtc_load_slider() {
		return (
			'<div>
                <div id="jssor_1">' .
					$this->rtc_slider_loading() .
					$this->load_all_images() .
					$this->rtc_slider_bullets() .
					$this->rtc_slider_left_arrows() .
					$this->rtc_slider_right_arrows() .
				'</div>
            </div>'
		);
	}

	/**
	 * Function to all required scripts and styles
	 *
	 * @access public
	 * @return void
	 */
	public function rtc_load_slider_style_scripts() {
		// jssor slider css.
		wp_register_style(
			'rtc-jssor-slider-css',
			$this->get_file_url( 'lib/jssor/assets/css/jssor-style.css' ),
			[],
			'26.3.0',
			'all'
		);
		wp_enqueue_style( 'rtc-jssor-slider-css' );
		// jssor plugin file.
		wp_register_script(
			'rtc-jssor-slider',
			$this->get_file_url( 'lib/jssor/assets/js/jssor.slider.min.js' ),
			[],
			'26.3.0',
			true
		);
		// jssor slider adjstment code.
		wp_register_script(
			'rtc-jssor-slider-setting',
			$this->get_file_url( 'lib/jssor/assets/js/jssor-slider.js' ),
			[ 'rtc-jssor-slider' ],
			'26.3.0',
			true
		);
		wp_enqueue_script( 'rtc-jssor-slider-setting' );
	}

	/**
	 * Fucntion to display on page loading
	 *
	 * @access public
	 * @return text
	 */
	public function rtc_slider_loading() {
		return ( '<!-- Loading Screen --> <div data-u="loading" class="jssorl-009-spin"> <img class="jssor-loader" src="' . $this->get_file_url( 'lib/jssor/assets/svg/ball-triangle.svg' ) . '" /> </div>' );
	}

	/**
	 * Function to get slider bullets
	 *
	 * @access public
	 * @return text
	 */
	public function rtc_slider_bullets() {
		return (
			'<!-- Bullet Navigator -->
            <div data-u="navigator" class="jssorb053 jssorb101" data-autocenter="1" data-scale="0.5"
                data-scale-bottom="0.75">
                <div data-u="prototype" class="i rtc-slider-bullet-size">
					<svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    	<circle class="co" cx="8000" cy="8000" r="5000"></circle>
                    	<circle class="ci" cx="8000" cy="8000" r="3000"></circle>
                	</svg>
                </div>
            </div>'
		);
	}

	/**
	 * Function to get left aorrw
	 *
	 * @access public
	 * @return text
	 */
	public function rtc_slider_left_arrows() {
		return (
			'<div data-u="arrowleft" class="jssora093 jssor-arrowLeft" data-autocenter="2" data-scale="0.75"
                data-scale-left="0.75">
                <svg viewBox="0 0 16000 16000" class="jssor-arrw">
                    <circle class="c" cx="8000" cy="8000" r="5920"></circle>
                    <polyline class="a" points="7777.8,6080 5857.8,8000 7777.8,9920 "></polyline>
                    <line class="a" x1="10142.2" y1="8000" x2="5857.8" y2="8000"></line>
                </svg>
            </div>'
		);
	}

	/**
	 * Function to get right aorrw
	 *
	 * @access public
	 * @return text
	 */
	public function rtc_slider_right_arrows() {
		return (
			'<div data-u="arrowright" class="jssora093 jssor-arrowRight" data-autocenter="2" data-scale="0.75"
                data-scale-right="0.75">
                <svg viewBox="0 0 16000 16000" class="jssor-arrw">
                    <circle class="c" cx="8000" cy="8000" r="5920"></circle>
                    <polyline class="a" points="8222.2,6080 10142.2,8000 8222.2,9920 "></polyline>
                    <line class="a" x1="5857.8" y1="8000" x2="10142.2" y2="8000"></line>
                </svg>
            </div>'
		);
	}

	/**
	 * Function to get div tag
	 *
	 * @access public
	 * @param  string $html // load given html in div tag.
	 * @return text
	 */
	public function rtc_slider_get_div( $html = '' ) {
		return '<div>' . $html . '</div>';
	}

	/**
	 * Function to get slider image tag
	 *
	 * @access public
	 * @param  string $link // image link to add in src.
	 * @return text
	 */
	public function rtc_slider_img_tag( $link = '' ) {
		if ( empty( $link ) ) {
			return '';
		}
		return '<img data-u="image" src="' . $link . '" />';
	}

	/**
	 * Function to get image html to display on home page
	 *
	 * @access public
	 * @param  array $image_link // image link.
	 * @return html
	 */
	public function rtc_slider_img_html( $image_link = '' ) {
		$html = '';
		if ( ! empty( $image_link ) ) {
			$html = $this->rtc_slider_get_div(
				$this->rtc_slider_img_tag( $image_link )
			);
		}
		return $html;
	}

	/**
	 * Function to get list of all selected slider images
	 *
	 * @access public
	 * @param  array $all_images // all images links data.
	 * @return text
	 */
	public function rtc_slider_load_list( $all_images ) {
		$all_images_link = '';
		if ( ! empty( $all_images ) ) {
			foreach ( $all_images as $key => $slider_image ) {
				// function to get slider image tag.
				$all_images_link .= $this->rtc_slider_img_html( $slider_image['link'] );
			}
		}
		return $all_images_link;
	}

	/**
	 * Function to load all images in slider
	 *
	 * @access public
	 * @return string
	 */
	public function load_all_images() {
		// get all slide images.
		$all_images_link = $this->rtc_slider_images_url( 'rtc_slider_images' );
		// function to list of all images to display in slider.
		$all_images = $this->rtc_slider_load_list( $all_images_link );
		return (
			'<div data-u="slides" class="jssor-imgs-container">' .
				$all_images .
			'</div>'
		);
	}

}

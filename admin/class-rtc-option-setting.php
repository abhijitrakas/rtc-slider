<?php
/**
 * This file will add slider option menu in WordPress backends
 *
 * @package Rtc_Slider
 * @version 1.0
 *
 * @wordpress-plugin
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class contain method which add custom menu option in WordPress backend
 */
class Rtc_Option_Setting extends Rtc_Slider_Query {

	/**
	 * Loading class dependancies
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'rtc_slider_add_menu_option' ] );
		// to load required script and styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'rtc_slider_load_required_script' ] );
		// to call ajax to update images data.
		add_action( 'wp_ajax_rtc_slider_save_image_id', [ $this, 'rtc_slider_save_image_id' ] );
	}

	/**
	 * Method to add slider setting menu in administrative view
	 *
	 * @access public
	 * @return void
	 */
	public function rtc_slider_add_menu_option() {
		add_menu_page(
			'Rtc Slider Options',
			'Rtc Slider',
			'manage_options',
			'rtc-slider-setting.php',
			[
				$this,
				'rtc_slider_page',
			],
			'',
			81
		);
	}

	/**
	 * Function to load slider setting page content
	 *
	 * @access public
	 * @return void
	 */
	public function rtc_slider_page() {
		?>
			<div class="wrap">
				<h1 class="wp-heading-inline">Rtc Slider</h1>
				<a href="#" class="page-title-action" id="rtc-slider-upload-img">Upload Slider Image</a>
				<div>
					<div class="rtc-tp-btm">
						<select name="action" id="rtc-bulk-action">
							<option vlaue="-1">Bulk Actions</option>
							<option value="rtc-remove-call">Remove</option>
						</select>
						<input type="button" class="button action" id="rtc-remove-btn" value="Apply">
					</div>
					<?php
						echo '<input type="hidden" name="rtc-jq-nonce" id="rtc-jq-nonce" value="' . wp_create_nonce( 'rtc-jq-nonce' ) . '" />'; // WPCS: XSS ok.
					?>
					<div class="rtc-title">
						<div class="div-cell">
							<input type="checkbox" id="rtc-select-all">
						</div>
						<h3 class="div-cell rtc-pd-lft	">
							Slider Images
						</h3>
					</div>
					<ul id="rtc-slider-images" class="connectedSortable">
						<?php
							echo $this->rtc_slider_get_images_wrapper(); // WPCS: XSS ok.
						?>
					</ul>
				</div>
			</div>
		<?php
	}

	/**
	 * Function to load requried script to plugin setting page
	 *
	 * @access public
	 * @return void
	 */
	public function rtc_slider_load_required_script() {
		wp_register_style(
			'rtc-toast-noty',
			$this->get_file_url( 'lib/toast/css/jquery.toastmessage.css' ),
			[],
			'1.0.0',
			'all'
		);
		wp_register_style(
			'rtc-admin-css',
			$this->get_file_url( 'admin/assets/css/rtc-admin-style.css' ),
			[ 'rtc-toast-noty' ],
			'1.0.0',
			'all'
		);
		wp_enqueue_style( 'rtc-admin-css' );
		wp_register_script(
			'rtc-sortable',
			$this->get_file_url( 'lib/jquery-ui.min.js' ),
			[ 'jquery' ],
			'1.12.1',
			true
		);
		wp_enqueue_script( 'rtc-sortable' );
		wp_register_script(
			'rtc-toast-js',
			$this->get_file_url( 'lib/toast/jquery.toastmessage.js' ),
			[ 'jquery' ],
			'1.0.0',
			true
		);
		wp_enqueue_script( 'rtc-toast-js' );
		wp_enqueue_media();
		wp_register_script(
			'rtc-slider-js',
			$this->get_file_url( 'admin/assets/js/rtc-slider.js' ),
			[ 'jquery' ],
			'1.0.0',
			true
		);
		wp_enqueue_script( 'rtc-slider-js' );
	}

	/**
	 * Function to handle ajax call and save slider images data
	 *
	 * @access public
	 * @return void
	 */
	public function rtc_slider_save_image_id() {
		// saving all images in database.
		$success_msg = 'All slider images saved!';
		$fail_msg    = 'No change found in setting!';
		if ( isset( $_POST['security'] ) && check_ajax_referer( 'rtc-jq-nonce', 'security', false ) ) { // Input var okay.
			$images_ids = [];
			if ( isset( $_POST['imagesIds'] ) && ! empty( $_POST['imagesIds'] ) ) { // Input var okay.
				$images_ids = array_map( 'intval', wp_unslash( $_POST['imagesIds'] ) ); // Input var okay.
			}
			if ( isset( $_POST['rtcaction'] ) && 'remove' === $_POST['rtcaction'] ) { // Input var okay.
				$fail_msg    = 'Opps something fail! Unable to remove image.';
				$success_msg = 'Image removed successfully.';
			}

			if ( $this->rtc_slider_update_slider_setting( $images_ids ) ) { // Input var okay.
				wp_send_json_success(
					[
						'class'   => 'showSuccessToast',
						'message' => $success_msg,
						'success' => true,
					]
				);
			}
		}
		wp_send_json_success(
			[
				'class'   => 'showWarningToast',
				'message' => $fail_msg,
				'success' => false,
			]
		);

	} // @codeCoverageIgnore

	/**
	 * Function to get all slider iamges with styles
	 *
	 * @access public
	 * @param  stirng $get_option // get option name.
	 * @return text
	 */
	public function rtc_slider_get_images_wrapper( $get_option = 'rtc_slider_images' ) {
		// getting all slider images from database.
		$all_slider_images = $this->rtc_slider_images_url( $get_option );
		// render html.
		return $this->render_all_selected_images_html( $all_slider_images );
	}

	/**
	 * Function to get image with li tag wrapper
	 *
	 * @access public
	 * @param  int  $image_id    // media id.
	 * @param  text $media_link  // media link.
	 * @return html
	 */
	public function rtc_get_image_wrapper( $image_id, $media_link ) {
		if ( ! empty( $image_id ) && ! empty( $media_link ) ) {
			return '<li class="rtc-slide-thumb or-spacer" data-img-id="' . $image_id . '"><div class="rtc-slide-row"><input type="checkbox" class="rtc-slider-remove"></div><div class="rtc-slide-row"><img class="rtc-full-wd" src="' . $media_link . '"><div class="mask"></div><div></li>';
		}
		return '';
	}

	/**
	 * Function to get all selected iamges html
	 *
	 * @access public
	 * @param  array $images // array of images.
	 * @return text
	 */
	public function render_all_selected_images_html( $images = [] ) {
		$html = '';
		if ( ! empty( $images ) ) {
			foreach ( $images as $key => $slider_image ) {
				// combining all slider images to display on setting page.
				$html .= $this->rtc_get_image_wrapper( $slider_image['id'], $slider_image['link'] );
			}
		}
		return $html;
	}

}

<?php
/**
 * Modal class to intract with database to perform database related operations.
 *
 * @package Rtc_Slider
 * @version 1.0.0
 *
 * @WordPress package
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Class conatian all db related opreations
 */
class Rtc_Slider_Query {

	/**
	 * Function to update images data in database
	 *
	 * @access public
	 * @param  array  $images_id   // all selected images ids.
	 * @param  string $option_name // all selected images ids.
	 * @return boolean
	 */
	public function rtc_slider_update_slider_setting( $images_id = [], $option_name = 'rtc_slider_images' ) {
		if ( ! empty( $option_name ) ) {
			return update_option( $option_name, wp_json_encode( $images_id ) );
		}
		return false;
	}

	/**
	 * Function to get all images id from option
	 *
	 * @access public
	 * @param  string  $get_option      // option to select select data.
	 * @param  boolean $select_unique_ids // select unique ids from list.
	 * @return json
	 */
	public function rtc_slider_get_images_ids( $get_option = 'rtc_slider_images', $select_unique_ids = false ) {
		$get_ids = json_decode( get_option( $get_option ) );
		if ( true === $select_unique_ids && ! empty( $get_ids ) ) {
			$get_ids = array_unique( $get_ids );
		}
		return $get_ids;
	}

	/**
	 * Function to get full path url to load
	 *
	 * @access public
	 * @param  string $file_path   // file loaction.
	 * @return string
	 */
	public function get_file_url( $file_path ) {
		$full_url = '';
		if ( $file_path ) {
			$full_url = plugins_url(
				'rtc-slider/' . $file_path,
				dirname( __FILE__ )
			);
		}
		return $full_url;
	}

	/**
	 * Function to get image link
	 *
	 * @access public
	 * @param  array $media_ids // id of media post.
	 * @return object
	 */
	public function rtc_slider_get_media_link( $media_ids = [] ) {
		if ( ! empty( $media_ids ) ) {
			$args = [
				'post__in'       => $media_ids,
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'any',
			];
			return new WP_Query( $args );
		}
		return false;
	}

	/**
	 * Function to get slider images data from wp query oobject
	 *
	 * @access public
	 * @param  object $wp_media_obj // instance of wp query.
	 * @return array
	 */
	public function rtc_slider_get_media_from_obj( $wp_media_obj ) {
		$all_media = [];
		if ( ! empty( $wp_media_obj ) && $wp_media_obj->have_posts() ) {
			while ( $wp_media_obj->have_posts() ) {
				$wp_media_obj->the_post();
				$all_media[ get_the_id() ] = get_the_guid();
			}
			wp_reset_postdata();
		}
		return $all_media;
	}

	/**
	 * Function to set images to media ids
	 *
	 * @access public
	 * @param  array $images_ids     // images ids to assign media links.
	 * @param  array $wp_media_links // link of media images.
	 * @return array
	 */
	public function rtc_slider_assign_url_to_ids( $images_ids, $wp_media_links ) {
		$full_img_url = [];
		if ( ! empty( $images_ids ) && ! empty( $wp_media_links ) ) {
			foreach ( $images_ids as $image ) {
				if ( isset( $wp_media_links[ $image ] ) ) {
					$full_img_url[] = [
						'link' => $wp_media_links[ $image ],
						'id'   => $image,
					];
				}
			}
		}
		return $full_img_url;
	}

	/**
	 * Function to get all images links
	 *
	 * @access public
	 * @param  string $option_name // get option details.
	 * @return array
	 */
	public function rtc_slider_images_url( $option_name = 'rtc_slider_images' ) {
		$media_links = [];
		// getting slider images data from database.
		$images       = $this->rtc_slider_get_images_ids( $option_name );
		$wp_media_img = $this->rtc_slider_get_media_link( $images );
		$get_urls     = $this->rtc_slider_get_media_from_obj( $wp_media_img );
		$media_links  = $this->rtc_slider_assign_url_to_ids( $images, $get_urls );
		return $media_links;
	}

}

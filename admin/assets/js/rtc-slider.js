/**
 * JS file to add rtc slider plugin related js code
 *
 * @package Rtc_Slider
 * @version 1.0
 *
 * @wordpress-plugin
 */

jQuery( 'document' ).ready(
	function($) {
		$().toastmessage( { stayTime: 5000 } );
		$( '#rtc-slider-upload-img' ).on(
			'click',
			function(e) {
				e.preventDefault();
				open_media_uploader_multiple_images();
			}
		);

		// function to handle selected slider images action.
		$(
			function () {
				$( "#rtc-slider-images" ).sortable(
					{
						connectWith: ".connectedSortable",
						stop: function() { updateImageOrder(); }
					}
				).disableSelection();
			}
		);

		// function to save.
		$( 'body' ).on(
			'click', '.remove-icon', function(){
				$( this ).parent( 'li' ).remove();
				// trigger function to re save all available images.
				updateImageOrder();
			}
		);

		// code to select all slider images to remove.
		$( 'body' ).on(
			'click', '.rtc-slider-remove', function () {
				if ($( '.rtc-slider-remove:checked' ).length == $( '.rtc-slider-remove' ).length) {
					$( '#rtc-select-all' ).prop( 'checked', true );
				} else {
					$( '#rtc-select-all' ).prop( 'checked', false );
				}
			}
		);
		$( '#rtc-select-all' ).on(
			'click', function () {
				if (this.checked) {
					$( '.rtc-slider-remove' ).each(
						function () {
							this.checked = true;
						}
					);
				} else {
					$( '.rtc-slider-remove' ).each(
						function () {
							this.checked = false;
						}
					);
				}
			}
		);

		// function to remove.
		$( '#rtc-remove-btn' ).on(
			'click', function(e) {
				e.preventDefault();
				var removeAct = $( '#rtc-bulk-action option:selected' ).val();
				if ( removeAct == "rtc-remove-call" ) {
					var allSelectedImg = [];
					// checking seleted images.
					$( '.rtc-slider-remove:checked' ).each(
						function(i, v) {
							var rtcli         = $( v ).parent( 'div' ).parent( 'li' );
							allSelectedImg[i] = rtcli.attr( 'data-img-id' );
							$( rtcli ).remove();
						}
					);
					// update if any item is selected.
					if ( allSelectedImg ) {
						updateImageOrder( 'remove' );
						$( '#rtc-select-all' ).prop( 'checked', false );
					} else {
						$().toastmessage( 'showNoticeToast', 'Select Images to remove!' );
					}
				} else {
					$().toastmessage( 'showNoticeToast', 'Select action from select box!' );
				}
			}
		);

		/**
		 * Function to update selected image orders
		 */
		function updateImageOrder ( rtcaction ) {
			var allSelectedImg = [];
			// prvious images order.
			var previousImgOrder = getInArray(
				$( '#rtc-slider-images' ).attr( 'data-images-id' )
			);
			// selected images for slider.
			allSelectedImg = checkImagesNOrder( previousImgOrder, 'rtc-slider-images' );
			// data to save selected slider images ids in database.
			var data = {
				'action'   : 'rtc_slider_save_image_id',
				'security' : $( '#rtc-jq-nonce' ).val(),
			};
			if (allSelectedImg ) {
				data.imagesIds = allSelectedImg;
			}
			if (typeof rtcaction != 'undefined') {
				data.rtcaction = rtcaction;
			}
			// sending post request to save data to database.
			rtc_slider_post_data( data );
			if ( allSelectedImg ) {
				// updating data value for selected images.
				$(
					'#rtc-slider-images'
				).attr(
					'data-images-id',
					allSelectedImg.join()
				);
			}
		}

		/**
		 * Function to get array from comma separated values.
		 *
		 * @param  string values
		 * @return void
		 */
		function getInArray( values ) {
			// check if previous images order not available.
			if (typeof values != 'undefined') {
				// make array of previous images array.
				values = values.split( "," );
			}
			return values;
		}

		/**
		 * Function to store data in database
		 */
		function rtc_slider_post_data( data ) {
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations.
			jQuery.post(
				ajaxurl,
				data,
				function (response) {
					$().toastmessage( response.data.class, response.data.message );
				}
			);
		}

		/**
		 * Function to check all selected or removed images and its order
		 *
		 * @param  array previousImgOrder
		 * @return array
		 */
		function checkImagesNOrder( previousImgOrder, section ) {
			// flag to set info about previous and current order of slider images.
			var isSame 		   = true;
			var allSelectedImg = [];
			// select only selected images.
			$(
				'ul#' + section + ' li.rtc-slide-thumb'
			).each(
				function (i, v) {
					allSelectedImg[i] = $( this ).attr( 'data-img-id' );
					if (
						typeof previousImgOrder == 'undefined' ||
						previousImgOrder[i] != $( this ).attr( 'data-img-id' )
					) {
						// un set flag if slider image order is change.
						isSame = false;
					}
				}
			);
			// if not equal then send new order.
			if ( ! isSame || typeof allSelectedImg == 'undefined' || typeof previousImgOrder == 'undefined' || ( previousImgOrder.length != allSelectedImg.length )) {
				return allSelectedImg;
			}
			return [];
		}

		// Start media uploader code.
		var media_uploader = null;

		function open_media_uploader_multiple_images() {
			media_uploader = wp.media(
				{
					button: {
						text: 'Add To Rtc Slider!',
						id  : 'hello-id'
					},
					multiple: true
				}
			);

			media_uploader.on(
				"select", function () {
					var length = media_uploader.state().get( "selection" ).length;
					var images = media_uploader.state().get( "selection" ).models

					images.map(
						function(v, i) {
							var image_url     = v.changed.url;
							var image_caption = v.changed.caption;
							var image_title   = v.changed.title;
							$( '#rtc-slider-images' ).append( '<li class="rtc-slide-thumb or-spacer" data-img-id="' + v.id + '"><div class="rtc-slide-row"><input type="checkbox" class="rtc-slider-remove"></div><div class="rtc-slide-row"><img class="rtc-full-wd" src="' + image_url + '"><div class="mask"></div></div></li>' );
						}
					);
					updateImageOrder();

				}
			);

			media_uploader.open();
		}
	}
);

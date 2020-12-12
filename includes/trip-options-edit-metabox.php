<?php
class Trip_Options_Edit_Metabox {

	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', array( __CLASS__, 'trip_options_add_metabox' ) );
		add_action( 'save_post', array( __CLASS__, 'trip_options_save_metabox' ), 10, 2 );
	}

	/**
	 * Add a new meta box for product
	 * Step 1. add_meta_box()
	 * Step 2. Callback function with meta box HTML
	 * Step 3. Save meta box data
	 */
	function trip_options_add_metabox() {
		add_meta_box(
			'trip-options', // metabox ID
			esc_html__( 'Trip Options', 'dgc-domain' ), // title
			//array( __CLASS__, 'trip_options_metabox_callback' ), // callback function
			array( __CLASS__, 'horizontal_tabs_metabox' ), // callback function
			//array( __CLASS__, 'vertical_example_metabox' ), // callback function
			'product', // post type or post types in array
			'normal', // position (normal, side, advanced)
			'default' // priority (default, low, high, core)
		);
		//wp_enqueue_script( 'mytabs', get_bloginfo( 'stylesheet_directory' ). '/mytabs.js', array( 'jquery-ui-tabs' ) );
		wp_enqueue_script( 'mytabs', 'mytabs.js', array( 'jquery-ui-tabs' ) );
		//wp_enqueue_script( 'mytabs', '', array( 'jquery-ui-tabs' ) );
	}
 
	function horizontal_tabs_metabox( $post ) {
		//$admin_tabs = new WP_Travel_Admin_Tabs();
		$admin_tabs = WP_Travel_Admin_Tabs::list_all();
		echo '{';
			foreach ( $admin_tabs as $key=>$values ) {
				echo $key.':{';
				foreach ( $values as $key=>$value ) {
					echo '{'.$key.':'.$value.'},';
				}
				echo '},';
			}
		echo '}';
		?>
		<div id="mytabs">
			<ul class="category-tabs">
				<li><a href="#tab_itinerary"><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></a></li>
<!--			<li><a href="#tab_prices_dates"><?php //esc_html_e( 'Prices & Dates', 'wp-travel' ); ?></a></li>  -->
				<li><a href="#tab_includes_excludes"><?php esc_html_e( 'Includes/Excludes', 'wp-travel' ); ?></a></li>
				<li><a href="#tab_facts"><?php esc_html_e( 'Facts', 'wp-travel' ); ?></a></li>
				<li><a href="#tab_gallery"><?php esc_html_e( 'Gallery', 'wp-travel' ); ?></a></li>
				<li><a href="#tab_locations"><?php esc_html_e( 'Locations', 'wp-travel' ); ?></a></li>
				<li><a href="#tab_faqs"><?php esc_html_e( 'FAQs', 'wp-travel' ); ?></a></li>
				<li><a href="#tab_misc"><?php esc_html_e( 'Misc. Options', 'wp-travel' ); ?></a></li>
				<li><a href="#tab_tabs"><?php esc_html_e( 'Tabs', 'wp-travel' ); ?></a></li>
			</ul>
			<br class="clear" />
			<div id="tab_itinerary">
				<?php self::trip_options_callback_itinerary( $post )?>
			</div>

			<div class="hidden" id="tab_prices_dates">
				<?php //self::trip_options_callback_prices_dates( $post )?>
			</div>

			<div class="hidden" id="tab_includes_excludes">
				<?php self::trip_options_callback_includes_excludes( $post )?>
			</div>
			
			<div class="hidden" id="tab_facts">
				<?php self::trip_options_metabox_callback( $post )?>
			</div>
			
			<div class="hidden" id="tab_gallery">
				<?php self::trip_options_metabox_callback( $post )?>
			</div>
			
			<div class="hidden" id="tab_locations">
				<?php self::trip_options_metabox_callback( $post )?>
			</div>
			
			<div class="hidden" id="tab_faqs">
				<?php self::trip_options_callback_faqs( $post )?>
			</div>
			
			<div class="hidden" id="tab_misc">
				<?php self::trip_options_metabox_callback( $post )?>
			</div>
			
			<div class="hidden" id="tab_tabs">
				<?php self::trip_options_callback_tabs( $post )?>
			</div>
		</div>
		<table style="width:100%">
			<tr>
				<td><?php esc_html_e( '* Please save the changes', 'wp-travel' ); ?></td>
				<td style="text-align:right"><button id="save-changes" style="background:blue;color:white" type="button"><?php esc_html_e( 'Save Changes', 'wp-travel' ); ?></button></td>
			</tr>
		</table>

		<script>
			jQuery(document).ready(function($) {
    			$("#mytabs .hidden").removeClass('hidden');
				$("#mytabs").tabs();
				
				$("#save-changes").click( function(){
					$.ajax({
                		type: 'POST',
                		url: ajax_url,
                		data: {
                    		action:         'wpt_query_table_load_by_args',
                    		temp_number:    temp_number,
                    		targetTableArgs:targetTableArgs,
                    		pageNumber:     pageNumber,
                    		load_type:      load_type,
                		},
                		complete: function() {
                    		$( document ).trigger( 'wc_fragments_refreshed' );
                    		arrangingTDContentForMobile(); //@Since 5.2
                    		loadMiniFilter(); //@Since 4.8                    
                    		fixAfterAjaxLoad();
                		},
                		success: function(data) {
                    		targetTableBody.html(data);
                    		targetTableBody.css('opacity','1');
                    
                    		var $data = {
                        		action:         'wpt_ajax_paginate_links_load',
                        		temp_number:    temp_number,
                        		targetTableArgs:targetTableArgs, 
                        		pageNumber:     pageNumber,
                        		load_type:      load_type,
                    		};
                    
                    		loadPaginationLinks($data,temp_number);

                    		removeCatTagLings();//Removing Cat,tag link, if eanabled from configure page
                    		updateCheckBoxCount(temp_number); //Selection reArrange 
                    		uncheckAllCheck(temp_number);//Uncheck All CheckBox after getting New pagination
                    		emptyInstanceSearchBox(temp_number);//CleanUp or do empty Instant Search

                    		pageNumber++; //Page Number Increasing 1 Plus
                    		targetTable.attr('data-page_number',pageNumber);
                		},
                		error: function() {
                    		console.log("Error On Ajax Query Load. Please check console.");
                		},
            		});
					$.ajax({
            			url:"test.php",    //the page containing php script
            			type: "post",    //request type,
            			dataType: 'json',
            			data: {registration: "success", name: "xyz", email: "abc@gmail.com"},
            			success:function(result){
                			console.log(result.abc);
            			}
        			});
				});
			});
		</script>
		<?php
	}

	function trip_options_metabox_callback( $post ) {

		echo "<h3>Add Your Content Here</h3>";
		$content = get_post_meta($post->ID, 'custom_editor', true);
		
		//This function adds the WYSIWYG Editor 
		wp_editor ( $content , 'custom_editor', array ( "media_buttons" => true ) );
		
		/**
		 * Retrieves a post meta field for the given post ID.
		 * get_post_meta( int $post_id, string $key = '', bool $single = false )
		 */
		$seo_title = get_post_meta( $post->ID, 'seo_title', true );
		$seo_robots = get_post_meta( $post->ID, 'seo_robots', true );
 
		// nonce, actually I think it is not necessary here
		wp_nonce_field( 'somerandomstr', '_tripnonce' );
 
		echo '<table class="form-table">
			<tbody>
				<tr>
					<th><label for="seo_title">SEO title</label></th>
					<td><input type="text" id="seo_title" name="seo_title" value="' . esc_attr( $seo_title ) . '" class="regular-text"></td>
				</tr>
				<tr>
					<th><label for="seo_tobots">SEO robots</label></th>
					<td>
						<select id="seo_robots" name="seo_robots">
							<option value="">Select...</option>
							<option value="index,follow"' . selected( 'index,follow', $seo_robots, false ) . '>Show for search engines</option>
							<option value="noindex,nofollow"' . selected( 'noindex,nofollow', $seo_robots, false ) . '>Hide for search engines</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>';
 
	}


	/**
	 * Itinerary metabox callback
	 */
	function trip_options_callback_itinerary( $post ) {
		if ( ! $post ) {
			return;
		}
		$trip_code = wp_travel_get_trip_code( $post->ID );
		$trip_outline = get_post_meta( $post->ID, 'wp_travel_outline', true );
		$itineraries = get_post_meta( $post->ID, 'wp_travel_trip_itinerary_data', true );
/*	
		echo '$post->ID = ' . $post->ID;
		echo '{';
			foreach ( $itineraries as $key=>$values ) {
				echo $key.':{';
				foreach ( $values as $key=>$value ) {
					echo '{'.$key.':'.$value.'},';
				}
				echo '},';
			}
		echo '}';
*/
		?>
		<table style="width:100%" class="form-table trip-info">
			<tr>
				<td>
					<?php esc_html_e( 'Trip Code : ', 'wp-travel' ); ?>
					<input type="text" id="wp-travel-trip-code" disabled="disabled" value="<?php echo esc_attr( $trip_code ); ?>" />
				</td>
			</tr>

		<?php
		$remove_itinerary = __( "- Remove Itinerary", "wp-travel" );
		$xx = 0;
		if ( is_array( $itineraries ) && count( $itineraries ) > 0 ) {
			foreach ( $itineraries as $itinerary ) {
				$xx++;
			}
		} else {?>
			<tr class="no-itineraries"><td colspan="2">
				<span><h3><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></h3></span><br>
				<span><?php esc_html_e( 'No Itineraries found.', 'wp-travel' ); ?></span>
				<span id="first-itinerary"><?php esc_html_e( 'Add Itinerary', 'wp-travel' ); ?></span>
			</td></tr><?php
		}?>

			<tr style="display:none" class="init-rows">
				<td><h3><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></h3></td>
				<td style="text-align:right"><button id="add-itinerary" type="button"><?php esc_html_e( '+ Add Itinerary', 'wp-travel' ); ?></button></td>
			</tr>
			
			<tr style="display:none" class="init-rows"><td colspan="2">
				<ul id="itineraries-ul"><?php			  
				for ($x = 0; $x < 100; $x++) {
					echo '<li class="itinerary-li" id="itinerary-li-' . $x . '">';
					if ($xx<=0) {
						$itinerary_label = DEFAULT_ITINERARY_LABEL;
						$itinerary_title = DEFAULT_ITINERARY_TITLE;
					} else {
						$itinerary_label = esc_attr( $itineraries[$x]['label'] );
						$itinerary_title = esc_attr( $itineraries[$x]['title'] );
					}
					$xx--;
					echo '<span class="fas fa-bars"> </span>';
					echo '<span class="span-label">' . $itinerary_label . '</span>, ';
					echo '<span class="span-title">' . $itinerary_title . '</span>';
					echo '<p style="display:none">' . $x . '</p>';
					echo '
					<table class="update-itinerary" style="width:100%">
				  	  <tbody>
						<tr>
							<th>Itinerary label</th>
							<td><input type="text" class="item-label" name="itinerary_item_label-' . $x . '" value="' . $itinerary_label . '"></td>
						</tr>
						<tr>
							<th>Itinerary title</th>
							<td><input type="text" class="item-title" name="itinerary_item_title-' . $x . '" value="' . $itinerary_title . '"></td>
						</tr>
						<tr>
							<th>Itinerary date</th>
							<td><input type="text" class="item_date" name="itinerary_item_date-' . $x . '" value="' . esc_attr( $itineraries[$x]['date'] ) . '"></td>
						</tr>
						<tr>
							<th>Itinerary time</th>
							<td><input type="text" class="item_time" name="itinerary_item_time-' . $x . '" value="' . esc_attr( $itineraries[$x]['time'] ) . '"></td>
						</tr>
						<tr>
							<td colspan="2"><b>Description</b><br>
							<textarea rows="3" style="width:100%" name="itinerary_item_desc-' . $x . '">' . esc_attr( $itineraries[$x]['desc'] ) . '</textarea></td>
						</tr>
						<tr>
							<th><label for="itinerary_item_robots">Itinerary robots</label></th>
							<td>
								<select id="itinerary_item_robots" name="itinerary_item_robots-' . $x . '">
									<option value="">Home Stay ...</option>
									<option value="index,follow"' . selected( 'index,follow', $itineraries[$x]['label'], false ) . '>Show for search engines</option>
									<option value="noindex,nofollow"' . selected( 'noindex,nofollow', $itineraries[$x]['label'], false ) . '>Hide for search engines</option>
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
							<td class="remove-itinerary" style="text-align:right"><button id="remove-itinerary-' . $x . '" style="color:red" type="button">' . $remove_itinerary . '</button></td>
						</tr>
				  	  </tbody>
					</table>
			  		</li>';
				}?>			
				</ul>
			</td></tr>

			<tr style="display:none" class="init-rows">
				<td></td>
				<td style="text-align:right"><button id="add-itinerary" type="button"><?php esc_html_e( "+ Add Itinerary", "wp-travel" ); ?></button></td>
			</tr>
		</table>

		<script>
			jQuery(document).ready(function($) {
    			//$( "#itineraries-ul" ).sortable();
				//$( "#itineraries-ul" ).disableSelection();
				$( ".itinerary-li" ).hide();

				$( ".itinerary-li" ).each( function( index, element ) {
					if ( !$( 'p', element ).is(":empty") ) {
						$( ".init-rows" ).show();
						$( element ).show();
						$( element ).delegate("span", "click", function(){
							$( 'table', element ).toggleClass('toggle-access');
						});
					};

					$( element ).delegate(".item-label", "keyup", function(){
						$( '.span-label', element ).text($(this).val());
					});
					$( element ).delegate(".item-title", "keyup", function(){
						$( '.span-title', element ).text($(this).val());
					});
				});

				$( ".remove-itinerary" ).each( function( index, element ) {
					$( element ).delegate("button", "click", function(){
						$( this ).closest('.itinerary-li').remove();
					});					
				});

				$("#first-itinerary").click( function(){
					$(".no-itineraries").hide();
					$(".init-rows").show();
					$(".itinerary-li").hide();
					$("#itinerary-li-0").show();
					$('span','#itinerary-li-0').on('click', function() {
						$('table','#itinerary-li-0').toggleClass('toggle-access');
					});
				} );
			
				$("#add-itinerary").click( function(){
					$( ".itinerary-li" ).each( function( index, element ) {
						if ( $( this ).is(":hidden") ) {
							$( this ).show();
							$( element ).delegate("span", "click", function(){
								$( 'table', element ).toggleClass('toggle-access');
							});
							return false;
						};
					});
				} );

				$( '.item_date' ).datepicker();
				$( '.item_time' ).timepicker({format: 'HH:mm'});
			} );
		</script>
	
		<style>
  			#itineraries-ul { list-style-type:none; margin:0; padding:0; width:100%; }
  			#itineraries-ul li { background:#f2f2f2; border:1px solid #ccc; margin:0 3px 3px 3px; padding:0.4em; padding-left:1.5em; font-size:1.4em; }
			#itineraries-ul li span { cursor:pointer; }
			#itineraries-ul li .fas.fa-bars { margin-left:-1.3em; }
			#itineraries-ul li .fas.fa-bars:before { content: "\f0c9"; }
			/*#itineraries-ul li span { margin-left:-1.3em; cursor:pointer; }*/
			#itineraries-ul li table { background:#ffffff; border:1px solid #ccc; width:100%; display:none; margin-left:-1.2em; padding-left:1.5em; }
			#itineraries-ul li .toggle-access { display:block; }
			#first-itinerary { color:blue; text-decoration:underline; cursor:pointer;}
			.item-title { width:100%;}
			/*i.fas*/
			/*.fa-bars:before { content: "\f0c9"; }*/
  		</style>
		<?php
	}

	/**
	 * Prices & Dates metabox callback
	 */
	function trip_options_callback_prices_dates( $post ) {
		if ( ! $post ) {
			return;
		}
		$trip_data = WP_Travel_Helpers_Trips::get_trip( $post->ID );
		$pricings = $trip_data['trip']['pricings'];
		echo '$post->ID = ' . $post->ID;
		echo '{';
			foreach ( $pricings as $key=>$values ) {
				echo $key.':{';
				foreach ( $values as $key=>$value ) {
					echo '{'.$key.':'.$value.'},';
				}
				echo '},';
			}
		echo '}';

		?>
		<table style="width:100%" class="form-table trip-info">
		<?php
		$remove_pricing = __( "- Remove Price", "wp-travel" );
		$xx = 0;
		if ( is_array( $pricings ) && count( $pricings ) > 0 ) {
			foreach ( $pricings as $pricing ) {
				$xx++;
			}
		} else {?>
			<tr class="no-pricings"><td colspan="2">
				<span><?php esc_html_e( 'No Pricings found.', 'wp-travel' ); ?></span>
				<span id="first-pricing"><?php esc_html_e( 'Add Price', 'wp-travel' ); ?></span>
			</td></tr><?php
		}?>

			<tr style="display:none" class="init-rows">
				<td></td>
				<td style="text-align:right"><button id="add-pricing" type="button"><?php esc_html_e( '+ Add Price', 'wp-travel' ); ?></button></td>
			</tr>
			
			<tr style="display:none" class="init-rows"><td colspan="2">
				<ul id="pricings-ul"><?php
				for ($x = 0; $x < 100; $x++) {
					echo '<li class="pricing-li" id="pricing-li-' . $x . '"><span><i class="fas fa-bars"></i>';
					if ($xx<=0) {
						$pricing_title = DEFAULT_PRICING;
						echo $pricing_title . '</span><p style="display:none"></p>';
					} else {
						$pricing_title = esc_attr( $pricings[$x]['title'] );
						echo $pricing_title . '</span><p style="display:none">' . $x . '</p>';
					}
					$xx--;
					echo '
					<table class="update-pricing" style="width:100%">
				  	  <tbody>
						<tr>
							<th>Pricing Name</th>
							<td><input type="text" class="item-title" name="pricing_item_title-' . $x . '" value="' . $pricing_title . '"></td>
						</tr>
						<tr>
							<th>Min. Pax</th>
							<td><input type="text" name="pricing_min_pax-' . $x . '" value="' . esc_attr( $pricings[$x]['min_pax'] ) . '"></td>
						</tr>
						<tr>
							<th>Max. Pax</th>
							<td><input type="text" name="pricing_max_pax-' . $x . '" value="' . esc_attr( $pricings[$x]['max_pax'] ) . '"></td>
						</tr>
						<tr>
							<th>Price Categories</th>
							<td></td>
						</tr>
						<tr>
							<th>' . esc_html_e( 'No Categories found.', 'wp-travel' ) . '</th>
							<td></td>
						</tr>
						<tr>
							<th>Trip Extras</th>
							<td></td>
						</tr>
						<tr>
							<th>' . esc_html_e( 'Please add extras first', 'wp-travel' ) . '</th>
							<td></td>
						</tr>
						<tr>
							<td></td>
							<td class="remove-pricing" style="text-align:right"><button id="remove-pricing-' . $x . '" style="color:red" type="button">' . $remove_pricing . '</button></td>
						</tr>
				  	  </tbody>
					</table>
			  		</li>';
				}?>			
				</ul>
			</td></tr>
		</table>

		<script>
			jQuery(document).ready(function($) {
    			//$( "#itineraries-ul" ).sortable();
				//$( "#itineraries-ul" ).disableSelection();
				$( ".pricing-li" ).hide();

				$( ".pricing-li" ).each( function( index, element ) {
					if ( !$( 'p', element ).is(":empty") ) {
						$( ".init-rows" ).show();
						$( element ).show();
						$( element ).delegate("span", "click", function(){
							$( 'table', element ).toggleClass('toggle-access');
						});
					};

					$( element ).delegate(".item-title", "keyup", function(){
						$( 'span', element ).text($(this).val());
					});
				});

				$( ".remove-pricing" ).each( function( index, element ) {
					$( element ).delegate("button", "click", function(){
						$( this ).closest('.pricing-li').remove();
					});					
				});

				$("#first-pricing").click( function(){
					$(".no-pricings").hide();
					$(".init-rows").show();
					$(".pricing-li").hide();
					$("#pricing-li-0").show();
					$('span','#pricing-li-0').on('click', function() {
						$('table','#pricing-li-0').toggleClass('toggle-access');
					});
				} );
			
				$("#add-pricing").click( function(){
					$( ".pricing-li" ).each( function( index, element ) {
						if ( $( this ).is(":hidden") ) {
							$( this ).show();
							$( element ).delegate("span", "click", function(){
								$( 'table', element ).toggleClass('toggle-access');
							});
							return false;
						};
					});
				} );

				$( '.pricing_item_date' ).datepicker();
			} );
		</script>
	
		<style>
  			#pricings-ul { list-style-type:none; margin:0; padding:0; width:100%; }
  			#pricings-ul li { background:#f2f2f2; border:1px solid #ccc; margin:0 3px 3px 3px; padding:0.4em; padding-left:1.5em; font-size:1.4em; }
			#pricings-ul li span { margin-left:-1.3em; cursor:pointer; }
			#pricings-ul li table { background:#ffffff; border:1px solid #ccc; width:100%; display:none; margin-left:-1.2em; padding-left:1.5em; }
			#pricings-ul li .toggle-access { display:block; }
			#first-pricing { color:blue; text-decoration:underline; cursor:pointer;}
			/*i.fas*/
			.fa-bars:before { content: "\f0c9"; }
  		</style>
		<?php
	}

	/**
	 * Includes/Excludes metabox callback
	 */
	function trip_options_callback_includes_excludes( $post ) {
		if ( ! $post ) {
			return;
		}
		$trip_include = get_post_meta( $post->ID, 'wp_travel_trip_include', true );
		$trip_exclude = get_post_meta( $post->ID, 'wp_travel_trip_exclude', true );
		echo '<h3>';
		esc_html_e( 'Trip Includes', 'wp-travel' );
		echo '</h3>';
		wp_editor ( $trip_include , 'wp_travel_trip_include', array ( "media_buttons" => true ) );
		echo '<br>';
		echo '<br>';

		echo '<h3>';
		esc_html_e( 'Trip Excludes', 'wp-travel' );
		echo '</h3>';
		wp_editor ( $trip_exclude , 'wp_travel_trip_exclude', array ( "media_buttons" => true ) );
	}


	/**
	 * FAQs metabox callback
	 *
	 * @param  Object $post Post object.
	 */
	function trip_options_callback_faqs( $post ) {
		if ( ! $post ) {
			return;
		}
		$faqs = wp_travel_get_faqs( $post->ID );

		$remove_faq = __( "- Remove FAQ", "wp-travel" );
		$xx = 0;
		?>
		<table style="width:100%" class="form-table">
			<tr style="display:none" class="faq-init-rows">
				<td><h3><?php esc_html_e( 'FAQ', 'wp-travel' ); ?></h3></td>
				<td style="text-align:right"><button id="add-faq" type="button"><?php esc_html_e( '+ Add FAQ', 'wp-travel' ); ?></button></td>
			</tr>

		<?php
		if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
			foreach ( $faqs as $x=>$faq ) {
				$xx++;
			}
		} else {?>
			<tr class="no-faqs"><td colspan="2">
				<span><h3><?php esc_html_e( 'FAQ', 'wp-travel' ); ?></h3></span><br>
				<span><?php esc_html_e( 'Please add new FAQ here.', 'wp-travel' ); ?></span>
				<span id="first-faq"><?php esc_html_e( 'Add FAQ', 'wp-travel' ); ?></span>
			</td></tr><?php
		}?>

			<tr style="display:none" class="faq-init-rows"><td colspan="2">
				<ul id="faqs-ul"><?php			  
				for ($x = 0; $x < 100; $x++) {
					echo '<li class="faq-li" id="faq-li-' . $x . '"><span><i class="fas fa-bars"></i>';
					if ($xx<=0) {
						$faq_question = DEFAULT_FAQ_QUESTION;
						echo $faq_question . '</span><p style="display:none"></p>';
					} else {
						$faq_question = esc_attr( $faqs[$x]['question'] );
						echo $faq_question . '</span><p style="display:none">' . $x . '</p>';
					}
					$xx--;
					echo '
					<table class="update-faq" style="width:100%">
				  	  <tbody>
						<tr>
							<th>Enter your question</th>
							<td><input type="text" width="100%" class="item-title" name="faq_item_question-' . $x . '" value="' . $faq_question . '" class="regular-text"></td>
						</tr>
						<tr>
							<th>Your answer</th>
							<td><textarea rows="3" name="faq_item_answer-' . $x . '" class="regular-text">' . esc_attr( $faqs[$x]['answer'] ) . '</textarea></td>
						</tr>
						<tr>
							<td></td>
							<td class="remove-faq" style="text-align:right"><button id="remove-faq-' . $x . '" style="color:red" type="button">' . $remove_faq . '</button></td>
						</tr>
				  	  </tbody>
					</table>
			  		</li>';
				}?>			
				</ul>
			</td></tr>

			<tr style="display:none" class="faq-init-rows">
				<td></td>
				<td style="text-align:right"><button id="add-faq" type="button"><?php esc_html_e( "+ Add FAQ", "wp-travel" ); ?></button></td>
			</tr>
		</table>

		<script>
			jQuery(document).ready(function($) {
    			//$( "#sortable" ).sortable();
				//$( "#sortable" ).disableSelection();
				$( ".faq-li" ).hide();

				$( ".faq-li" ).each( function( index, element ) {
					if ( !$( 'p', element ).is(":empty") ) {
						$( ".faq-init-rows" ).show();
						$( element ).show();
						$( element ).delegate("span", "click", function(){
							$( 'table', element ).toggleClass('toggle-access');
						});
					};

					$( element ).delegate(".item-title", "keyup", function(){
						$( 'span', element ).text($(this).val());
					});
				});

				$( ".remove-faq" ).each( function( index, element ) {
					$( element ).delegate("button", "click", function(){
						$( this ).closest('.faq-li').remove();
					});						
				});

				$("#first-faq").click( function(){
					$(".no-faqs").hide();
					$(".faq-init-rows").show();
					$(".faq-li").hide();
					$("#faq-li-0").show();
					$('span','#faq-li-0').on('click', function() {
						$('table','#faq-li-0').toggleClass('toggle-access');
					});
				} );
			
				$("#add-faq").click( function(){
					$( ".faq-li" ).each( function( index, element ) {
						if ( $( this ).is(":hidden") ) {
							$( this ).show();
							$( element ).delegate("span", "click", function(){
								$( 'table', element ).toggleClass('toggle-access');
							});
							return false;
						};
					});
				} );

				$( '.faq_item_date' ).datepicker();
			} );
		</script>
	
		<style>
  			#faqs-ul { list-style-type:none; margin:0; padding:0; width:100%; }
  			#faqs-ul li { background:#f2f2f2; border:1px solid #ccc; margin:0 3px 3px 3px; padding:0.4em; padding-left:1.5em; font-size:1.4em; }
			#faqs-ul li span { margin-left:-1.3em; cursor:pointer; }
			#faqs-ul li table { background:#ffffff; border:1px solid #ccc; width:100%; display:none; margin-left:-1.2em; padding-left:1.5em; }
			#faqs-ul li .toggle-access { display:block; }
			#first-faq { color:blue; text-decoration:underline; cursor:pointer;}
			.fa-bars:before { content: "\f0c9"; }
  		</style>
		<?php		
	}

	/**
	 * Tabs metabox callback
	 */
	function trip_options_callback_tabs( $post ) {
		if ( ! $post ) {
			return;
		}
		$trip_tabs = wp_travel_get_admin_trip_tabs( $post->ID );

		echo '$post->ID = ' . $post->ID;
		echo '{';
			foreach ( $trip_tabs as $key=>$values ) {
				echo $key.':{';
				foreach ( $values as $key=>$value ) {
					echo '{'.$key.':'.$value.'},';
				}
				echo '},';
			}
		echo '}';

		?>
		<ul id="tabs-ul" style="width:100%" >
		<?php
		if ( is_array( $trip_tabs ) && count( $trip_tabs ) > 0 ) {
			foreach ( $trip_tabs as $key=>$value ) {
				echo '<li class="tab-li" id="tab-li-' . $key . '"><span class="fas fa-bars">';
				$tab_label = esc_attr( $trip_tabs[$key]['label'] );
				echo $tab_label . '</span><p style="display:none">' . $key . '</p>';

				echo '
				<table class="update-tab" style="width:100%">
					<tbody>
					<tr>
						<th>Default Trip Title</th>
						<td><input type="text" name="tab_item_default-' . $key . '" value="' . esc_attr( $trip_tabs[$key]['label'] ) . '"></td>
					</tr>
					<tr>
						<th>Custom Trip Title</th>
						<td><input type="text" class="item-title" name="tab_item_custom-' . $key . '" value="' . $tab_label . '"></td>
					</tr>
					<tr>
						<th>Display</th>
						<td><input type="checkbox" checked name="tab_item_show_in_menu-' . $key . '" value="' . esc_attr( $trip_tabs[$key]['show_in_menu'] ) . '"></td>
					</tr>
					</tbody>
				</table>
				</li>';

			}
		}?>			
		</ul>

		<script>
			jQuery(document).ready(function($) {
    			$( "#tabs-ul" ).sortable();
				$( "#tabs-ul" ).disableSelection();
				$( ".tab-li" ).hide();

				$( ".tab-li" ).each( function( index, element ) {
					if ( !$( 'p', element ).is(":empty") ) {
						$( ".init-rows" ).show();
						$( element ).show();
						$( element ).delegate("span", "click", function(){
							$( 'table', element ).toggleClass('toggle-access');
						});
					};

					$( element ).delegate(".item-title", "keyup", function(){
						$( 'span', element ).text($(this).val());
					});
				});
			} );
		</script>
	
		<style>
  			#tabs-ul { list-style-type:none; margin:0; padding:0; width:100%; }
  			#tabs-ul li { background:#f2f2f2; border:1px solid #ccc; margin:0 3px 3px 3px; padding:0.4em; padding-left:1.5em; font-size:1.4em; }
			#tabs-ul li span { margin-left:-1.3em; cursor:pointer; }
			#tabs-ul li table { background:#ffffff; border:1px solid #ccc; width:100%; display:none; margin-left:-1.2em; padding-left:1.5em; }
			#tabs-ul li .toggle-access { display:block; }
			/*#first-tab { color:blue; text-decoration:underline; cursor:pointer;}*/
			/*.fa-bars:before { content: "\f0c9"; }*/
  		</style>
		<?php
	}

	function trip_options_save_metabox( $post_id, $post ) {
	
		// nonce check
		if ( ! isset( $_POST[ '_tripnonce' ] ) || ! wp_verify_nonce( $_POST[ '_tripnonce' ], 'somerandomstr' ) ) {
			return $post_id;
		}

		// check current use permissions
		$post_type = get_post_type_object( $post->post_type );

		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		// Do not save the data if autosave
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// define your own post type here
		if( $post->post_type != 'product' ) {
			return $post_id;
		}
	
		/**
		 * Updates a post meta field based on the given post ID.
		 * update_post_meta( int $post_id, string $meta_key, mixed $meta_value, mixed $prev_value = '' )
		 */

		$itineraries = array();
		$xx = 0;
		for ($x = 0; $x < 100; $x++) {
			//if ($_POST['itinerary_item_label-' . $x]!="" && $_POST['itinerary_item_label-' . $x] != DEFAULT_ITINERARY_LABEL && $_POST['itinerary_item_title-' . $x]!="" && $_POST['itinerary_item_title-' . $x] != DEFAULT_ITINERARY_TITLE) {
			if ($_POST['itinerary_item_label-' . $x]!="" && $_POST['itinerary_item_label-' . $x] != DEFAULT_ITINERARY_LABEL) {
				$itineraries[$xx]['label'] = sanitize_text_field( $_POST['itinerary_item_label-' . $x] );
				$itineraries[$xx]['title'] = sanitize_text_field( $_POST['itinerary_item_title-' . $x] );
				$itineraries[$xx]['date'] = sanitize_text_field( $_POST['itinerary_item_date-' . $x] );
				$itineraries[$xx]['time'] = sanitize_text_field( $_POST['itinerary_item_time-' . $x] );
				$itineraries[$xx]['desc'] = sanitize_text_field( $_POST['itinerary_item_desc-' . $x] );
				$xx++;
			}
		}
		//delete_post_meta( $post_id, 'wp_travel_trip_itinerary_data' );
		update_post_meta( $post_id, 'wp_travel_trip_itinerary_data', $itineraries );

		$pricings = array();
		$xx = 0;
		for ($x = 0; $x < 100; $x++) {
			if ($_POST['pricing_item_title-' . $x]!="" && $_POST['pricing_item_title-' . $x] != DEFAULT_PRICING) {
				$pricings[$xx]['title'] = sanitize_text_field( $_POST['pricing_item_title-' . $x] );
				$pricings[$xx]['min_pax'] = sanitize_text_field( $_POST['pricing_min_pax-' . $x] );
				$pricings[$xx]['max_pax'] = sanitize_text_field( $_POST['pricing_max_pax-' . $x] );
				$xx++;
			}
		}

		if (!empty($_POST['wp_travel_trip_include'])) {
			$includes = sanitize_text_field( $_POST['wp_travel_trip_include'] );
			update_post_meta($post_id, 'wp_travel_trip_include', $includes);
		}

		if (!empty($_POST['wp_travel_trip_exclude'])) {
			$excludes = sanitize_text_field( $_POST['wp_travel_trip_exclude'] );
			update_post_meta($post_id, 'wp_travel_trip_exclude', $excludes);
		}

		$faqs = array();
		$xx = 0;
		for ($x = 0; $x < 100; $x++) {
			if ($_POST['faq_item_question-' . $x] != "" && $_POST['faq_item_question-' . $x] != DEFAULT_FAQ_QUESTION) {
				$faqs['question'][$xx] = sanitize_text_field( $_POST['faq_item_question-' . $x] );
				$faqs['answer'][$xx] = sanitize_text_field( $_POST['faq_item_answer-' . $x] );
				$xx++;
			}
		}
		$question = isset( $faqs['question'] ) ? $faqs['question'] : array();
		$answer   = isset( $faqs['answer'] ) ? $faqs['answer'] : array();
		update_post_meta( $post_id, 'wp_travel_faq_question', $question );
		update_post_meta( $post_id, 'wp_travel_faq_answer', $answer );

		// Create Category
		wp_insert_term(
			__( "Itinerary", "wp-travel" ), // the term 
			'product_cat', // the taxonomy
			array(
	  			'description'=> __( "Category of Itinerary", "wp-travel" ),
	  			'slug' => 'itinerary'
			)
  		);

		// Gets term object from Category in the database. 
		$term = get_term_by('name', __( "Itinerary", "wp-travel" ), 'product_cat');
		wp_set_object_terms( $post_id, $term->term_id, 'product_cat' );

/*
		if( isset( $_POST[ 'seo_robots' ] ) ) {
			update_post_meta( $post_id, 'seo_robots', sanitize_text_field( $_POST[ 'seo_robots' ] ) );
		} else {
			delete_post_meta( $post_id, 'seo_robots' );
		}
*/
		return $post_id;
	}
}
new Trip_Options_Edit_Metabox;


/**
 * Add a custom Product Data tab
 */
add_filter( 'woocommerce_product_data_tabs', 'wk_custom_product_tab', 10, 1 );
function wk_custom_product_tab( $default_tabs ) {
    $default_tabs['custom_tab'] = array(
        'label'   =>  __( 'Itineraries', 'domain' ),
        'target'  =>  'wk_custom_tab_data',
        'priority' => 60,
        'class'   => array()
    );
    return $default_tabs;
}

add_action( 'woocommerce_product_data_panels', 'wk_custom_tab_data' );
function wk_custom_tab_data() {
   echo '<div id="wk_custom_tab_data" class="panel woocommerce_options_panel">// add content here</div>';
}

function vertical_example_metabox( $post ) {
	?>
	<div id="tabs">
		<ul class="category-tabs">
			<li><a href="#tab1"><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></a></li>
			<li><a href="#tab2"><?php esc_html_e( 'Prices & Dates', 'wp-travel' ); ?></a></li>
			<li><a href="#tab3"><?php esc_html_e( 'Includes/Excludes', 'wp-travel' ); ?></a></li>
			<li><a href="#tab4"><?php esc_html_e( 'Facts', 'wp-travel' ); ?></a></li>
			<li><a href="#tab5"><?php esc_html_e( 'Gallery', 'wp-travel' ); ?></a></li>
			<li><a href="#tab6"><?php esc_html_e( 'Locations', 'wp-travel' ); ?></a></li>
			<li><a href="#tab7"><?php esc_html_e( 'FAQs', 'wp-travel' ); ?></a></li>
			<li><a href="#tab8"><?php esc_html_e( 'Misc. Options', 'wp-travel' ); ?></a></li>
			<li><a href="#tab9"><?php esc_html_e( 'Tabs', 'wp-travel' ); ?></a></li>
		</ul>
		<br class="clear" />
		<div id="tab1">
			<?php wp_travel_trip_info( $post )?>
		</div>

		<div class="hidden" id="tab2">
			<?php wp_travel_trip_info( $post )?>				
		</div>

		<div class="hidden" id="tab3">
			<?php wp_travel_trip_info( $post )?>
		</div>
		
		<div class="hidden" id="tab4">
			<?php wp_travel_trip_info( $post )?>
		</div>
		
		<div class="hidden" id="tab5">
			<?php wp_travel_trip_info( $post )?>
		</div>
		
		<div class="hidden" id="tab6">
			<?php wp_travel_trip_info( $post )?>
		</div>
		
		<div class="hidden" id="tab7">
			<?php wp_travel_trip_info( $post )?>
		</div>
		
		<div class="hidden" id="tab8">
			<?php wp_travel_trip_info( $post )?>
		</div>
		
		<div class="hidden" id="tab9">
			<?php wp_travel_trip_info( $post )?>
		</div>
	</div>

	<script>
		jQuery(document).ready(function($) {
			$( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
			$( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
		});
	</script>
	<style>
		.ui-tabs-vertical { width: 55em; }
		.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
		.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
		.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
		.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
		.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
	</style>
	<?php
}


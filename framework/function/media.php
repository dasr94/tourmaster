<?php
	/*	
	*	Tourmaster Media File
	*	---------------------------------------------------------------------
	*	This file contains media function in the theme
	*	---------------------------------------------------------------------
	*/

	// get image from image id/url
	if( !function_exists('tourmaster_get_image_url') ){
		function tourmaster_get_image_url( $image, $size = 'full', $placeholder = true){
			if( is_numeric($image) ){
				$image_src = wp_get_attachment_image_src($image, $size);
				if( !empty($image_src) ) return $image_src[0];
			}else if( !empty($image) ){
				return $image;
			}
		}
	}

	if( !function_exists('tourmaster_get_image') ){
		function tourmaster_get_image( $image, $size = 'full', $settings = array() ){

			$ret = '';

			// get_image section
			if( is_numeric($image) ){
				$alt_text = get_post_meta($image , '_wp_attachment_image_alt', true);	
				$image_src = wp_get_attachment_image_src($image, $size);

				if( !empty($image_src) ){
					$srcset = '';
					if( function_exists('gdlr_core_get_image_srcset') ){
						$srcset = gdlr_core_get_image_srcset($image, $image_src);
					}

					if( !empty($srcset) ){
						$ret .= '<img ' . $srcset . ' alt="' . esc_attr($alt_text) . '" />';
					}else{
						$ret .= '<img src="' . esc_url($image_src[0]) . '" alt="' . esc_attr($alt_text) . '" width="' . esc_attr($image_src[1]) .'" height="' . esc_attr($image_src[2]) . '" />';
					}
				}else{
					return;
				}
			}else if( !empty($image) ){
				$ret .= '<img src="' . esc_url($image) . '" alt="" />';
			}

			// apply link
			if( !empty($settings['link']) ){
				$ret  = '<a href="' . esc_url($settings['link']) . '" ' . 
					(empty($settings['link-target'])? '': 'target="' . esc_attr($settings['link-target']) . '"') . ' >' . $ret . '</a>';

			}

			return $ret;
		}
	}

	// get video from url
	if( !function_exists('tourmaster_get_video') ){
		function tourmaster_get_video( $video, $size = 'full', $atts = array() ){
			
			$size = tourmaster_get_video_size($size);
			
			// video shortcode
			if( preg_match('#^\[video\s.+\[/video\]#', $video, $match) ){ 
				return do_shortcode($match[0]);
				
			// embed shortcode
			}else if( preg_match('#^\[embed.+\[/embed\]#', $video, $match) ){ 
				global $wp_embed; 
				return $wp_embed->run_shortcode($match[0]);
				
			// youtube link
			}else if( strpos($video, 'youtube') !== false || strpos($video, 'youtu.be') !== false ){
				if( strpos($video, 'youtube') !== false ){
					preg_match('#[?&]v=([^&]+)(&.+)?#', $video, $id);
				}else{
					preg_match('#youtu.be\/([^?&]+)#', $video, $id);
				}
				$id[2] = empty($id[2])? '': $id[2];
				$url = '//www.youtube.com/embed/' . $id[1] . '?wmode=transparent' . $id[2];
				if( !empty($atts['background']) ){
					$url = add_query_arg(array(
						'autoplay' => 1,
						'controls' => 0,
						'showinfo' => 0,
						'rel' => 0,
						'enablejsapi' => 1,
						'loop' => 1,
						'playlist' => $id[1]
					), $url);
				}
				
				return '<iframe src="' . esc_url($url) . '" width="' . esc_attr($size['width']) . '" height="' . esc_attr($size['height']) . '" data-player-type="youtube" allowfullscreen ></iframe>';

			// vimeo link
			}else if( strpos($video, 'vimeo') !== false ){
				preg_match('#https?:\/\/vimeo.com\/(\d+)#', $video, $id);
				$url = '//player.vimeo.com/video/' . $id[1] . '?title=0&byline=0&portrait=0';
				if( !empty($atts['background']) ){
					$url = add_query_arg(array(
						'autopause' => 0,
						'autoplay' => 1,
						'loop' => 1,
						'api' => 1,
						'background' => 1
					), $url);
				}
				
				return '<iframe src="' . esc_url($url) . '" width="' . esc_attr($size['width']) . '" height="' . esc_attr($size['height']) . '" data-player-type="vimeo" allowfullscreen ></iframe>';
			
			// another link
			}else if(preg_match('#^https?://\S+#', $video, $match)){ 	
				$path_parts = pathinfo($match[0]);
				if( !empty($path_parts['extension']) ){
					return wp_video_shortcode( array( 'width' => $size['width'], 'height' => $size['height'], 'src' => $match[0]) );
				}else{
					global $wp_embed;
					return $wp_embed->run_shortcode('[embed width="' . $size['width'] . '" height="' . $size['height'] . '" ]' . $match[0] . '[/embed]');
				}				
			}
			
		}
	}	
	if( !function_exists('tourmaster_get_video_size') ){
		function tourmaster_get_video_size( $size = '' ){

			if( empty($size) || $size == 'full' ){
				return array( 'width'=>640, 'height'=>360 );
			}
			
			if( is_array($size) && ( $size['width'] == '100%' || $size['height'] == '100%') ){
				return array( 'width'=>640, 'height'=>360 );
			}
			
			if( !empty($GLOBALS['_wp_additional_image_sizes'][$size]) ){
				$width = $GLOBALS['_wp_additional_image_sizes'][$size]['width'];
				$height = $GLOBALS['_wp_additional_image_sizes'][$size]['height'];
				if( !empty($width) && !empty($height) ){
					return array( 'width'=>$width, 'height'=>$height );
				}
			}

			return array( 'width'=>640, 'height'=>360 );
		}
	}

	// get flexslider slides
	if( !function_exists('tourmaster_get_flexslider') ){
		function tourmaster_get_flexslider( $slides = array(), $atts = array() ){
			if( function_exists('gdlr_core_get_flexslider') ){
				return gdlr_core_get_flexslider( $slides, $atts );
			}

			$extra_class = empty($atts['additional-class'])? '': $atts['additional-class'];

			$ret  = '<div class="tourmaster-flexslider ' . esc_attr($extra_class) . '" ';
			$ret .= empty($atts['carousel'])? 'data-type="slider" ': 'data-type="carousel" ';
			$ret .= empty($atts['column'])? '': 'data-column="' . esc_attr($atts['column']) . '" ';
			$ret .= empty($atts['pausetime'])? '': 'data-pausetime="' . esc_attr($atts['pausetime']) . '" ';
			$ret .= empty($atts['slidespeed'])? '': 'data-slidespeed="' . esc_attr($atts['slidespeed']) . '" ';
			$ret .= empty($atts['effect'])? '': 'data-effect="' . esc_attr($atts['effect']) . '" ';
			$ret .= empty($atts['navigation'])? '': 'data-nav="' . esc_attr($atts['navigation']) . '" ';
			$ret .= empty($atts['nav-parent'])? '': 'data-nav-parent="' . esc_attr($atts['nav-parent']) . '" ';
			$ret .= empty($atts['nav-type'])? '': 'data-nav-type="' . esc_attr($atts['nav-type']) . '" ';
			$ret .= empty($atts['vcenter-nav'])? '': 'data-vcenter-nav="1" ';
			$ret .= empty($atts['with-thumbnail'])? '': 'data-thumbnail="1" ';
			$ret .= empty($atts['disable-autoslide'])? '': 'data-disable-autoslide="1" ';
			$ret .= ' >';

			$ret .= empty($atts['pre-content'])? '': $atts['pre-content'];

			$ret .= '<ul class="slides" >';
			foreach( $slides as $slide ){
				$ret .= '<li ' . ((!empty($atts['carousel']) && (!isset($atts['mglr']) || $atts['mglr'] === true))? ' class="tourmaster-item-mglr" ': '') . ' >';
				$ret .= $slide;
				$ret .= '</li>';
			}
			$ret .= '</ul>';

			$ret .= '</div>'; // flexslider
			return $ret;
		}
	}
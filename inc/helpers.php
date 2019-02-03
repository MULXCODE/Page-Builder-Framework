<?php
/**
 * Helpers
 *
 * Collection of helper functions
 *
 * @package Page Builder Framework
 */

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pingback
 */
function wpbf_pingback_header() {

	// add Pingback header if we're on a singular & pings are open
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="'. esc_url( get_bloginfo( 'pingback_url' ) ) .'">';
	}

}
add_action( 'wp_head', 'wpbf_pingback_header' );

/**
 * Schema Markup
 */
function wpbf_body_schema_markup() {

	// Blog variable
	$is_blog = ( is_home() || is_date() || is_category() || is_author() || is_tag() || is_attachment() || is_singular( 'post' ) ) ? true : false;

	// Default itemtype
	$itemtype = 'WebPage';

	// Define itemtype for Blog pages, otherwise use WebPage
	$itemtype = ( $is_blog ) ? 'Blog' : $itemtype;

	// Define itemtype for search results, otherwise use WebPage
	$itemtype = ( is_search() ) ? 'SearchResultsPage' : $itemtype;

	// Make result filterable
	$result = apply_filters( 'wpbf_body_itemtype', $itemtype );

	// Output
	echo 'itemscope="itemscope" itemtype="https://schema.org/'. esc_html( $result ) . '"'; // WPCS: XSS ok.

}

/**
 * Inner Content
 */
function wpbf_inner_content( $echo = true ) {

	if( is_singular() ) {

		// vars
		$options = get_post_meta( get_the_ID(), 'wpbf_options', true );

		// checking if template is set to full width
		// return false if $options is empty
		$fullwidth = $options ? in_array( 'full-width', $options ) : false;

		// checking if template is set to contained
		// return false if $options is empty
		$contained = $options ? in_array( 'contained', $options ) : false;

		// construct inner-content wrapper
		// return false if template is set to full-width
		$inner_content = $fullwidth ? false : apply_filters( 'wpbf_inner_content', '<div id="inner-content" class="wpbf-container wpbf-container-center wpbf-padding-medium">' );

		// check if Premium Add-On is active
		// only proceed if template is not set to contained
		if( wpbf_is_premium() && !$contained ) {

			// vars
			$wpbf_settings = get_option( 'wpbf_settings' );

			// get the array of post types that are set to full width under Appearance -> Theme Settings -> Global Templat Settings
			$fullwidth_global = isset( $wpbf_settings['wpbf_fullwidth_global'] ) ? $wpbf_settings['wpbf_fullwidth_global'] : array();

			// if current post type has been set to full-width globally, set $inner_content to false
			$fullwidth_global && in_array( get_post_type(), $fullwidth_global ) ? $inner_content = false : '';

		}

	// on archives, we only add the wpbf_inner_content filter
	} else {

		$inner_content = apply_filters( 'wpbf_inner_content', '<div id="inner-content" class="wpbf-container wpbf-container-center wpbf-padding-medium">' );

	}

	if( $echo ) {

		echo $inner_content; // WPCS: XSS ok.

	} else {

		return $inner_content;

	}

}

/**
 * Inner Content Close
 */
function wpbf_inner_content_close() {

	if( is_singular() ) {

		$options = get_post_meta( get_the_ID(), 'wpbf_options', true );

		$fullwidth = $options ? in_array( 'full-width', $options ) : false;

		$contained = $options ? in_array( 'contained', $options ) : false;

		$inner_content_close = $fullwidth ? false : '</div>';

		if( wpbf_is_premium() && !$contained ) {

			$wpbf_settings = get_option( 'wpbf_settings' );

			$fullwidth_global = isset( $wpbf_settings['wpbf_fullwidth_global'] ) ? $wpbf_settings['wpbf_fullwidth_global'] : array();

			$fullwidth_global && in_array( get_post_type(), $fullwidth_global ) ? $inner_content_close = false : '';

		}

	} else {

		$inner_content_close = '</div>';

	}

	echo $inner_content_close; // WPCS: XSS ok.

}

/**
 * Title
 */
function wpbf_title() {

	$options = get_post_meta( get_the_ID(), 'wpbf_options', true );

	$removetitle = $options ? in_array( 'remove-title', $options ) : false;

	$title = $removetitle ? false : '<h1 class="entry-title" itemprop="headline">'. get_the_title() .'</h1>';

	if ( wpbf_is_premium() ) {

		$wpbf_settings = get_option( 'wpbf_settings' );

		$removetitle_global = isset( $wpbf_settings['wpbf_removetitle_global'] ) ? $wpbf_settings['wpbf_removetitle_global'] : array();

		$removetitle_global && in_array( get_post_type(), $removetitle_global ) ? $title = false : '';

	}

	echo $title; // WPCS: XSS ok.

}

/**
 * Disable Header
 */
function wpbf_remove_header() {

	// stop here if we're on archives
	if( !is_singular() ) return;

	// vars
	$options = get_post_meta( get_the_ID(), 'wpbf_options', true );

	// checking if disable header is checked
	// return false if $options is empty
	$remove_header = $options ? in_array( 'remove-header', $options ) : false;

	// remove header if disable header is checked
	if( $remove_header ) {
		remove_action( 'wpbf_header', 'wpbf_do_header' );
	}

}
add_action( 'wp', 'wpbf_remove_header' );

/**
 * Disable Footer
 */
function wpbf_remove_footer() {

	// stop here if we're on archives
	if( !is_singular() ) return;

	// vars
	$options = get_post_meta( get_the_ID(), 'wpbf_options', true );

	// checking if disable footer is checked
	// return false if $options is empty
	$remove_footer = $options ? in_array( 'remove-footer', $options ) : false;

	// remove footer if disable footer is checked
	// also remove custom footer that has been added in the customizer
	if( $remove_footer ) {
		remove_action( 'wpbf_footer', 'wpbf_do_footer' );
		remove_action( 'wpbf_before_footer', 'wpbf_custom_footer' );
	}

}
add_action( 'wp', 'wpbf_remove_footer' );

/**
 * ScrollTop
 */
function wpbf_scrolltop() {

	if ( get_theme_mod( 'layout_scrolltop' ) ) {

		$scrollTop = get_theme_mod( 'scrolltop_value', 400 );
		echo '<div class="scrolltop" data-scrolltop-value="'. (int) $scrollTop .'"></div>';

	}

}
add_action( 'wp_footer', 'wpbf_scrolltop' );

/**
 * Archive Class
 *
 * We're adding unique classes to each archive type that exists
 *
 * wpbf-archive-content
 * wpbf-{post-type}-archive
 * wpbf-{archive-type}-content (for post archives)
 *
 * wpbf-{post-type}-archive-content (for cpt archives)
 * wpbf-{post-type}-taxonomy-content (for cpt-related taxonomies)
 */
function wpbf_archive_class() {

	$archive_class = '';

	if( is_date() ) {
		$archive_class = ' wpbf-archive-content wpbf-post-archive wpbf-date-content';
	} elseif( is_category() ) {
		$archive_class = ' wpbf-archive-content wpbf-post-archive wpbf-category-content';
	} elseif( is_tag() ) {
		$archive_class = ' wpbf-archive-content wpbf-post-archive wpbf-tag-content';
	} elseif( is_author() ) {
		$archive_class = ' wpbf-archive-content wpbf-post-archive wpbf-author-content';
	} elseif( is_home() ) {
		$archive_class = ' wpbf-archive-content wpbf-post-archive wpbf-blog-content';
	} elseif( is_search() ) {
		$archive_class = ' wpbf-archive-content wpbf-post-archive wpbf-search-content';
	} elseif( is_post_type_archive() ) {

		$post_type = get_post_type();
		if( !$post_type ) return $archive_class; // stop here if no post has been found

		$archive_class = ' wpbf-archive-content';
		$archive_class .= ' wpbf-'. $post_type .'-archive';
		$archive_class .= ' wpbf-'. $post_type .'-archive-content';

	} elseif( is_tax() ) {

		$post_type = get_post_type();
		if( !$post_type ) return $archive_class; // stop here if no post has been found

		$archive_class = ' wpbf-archive-content';
		$archive_class .= ' wpbf-'. $post_type .'-archive';
		$archive_class .= ' wpbf-'. $post_type .'-archive-content';
		$archive_class .= ' wpbf-'. $post_type .'-taxonomy-content';

	}

	return $archive_class;

}

/**
 * Singular Class
 */
function wpbf_singular_class() {

	if( is_singular( 'post' ) ) {
		$singular_class = ' wpbf-single-content';
	} elseif( is_attachment() ) {
		$singular_class = ' wpbf-attachment-content';
	} elseif( is_page() ) {
		$singular_class = ' wpbf-page-content';
	} elseif( is_404() ) {
		$singular_class = ' wpbf-404-content';
	} else {
		$post_type = get_post_type();
		$singular_class = ' wpbf-'. $post_type .'-content';
	}

	return $singular_class;

}

/**
 * Archive Header
 */
function wpbf_archive_header() {

	if( is_author() ) { ?>

		<section class="wpbf-author-box">
			<h1 class="page-title"><span class="vcard"><?php echo get_the_author(); ?></span></h1>
			<p><?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?></p>
			<div class="wpbf-avatar">
				<?php echo get_avatar( get_the_author_meta( 'email' ), 120 ); ?>
			</div>
		</section>

	<?php } else {

		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<div class="taxonomy-description">', '</div>' );

	}

}

/**
 * Archive Headline
 */
function wpbf_archive_title( $title ) {

	$archive_headline  = get_theme_mod( 'archive_headline' );

	if( is_category() ) {

		if( $archive_headline == 'hide_prefix' ) {
			$title = single_cat_title( '', false );
		} elseif( $archive_headline == 'hide' ) {
			$title = false;
		}

	} elseif( is_tag() ) {

		if( $archive_headline == 'hide_prefix' ) {
			$title = single_tag_title( '', false );
		} elseif( $archive_headline == 'hide' ) {
			$title = false;
		}

	} elseif( is_date() ) {

		$date = get_the_date( 'F Y' );
		if( is_year() ) $date = get_the_date( 'Y' );
		if( is_day() ) $date = get_the_date( 'F j, Y' );

		if( $archive_headline == 'hide_prefix' ) {
			$title = $date;
		} elseif( $archive_headline == 'hide' ) {
			$title = false;
		}

	} elseif( is_post_type_archive() ) {

		if( $archive_headline == 'hide_prefix' ) {
			$title = post_type_archive_title( '', false );
		} elseif( $archive_headline == 'hide' ) {
			$title = false;
		}

	} elseif( is_tax() ) {

		if( $archive_headline == 'hide_prefix' ) {
			$title = single_term_title( '', false );
		} elseif( $archive_headline == 'hide' ) {
			$title = false;
		}

	}

	return $title;

}
add_filter( 'get_the_archive_title', 'wpbf_archive_title', 10 );

/**
 * Responsive Breakpoints
 * 
 * Simple check if Responsive Breakpoints are set
 */
if( !function_exists( 'wpbf_has_responsive_breakpoints' ) ) {

	function wpbf_has_responsive_breakpoints() {

		// there can't be Responsive Breakpoints if there's no Premium Add-On
		if( !wpbf_is_premium() ) return false;

		// vars
		$wpbf_settings = get_option( 'wpbf_settings' );

		// check if custom breakpoints are set, otherwise return false
		if ( !empty( $wpbf_settings['wpbf_breakpoint_medium'] ) || !empty( $wpbf_settings['wpbf_breakpoint_desktop'] ) || !empty( $wpbf_settings['wpbf_breakpoint_mobile'] ) ) {
			return true;
		} else {
			return false;
		}

	}

}

/**
 * Right Sidebar
 */
function wpbf_do_sidebar_right() {

	if( wpbf_sidebar_layout() == 'right' ) get_sidebar();

}
add_action( 'wpbf_sidebar_right', 'wpbf_do_sidebar_right' );

/**
 * Left Sidebar
 */
function wpbf_do_sidebar_left() {

	if( wpbf_sidebar_layout() == 'left' ) get_sidebar();

}
add_action( 'wpbf_sidebar_left', 'wpbf_do_sidebar_left' );

/**
 * Sidebar Layout
 */
function wpbf_sidebar_layout() {

	// Default Sidebar Position is 'right'
	$sidebar = get_theme_mod( 'sidebar_position', 'right' );

	if ( is_singular() ) {

		$id                             = get_the_ID();
		$single_sidebar_position        = get_post_meta( $id, 'wpbf_sidebar_position', true );
		$single_sidebar_position_global = get_theme_mod( 'single_sidebar_layout' );

		$sidebar = $single_sidebar_position && $single_sidebar_position !== 'global' ? $single_sidebar_position : $sidebar;
		$sidebar = $single_sidebar_position_global && $single_sidebar_position_global !== 'global' ? $single_sidebar_position_global : $sidebar;

	}

	if ( is_home() ) {

		$blog_sidebar_position = get_theme_mod( 'blog_sidebar_layout' );
		$sidebar = $blog_sidebar_position && $blog_sidebar_position !== 'global' ? $blog_sidebar_position : $sidebar;

	}

	if ( is_archive() ) {

		$archive_sidebar_position = get_theme_mod( 'archive_sidebar_layout' );
		$sidebar = $archive_sidebar_position && $archive_sidebar_position !== 'global' ? $archive_sidebar_position : $sidebar;

	}

	return apply_filters( 'wpbf_sidebar_layout', $sidebar );

}

/**
 * Blog Layout
 */
function wpbf_blog_layout() {

	$template_parts_header = get_theme_mod( 'archive_sortable_header', array( 'title', 'meta', 'featured' ) );
	$template_parts_footer = get_theme_mod( 'archive_sortable_footer', array( 'readmore', 'categories' ) );
	$blog_layout           = get_theme_mod( 'archive_layout', 'default' );

	if( is_home() ) {

		$template_parts_header = get_theme_mod( 'blog_sortable_header', array( 'title', 'meta', 'featured' ) );
		$template_parts_footer = get_theme_mod( 'blog_sortable_footer', array( 'readmore', 'categories' ) );
		$blog_layout           = get_theme_mod( 'blog_layout', 'default' );

	}

	return apply_filters( 'wpbf_blog_layout', array( 'blog_layout' => $blog_layout, 'template_parts_header' => $template_parts_header, 'template_parts_footer' => $template_parts_footer ) );

}

/**
 * Navigation
 * 
 * Set wp_nav_menu for main navigations
 */
function wpbf_nav_menu() {

	$custom_menu = get_theme_mod( 'menu_custom' );

	if( $custom_menu ) {

		echo do_shortcode( $custom_menu );

	} elseif( in_array( get_theme_mod( 'menu_position' ), array( 'menu-off-canvas', 'menu-off-canvas-left' ) ) ) {

		wp_nav_menu( array(
			'theme_location'	=> 'main_menu',
			'container'			=> false,
			'menu_class'		=> 'wpbf-menu',
			'depth'				=> 3,
			'fallback_cb'		=> 'wpbf_menu_fallback'
		));

	} elseif ( get_theme_mod( 'menu_position' ) == 'menu-full-screen' ) {

		wp_nav_menu( array(
			'theme_location'	=> 'main_menu',
			'container'			=> false,
			'menu_class'		=> 'wpbf-menu',
			'depth'				=> 1,
			'fallback_cb'		=> 'wpbf_menu_fallback'
		));

	} else {

		wp_nav_menu( array(
			'theme_location'	=> 'main_menu',
			'container'			=> false,
			'menu_class'		=> 'wpbf-menu wpbf-sub-menu'. wpbf_sub_menu_animation() . wpbf_menu_effect() . wpbf_menu_effect_animation() . wpbf_menu_effect_alignment() .'',
			'depth'				=> 3,
			'fallback_cb'		=> 'wpbf_menu_fallback'
		));

	}
}
add_action( 'wpbf_main_menu', 'wpbf_nav_menu' );

/**
 * Menu
 * 
 * returns the menu selected under Header -> Navigation in the WordPress customizer
 */
function wpbf_menu() {
	return get_theme_mod( 'menu_position', 'menu-right' );
}

/**
 * Mobile Menu
 * 
 * returns the menu selected under Header -> Mobile Navigation in the WordPress customizer
 */
function wpbf_mobile_menu() {
	return get_theme_mod( 'mobile_menu_options', 'menu-mobile-hamburger' );
}

/**
 * Is Off Canvas Menu
 * 
 * A simple check that returns true if an Off-Canvas menu is being used
 */
function wpbf_is_off_canvas_menu() {

	if( in_array( get_theme_mod( 'menu_position' ), array( 'menu-off-canvas', 'menu-off-canvas-left', 'menu-full-screen' ) ) ) {
		return true;
	} else {
		return false;
	}

}

/**
 * Menu Alignment
 * 
 * return the stacked advanced menu alignment
 */
function wpbf_menu_alignment() {

	$alignment = get_theme_mod( 'menu_alignment', 'left' );
	$alignment = ' menu-align-' . $alignment;

	return $alignment;

}

/**
 * Menu Effect
 */
function wpbf_menu_effect() {

	$menu_effect = get_theme_mod( 'menu_effect', 'none' );
	$menu_effect = ' wpbf-menu-effect-' . $menu_effect;

	return $menu_effect;

}

/**
 * Menu Effect Animation
 */
function wpbf_menu_effect_animation() {

	$menu_effect_animation = get_theme_mod( 'menu_effect_animation', 'fade' );
	$menu_effect_animation = ' wpbf-menu-animation-' . $menu_effect_animation;

	return $menu_effect_animation;

}

/**
 * Menu Effect Alignment
 */
function wpbf_menu_effect_alignment() {

	$menu_effect_alignment = get_theme_mod( 'menu_effect_alignment', 'center' );
	$menu_effect_alignment = ' wpbf-menu-align-' . $menu_effect_alignment;

	return $menu_effect_alignment;

}

/**
 * Sub Menu Animation
 */
function wpbf_sub_menu_animation() {

	$sub_menu_animation = get_theme_mod( 'sub_menu_animation', 'fade' );
	$sub_menu_animation = ' wpbf-sub-menu-animation-' . $sub_menu_animation;

	return $sub_menu_animation;

}

/**
 * Navigation Attributes
 * 
 * Currently only being used to add the sub menu animation duration
 */
function wpbf_navigation_attributes() {

	// vars
	$submenu_animation_duration = get_theme_mod( 'sub_menu_animation_duration' );

	// Construct Navigation Attributes
	$navigation_attributes = $submenu_animation_duration ? 'data-sub-menu-animation-duration="' . esc_attr( $submenu_animation_duration ) . '"' : 'data-sub-menu-animation-duration="250"';

	echo $navigation_attributes; // WPCS: XSS ok.

}
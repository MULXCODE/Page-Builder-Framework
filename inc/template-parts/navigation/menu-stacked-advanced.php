<?php
/**
 * Menu Stacked
 *
 * @package Page Builder Framework
 * @subpackage Template Parts
 */
 
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpbf-visible-large wpbf-menu-stacked-advanced<?php echo esc_attr( wpbf_menu_alignment() ); ?>">

	<div class="wpbf-menu-stacked-advanced-wrapper">

		<div class="wpbf-container wpbf-container-center">

			<div class="wpbf-1-4">

			<?php get_template_part( 'inc/template-parts/logo/logo' ); ?>
			<?php get_template_part( 'inc/template-parts/logo/tagline' ); ?>

			</div>

			<div class="wpbf-3-4">
				<?php echo do_shortcode( get_theme_mod( 'menu_stacked_wysiwyg' ) ); ?>
			</div>

		</div>

	</div>

	<?php do_action( 'wpbf_before_main_menu' ); ?>

	<nav navigation class="wpbf-container wpbf-container-center wpbf-nav-wrapper" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">

		<?php do_action( 'wpbf_main_menu_open' ); ?>

		<?php do_action( 'wpbf_main_menu' ); ?>

		<?php do_action( 'wpbf_main_menu_close' ); ?>

	</nav>

	<?php do_action( 'wpbf_after_main_menu' ); ?>

</div>

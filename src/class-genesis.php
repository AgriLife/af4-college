<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/AgriLife/af4-college/blob/master/src/class-genesis.php
 * @since      0.1.1
 * @package    af4-college
 * @subpackage af4-college/src
 */

namespace College;

/**
 * The core plugin class
 *
 * @since 0.1.1
 * @return void
 */
class Genesis {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function __construct() {

		global $af_required;

		// Footer.
		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - Contact and Social', 'af4-college' ),
				'id'          => 'footer-left',
				'description' => __( 'This is the first widget area for the site footer.', 'af4-college' ),
			)
		);

		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - Menu', 'af4-college' ),
				'id'          => 'footer-right',
				'description' => __( 'This is the second widget area for the site footer.', 'af4-college' ),
			)
		);

		add_action( 'init', array( $this, 'init' ), 12 );
		add_filter( 'genesis_structural_wrap-footer', array( $this, 'class_footer_wrap' ), 12 );
		add_action( 'genesis_footer', array( $this, 'genesis_footer_widget_area' ), 7 );
		add_action( 'genesis_footer', array( $this, 'add_copyright' ), 9 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'add_widget_class' ) );
		add_filter( 'af4_header_logo', array( $this, 'header_logo' ), 11, 4 );
		add_filter( 'genesis_attr_title-area', array( $this, 'class_cell_title_area' ) );
		add_filter( 'af4_header_right_attr', array( $this, 'af4_header_right_attr' ) );

		// Move right header widget area attached to the AgriFlex\RequiredDOM class.
		remove_action( 'genesis_header', array( $af_required, 'add_header_right_widgets' ), 10 );
		add_filter( 'af4_primary_nav_menu', array( 'AgriFlex\RequiredDOM', 'add_header_right_widgets' ) );

		// Improve layout of navigation menu through classes.
		add_filter( 'af4_top_bar_left_attr', array( $this, 'af4_top_bar_left_attr' ) );
		add_filter(
			'wp_nav_menu_args',
			function( $args ) {

				if ( 'primary' === $args['theme_location'] && false === strpos( $args['menu_class'], 'cell' ) ) {
					$args['menu_class'] .= ' cell medium-auto';
				}

				return $args;

			}
		);

		// Add maroon bar behind navigation menu.
		add_filter(
			'genesis_structural_wrap-menu-primary',
			function( $output, $original_output ) {
				if ( '</div>' !== $output ) {
					$output .= '<span class="bar-wrap"></span>';
				}
				return $output;
			},
			10,
			3
		);

		remove_action( 'genesis_footer', array( $af_required, 'render_tamus_logo' ), 10 );

	}

	/**
	 * Init
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function init() {
		remove_action( 'genesis_header', array( 'AgriFlex\RequiredDOM', 'add_header_right_widgets' ), 10 );
	}

	/**
	 * Change footer wrap class names
	 *
	 * @since 0.1.0
	 * @param string $output The wrap HTML.
	 * @return string
	 */
	public function class_footer_wrap( $output ) {

		$output = preg_replace( '/\s?grid-container\s?/', ' ', $output );
		$output = preg_replace( '/\s?grid-x\s?/', ' ', $output );
		$output = preg_replace( '/\s?grid-padding-x\s?/', ' ', $output );
		$output = preg_replace( '/class=" /', 'class="', $output );

		return $output;
	}

	/**
	 * Add post left sidebar
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function genesis_footer_widget_area() {

		echo '<div class="footer-info grid-container">';

		genesis_widget_area(
			'footer-right',
			array(
				'before' => '',
				'after'  => '',
			)
		);

		$logo = sprintf(
			'<div class="logo fcell small-order-5"><a href="%s" title="College of Agriculture and Life Sciences"><img src="%s"></a></div>',
			trailingslashit( home_url() ),
			COLAF4_DIR_URL . 'images/logo-coals-light.svg'
		);

		echo wp_kses_post( $logo );

		genesis_widget_area(
			'footer-left',
			array(
				'before' => '',
				'after'  => '',
			)
		);

		$accessibility = '<div class="waa fcell small-order-3"><a class="underline" href="#">Website Accessibility Assistance</a></div>';
		echo wp_kses_post( $accessibility );

		echo '</div>';

	}

	/**
	 * Add copyright notice
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function add_copyright() {

		echo wp_kses_post( '<p class="center">&copy; ' . date( 'Y' ) . ' Texas A&amp;M University. All rights reserved.</p>' );

	}

	/**
	 * Add class name to widget elements
	 *
	 * @since 0.1.9
	 * @param array $params Widget parameters.
	 * @return array
	 */
	public function add_widget_class( $params ) {

		// Add class to outer widget container.
		$str = $params[0]['before_widget'];
		preg_match( '/class="([^"]+)"/', $str, $match );
		$classes = explode( ' ', $match[1] );

		if ( in_array( 'widget', $classes, true ) ) {

			// Add class to all footer widgets.
			if ( in_array( $params[0]['id'], array( 'footer-left', 'footer-right' ), true ) ) {
				$classes[] = 'fcell';
			}

			// Add small order classes to widgets.
			if ( 'footer-right' === $params[0]['id'] ) {
				$classes[] = 'small-order-1';
			} elseif ( false !== strpos( $params[0]['widget_id'], 'college_contact' ) ) {
				$classes[] = 'small-order-2';
			} elseif ( in_array( $params[0]['widget_name'], array( 'AddToAny Share', 'AddToAny Follow' ), true ) ) {
				$classes[] = 'small-order-4';
			}

			$class_output               = implode( ' ', $classes );
			$params[0]['before_widget'] = str_replace( $match[0], "class=\"{$class_output}\"", $params[0]['before_widget'] );
		}

		// Remove blank space from between Add To Any widgets for styling purposes.
		if ( in_array( $params[0]['widget_name'], array( 'AddToAny Share', 'AddToAny Follow' ), true ) ) {
			$params[0]['after_widget'] = preg_replace( '/\s/', '', $params[0]['after_widget'] );
		}

		return $params;

	}

	/**
	 * Header logo and title
	 *
	 * @since 0.1.1
	 * @param string $inside Current title inner HTML.
	 * @param string $old_inside Previous title inner HTML.
	 * @param string $logo_html HTML template string.
	 * @param string $home Homepage url.
	 * @return string
	 */
	public function header_logo( $inside, $old_inside, $logo_html, $home ) {

		$inside = sprintf(
			'<a href="%s" title="%s"><img src="%s"><small>Texas A&M University</small><br><span class="title">%s</span></a>',
			$home,
			get_bloginfo( 'name' ),
			COLAF4_DIR_URL . 'images/logo-coals-box.svg',
			get_bloginfo( 'name' )
		);

		return $inside;

	}

	/**
	 * Add header title area cell class names
	 *
	 * @since 0.1.1
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function class_cell_title_area( $attributes ) {
		$attributes['class'] .= ' cell small-6 medium-12';
		return $attributes;
	}

	/**
	 * Change attributes for header right widget area
	 *
	 * @since 0.1.1
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function af4_header_right_attr( $attributes ) {
		$attributes['class'] = 'header-right-widget-area cell medium-shrink';
		return $attributes;
	}


	/**
	 * Change attributes for top bar left
	 *
	 * @since 0.1.1
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function af4_top_bar_left_attr( $attributes ) {
		$attributes['class'] .= ' grid-x grid-padding-x';
		return $attributes;
	}
}

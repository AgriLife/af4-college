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

		add_filter( 'genesis_structural_wrap-footer', array( $this, 'class_footer_wrap' ), 12 );
		add_action( 'genesis_footer', array( $this, 'genesis_footer_widget_area' ), 7 );
		add_action( 'genesis_footer', array( $this, 'add_copyright' ), 9 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'add_widget_class' ) );

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
			'<a class="logo fcell" href="%s" title="College of Agriculture and Life Sciences"><img src="%s"></a>',
			trailingslashit( home_url() ),
			COLAF4_DIR_URL . '/images/logo-coals-light.svg'
		);

		echo wp_kses_post( $logo );

		genesis_widget_area(
			'footer-left',
			array(
				'before' => '',
				'after'  => '',
			)
		);

		$accessibility = '<a class="waa fcell underline" href="#">Website Accessibility Assistance</a>';
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

			// Invert footer widgets.
			if ( in_array( $params[0]['id'], array( 'footer-left', 'footer-right' ), true ) ) {
				$classes[] = 'fcell';
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
}

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

		add_action( 'genesis_footer', array( $this, 'genesis_footer_widget_area' ) );

	}

	/**
	 * Add post left sidebar
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function genesis_footer_widget_area() {

		$logo          = sprintf(
			'<a href="%s" title="College of Agriculture and Life Sciences"><img src="%s"></a>',
			trailingslashit( home_url() ),
			COLAF4_DIR_URL . '/images/logo-coals-light.svg'
		);
		$accessibility = '<a href="#">Website Accessibility Assistance</a>';

		echo wp_kses_post( $logo );

		genesis_widget_area(
			'footer-left',
			array(
				'before' => '',
				'after'  => '',
			)
		);

		echo wp_kses_post( $accessibility );

		genesis_widget_area(
			'footer-right',
			array(
				'before' => '',
				'after'  => '',
			)
		);

	}
}

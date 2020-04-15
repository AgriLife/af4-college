<?php
/**
 * The file that defines css and js files loaded for the plugin
 *
 * A class definition that includes css and js files used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/AgriLife/af4-college/blob/master/src/class-assets.php
 * @since      0.1.0
 * @package    af4-college
 * @subpackage af4-college/src
 */

namespace College;

/**
 * Add assets
 *
 * @since 0.1.0
 */
class Assets {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function __construct() {

		// Register global styles used in the theme.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );

		// Enqueue extension styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Remove registered scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'remove_af4_scripts' ), 12 );

		// Register scripts used in the plugin.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 14 );

		// Enqueue scripts used in the plugin.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );

	}

	/**
	 * Registers all styles used within the plugin
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function register_styles() {

		global $wp_query;
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

		wp_register_style(
			'college-styles',
			COLAF4_DIR_URL . 'css/college.css',
			array( 'agriflex-default-styles' ),
			filemtime( COLAF4_DIR_PATH . 'css/college.css' ),
			'screen'
		);

		if ( ! $template_name || 'default' === $template_name ) {
			wp_register_style(
				'college-default-template-styles',
				COLAF4_DIR_URL . 'css/template-default.css',
				array( 'college-styles' ),
				filemtime( COLAF4_DIR_PATH . 'css/template-default.css' ),
				'screen'
			);
		}

	}

	/**
	 * Enqueues extension styles
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_styles() {

		global $wp_query;
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

		wp_enqueue_style( 'college-styles' );

		if ( ! $template_name || 'default' === $template_name ) {
			wp_enqueue_style( 'college-default-template-styles' );
		}

	}

	/**
	 * Remove registered js files.
	 *
	 * @since 0.5.6
	 * @return void
	 */
	public function remove_af4_scripts() {

		wp_deregister_script( 'agriflex-public' );

	}

	/**
	 * Register js files.
	 *
	 * @since 0.1.2
	 * @return void
	 */
	public function register_scripts() {

		wp_register_script(
			'agriflex-college-public',
			COLAF4_DIR_URL . 'js/public.min.js',
			false,
			filemtime( COLAF4_DIR_PATH . '/js/public.min.js' ),
			true
		);

		wp_register_script(
			'foundation-college',
			COLAF4_DIR_URL . 'js/foundation.concat.js',
			array( 'jquery', 'foundation' ),
			filemtime( COLAF4_DIR_PATH . '/js/foundation.concat.js' ),
			true
		);

	}

	/**
	 * Enqueue js files.
	 *
	 * @since 0.1.2
	 * @return void
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'agriflex-college-public' );
		wp_enqueue_script( 'foundation-college' );

	}

}

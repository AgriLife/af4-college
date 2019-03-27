<?php
/**
 * College - AgriFlex4
 *
 * @package      af4-college
 * @author       Zachary Watkins
 * @copyright    2019 Texas A&M AgriLife Communications
 * @license      GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:  College - AgriFlex4
 * Plugin URI:   https://github.com/AgriLife/af4-college
 * Description:  College of Agriculture and Life Sciences variation of the AgriFlex4 theme.
 * Version:      0.1.1
 * Author:       Zachary Watkins
 * Author URI:   https://github.com/ZachWatkins
 * Author Email: zachary.watkins@ag.tamu.edu
 * Text Domain:  af4-college
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

/* Define some useful constants */
define( 'COLAF4_DIRNAME', 'af4-college' );
define( 'COLAF4_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'COLAF4_DIR_FILE', __FILE__ );
define( 'COLAF4_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'COLAF4_TEXTDOMAIN', 'af4-college' );

/**
 * The core plugin class that is used to initialize the plugin
 */
require COLAF4_DIR_PATH . 'src/class-college.php';
spl_autoload_register( 'College::autoload' );
College::get_instance();

/**
 * Notify user of missing dependencies
 */
register_activation_hook( __FILE__, 'af4_college_activation' );

/**
 * Check for missing dependencies
 *
 * @since 0.1.1
 * @return void
 */
function af4_college_activation() {
	$theme = wp_get_theme();
	if ( 'AgriFlex4' !== $theme->name ) {
		$error = sprintf(
			/* translators: %s: URL for plugins dashboard page */
			__(
				'Plugin NOT activated: The <strong>College - AgriFlex4 Plugin</strong> needs the <strong>AgriFlex4 Theme</strong> to be installed and activated first. <a href="%s">Back to plugins page</a>',
				'af4-college'
			),
			get_admin_url( null, '/plugins.php' )
		);
		wp_die( wp_kses_post( $error ) );
	}
}

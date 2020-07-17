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

		add_action( 'init', array( $this, 'init' ), 12 );
		add_filter( 'genesis_structural_wrap-footer', array( $this, 'class_footer_wrap' ), 12 );
		add_action( 'genesis_footer', array( $this, 'genesis_footer_widget_area' ), 7 );
		add_action( 'genesis_footer', array( $this, 'add_copyright' ), 9 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'add_widget_class' ) );
		add_filter( 'genesis_attr_title-area', array( $this, 'class_cell_title_area' ), 11 );
		add_filter( 'af4_nav_search_attr', array( $this, 'af4_nav_search_attr' ) );

		// Improve layout of navigation menu through classes.
		add_filter( 'af4_top_bar_left_attr', array( $this, 'af4_top_bar_left_attr' ) );
		add_filter( 'af4_top_bar_attr', array( $this, 'add_grid_container_class' ) );
		add_filter( 'wp_nav_menu_args', array( $this, 'nav_menu_args' ) );

		// Remove span tags from nav link elements.
		add_filter( 'af4_primary_nav_class', array( $this, 'af4_primary_nav_class' ) );

		// Add maroon bar behind navigation menu.
		add_filter( 'genesis_attr_nav-primary', array( $this, 'attr_nav_primary' ) );

		// Add department header menu.
		add_action( 'init', array( $this, 'register_additional_menu' ) );

		// Add custom page header content.
		add_action( 'genesis_after_header', array( $this, 'add_custom_header' ) );
		add_filter( 'body_class', array( $this, 'add_custom_header_class' ) );

		// Remove sticky attributes from site header.
		add_filter( 'af4_header_wrap', array( $this, 'remove_sticky_header' ), 11, 1 );

		// Add default header logo.
		add_filter( 'af4_header_logo', array( $this, 'default_coals_logo' ), 11, 4 );

		// Genesis header right widget area.
		add_action( 'after_setup_theme', array( $this, 'register_header_right_widget_area' ), 9 );
		add_filter( 'genesis_markup_header-widget-area_open', array( $this, 'header_right_open' ), 11, 2 );
		add_filter( 'genesis_markup_header-widget-area_close', array( $this, 'header_right_close' ), 11, 2 );

	}

	/**
	 * Init
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function init() {

		global $af_required;

		// Move navigation menu to after the header structural wrap but within the sticky container.
		remove_action( 'genesis_header', 'genesis_do_nav', 10 );
		add_action( 'genesis_structural_wrap-header', array( $this, 'genesis_do_nav' ), 16 );

		// Move right header widget area attached to the AgriFlex\RequiredDOM class.
		remove_action( 'genesis_header', array( $af_required, 'add_nav_search_widget_area' ), 10 );
		add_filter( 'af4_primary_nav_menu', array( $this, 'add_search_widget' ), 9 );

		// Remove default mobile navigation menu toggle elements.
		remove_filter( 'af4_before_nav', array( $af_required, 'af4_nav_primary_title_bar_open' ), 9 );
		remove_filter( 'af4_before_nav', array( $af_required, 'add_menu_toggle' ), 10 );
		remove_filter( 'af4_before_nav', array( $af_required, 'add_search_toggle' ), 11 );
		remove_filter( 'af4_before_nav', array( $af_required, 'af4_nav_primary_title_bar_close' ), 12 );
		add_action( 'genesis_header', array( $this, 'college_mobile_nav_toggle' ) );

		// Add new widget areas.
		$this->add_widget_areas();

		// Remove footer tamu logo.
		remove_action( 'genesis_footer', array( $af_required, 'render_tamus_logo' ), 10 );

	}

	/**
	 * Add department menu to header
	 *
	 * @since 0.1.2
	 * @param string $output Current output for Genesis title area close element.
	 * @param array  $args Arguments for Genesis title area close element.
	 * @return string
	 */
	public function department_nav_menu( $output, $args ) {

		if ( ! empty( $args['close'] ) ) {

			$menu = array(
				'theme_location' => 'college-dept-menu',
				'menu_class'     => 'grid-x reset dropdown menu submenu',
			);
			$icon = '<div id="dept-nav-menu" class="dept-nav-menu title-bars cell small-12 medium-shrink title-bar-right hide-for-small-only" data-toggler=".hide-for-small-only"><div class="title-bar title-bar-departments accordion-submenu-parent"><button type="button" data-toggle="header-depts">Departments</button></div><div id="header-depts" class="hide" data-toggler=".hide">%s</div></div>';

			ob_start();
			wp_nav_menu( $menu );
			$nav = ob_get_contents();
			ob_end_clean();

			$output .= sprintf( $icon, $nav );

		}

		return $output;

	}

	/**
	 * Register nav menu location
	 *
	 * @since 0.1.6
	 * @return void
	 */
	public function register_additional_menu() {

		register_nav_menu( 'college-dept-menu', __( 'Department Navigation Menu' ) );

	}

	/**
	 * Add nav menu in grid container
	 *
	 * @since 0.1.2
	 * @param string $output Output for the site header wrap.
	 * @return string
	 */
	public function genesis_do_nav( $output ) {

		ob_start();
		genesis_do_nav();
		$nav = ob_get_contents();
		ob_end_clean();

		// Add college menu to nav.
		if ( has_nav_menu( 'college-dept-menu' ) ) {
			$menu      = array(
				'theme_location' => 'college-dept-menu',
				'menu_class'     => 'menu submenu sub-menu vertical medium-horizontal menu-depth-1 first-sub',
				'container'      => '',
			);
			$dept_item = '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children unlinked dept-nav" role="menuitem" aria-haspopup="true" aria-label="Departments"><a href="#" itemprop="url">Departments</a>%s</li>';

			ob_start();
			wp_nav_menu( $menu );
			$depnav = ob_get_contents();
			ob_end_clean();

			$deptmenu = sprintf( $dept_item, $depnav );
			$nav      = str_replace( 'Home</a></li>', 'Home</a></li>' . $deptmenu, $nav );
		}

		$output = preg_replace( '/<\/div><\/div><\/div>$/', '</div>' . $nav . '</div></div>', $output );

		return $output;

	}

	/**
	 * Add search widget and toggle button.
	 *
	 * @since 0.1.2
	 * @param string $output Output for the primary menu.
	 * @return string
	 */
	public function add_search_widget( $output ) {

		global $af_required;

		if ( is_active_sidebar( 'af4-header-right' ) ) {

			$search  = '<div class="title-bars cell medium-shrink title-bar-right">';
			$search .= '<div class="title-bar title-bar-search"><button class="search-icon" type="button" data-toggle="header-search"></button><div class="title-bar-title">Search</div>';
			$search  = $af_required->add_nav_search_widget_area( $search );
			$search  = str_replace( 'id="header-search', 'data-toggler=".hide-for-medium" id="header-search', $search );
			$search .= '</div></div>';

			$output .= $search;

		}

		return $output;

	}

	/**
	 * Add header nav primary cell class names
	 *
	 * @since 0.2.1
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function attr_nav_primary( $attributes ) {
		$attributes['class'] = 'nav-p';
		return $attributes;
	}

	/**
	 * Replace Foundation class in primary nav menu
	 *
	 * @since 0.1.2
	 * @param array $class Array of classes for AgriFlex4 primary nav menu.
	 * @return array
	 */
	public function af4_primary_nav_class( $class ) {

		$key1 = array_search( 'medium-auto', $class, true );
		$key2 = array_search( 'small-12', $class, true );
		$key3 = array_search( 'cell', $class, true );

		unset( $class[ $key1 ] );
		unset( $class[ $key2 ] );
		unset( $class[ $key3 ] );

		return $class;

	}

	/**
	 * Change class for primary nav menu
	 *
	 * @since 0.1.2
	 * @param array $args Arguments for menu.
	 * @return array
	 */
	public function nav_menu_args( $args ) {

		if ( 'primary' === $args['theme_location'] ) {

			$args['menu_class'] .= ' cell medium-auto';

		}

		return $args;

	}

	/**
	 * Add AgriFlex4 menu and nav primary toggles for mobile
	 *
	 * @since 0.1.2
	 * @return void
	 */
	public function college_mobile_nav_toggle() {

		global $af_required;

		$open_m  = str_replace( 'small-6', 'shrink', $af_required->af4_nav_primary_title_bar_open() );
		$open_m  = str_replace( 'title-bar-right', 'title-bar-left', $open_m );
		$menu_m  = $af_required->add_menu_toggle();
		$menu_m  = str_replace( '<div class="title-bar-title" data-toggle="nav-menu-primary">Menu</div>', '', $menu_m );
		$close_m = $af_required->af4_nav_primary_title_bar_close();
		$m       = $open_m . $menu_m . $close_m;
		echo wp_kses_post( $m );

	}

	/**
	 * Adds sidebars
	 *
	 * @since 0.1.1
	 * @return void
	 */
	private function add_widget_areas() {

		// Footer.
		if ( function_exists( 'genesis_register_sidebar' ) ) {

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

		}

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

		echo '<div class="footer-info grid-container"><div class="grid-x grid-padding-x">';

		genesis_widget_area(
			'footer-right',
			array(
				'before' => '',
				'after'  => '',
			)
		);

		echo '<div class="cell medium-order-1 medium-6 small-12"><div class="grid-x">';

		genesis_widget_area(
			'footer-left',
			array(
				'before' => '',
				'after'  => '',
			)
		);

		$logo = sprintf(
			'<div class="logo cell medium-order-1"><a href="%s" title="College of Agriculture and Life Sciences"><img src="%s" alt="Texas A&M University" /></a></div>',
			'https://aglifesciences.tamu.edu/',
			COLAF4_DIR_URL . 'images/logo-coals-light.svg'
		);

		echo wp_kses_post( $logo );

		echo '</div></div></div></div>';

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

			// Add class to header right widgets.
			if ( 'header-right' === $params[0]['id'] ) {
				$classes[] = 'cell auto';
			}

			// Add class to all footer widgets.
			if ( in_array( $params[0]['id'], array( 'footer-left', 'footer-right' ), true ) ) {
				$classes[] = 'cell';
			}

			// Add order classes to widgets.
			if ( 'footer-right' === $params[0]['id'] ) {
				$classes[] = 'medium-6 small-12 medium-order-2';
			} elseif ( false !== strpos( $params[0]['widget_id'], 'college_contact' ) ) {
				$classes[] = 'medium-order-2';
			} elseif ( in_array( $params[0]['widget_name'], array( 'AddToAny Share', 'AddToAny Follow' ), true ) ) {
				$classes[] = 'medium-order-3';
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
	 * Add header title area cell class names
	 *
	 * @since 0.1.1
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function class_cell_title_area( $attributes ) {
		$attributes['class'] = 'title-area cell auto';
		return $attributes;
	}

	/**
	 * Change attributes for header right widget area
	 *
	 * @since 0.1.1
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function af4_nav_search_attr( $attributes ) {
		$attributes['class'] = 'header-right-widget-area hide-for-medium';
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

	/**
	 * Add custom page header from custom fields
	 *
	 * @since 0.3.0
	 * @return void
	 */
	public function add_custom_header() {

		$singular = is_singular( 'page' );
		$id       = get_the_ID();
		$show     = get_field( 'show_header_group', $id );

		if ( true === $show && $singular ) {

			add_filter( 'genesis_attr_entry-title', array( $this, 'add_grid_container_class' ) );
			add_action( 'genesis_entry_header', array( $this, 'open_custom_header' ), 4 );
			add_filter( 'genesis_post_title_output', array( $this, 'custom_header_subtitle' ) );
			add_action( 'genesis_entry_header', array( $this, 'close_custom_header' ), 16 );

		}
	}

	/**
	 * Add custom class to body for custom page header
	 *
	 * @since 0.5.3
	 * @param array $classes The current body classes.
	 * @return array
	 */
	public function add_custom_header_class( $classes ) {

		$singular = is_singular( 'page' );
		$id       = get_the_ID();
		$show     = get_field( 'show_header_group', $id );

		if ( true === $show && $singular ) {

			$classes[] = 'has-custom-post-header';

		}

		return $classes;

	}

	/**
	 * Add grid container class to element.
	 *
	 * @since 0.4.0
	 * @param array $attributes Element html attributes.
	 * @return array
	 */
	public function add_grid_container_class( $attributes ) {

		$attributes['class'] .= ' grid-container';
		return $attributes;

	}

	/**
	 * Open elements for the customer header.
	 * Todo: Manually add image size af4c_page_header_desktop_large to srcset.
	 *
	 * @since 0.4.0
	 * @return void
	 */
	public function open_custom_header() {

		$id      = get_the_ID();
		$fields  = get_field( 'header_group', $id );
		$image   = $fields['image'];
		$thumb   = wp_get_attachment_image( $image['id'], 'full' );
		$output  = '<div class="custom-page-header alignfull grid-container"><div class="header-image grid-x">';
		$output .= $thumb;
		$output .= '<div class="custom-title grid-container">';
		echo wp_kses_post( $output );

	}

	/**
	 * Add custom header subtitle.
	 *
	 * @since 0.4.0
	 * @param string $output Current header output.
	 * @return string
	 */
	public function custom_header_subtitle( $output ) {

		$fields   = get_field( 'header_group', get_the_ID() );
		$subtitle = $fields['subtitle'];
		if ( ! empty( $subtitle ) ) {
			$output .= wp_kses_post( '<span class="subtitle">' . $subtitle . '</span>' );
		}
		return $output;

	}

	/**
	 * Close elements for the customer header.
	 *
	 * @since 0.4.0
	 * @return void
	 */
	public function close_custom_header() {

		echo wp_kses_post( '</div></div></div>' );

	}

	/**
	 * Remove sticky header attributes from agriflex4 header.
	 *
	 * @since 0.5.6
	 * @param array $atts Open and close attributes of header wrapper.
	 * @return array
	 */
	public function remove_sticky_header( $atts ) {

		return array(
			'open'  => '<div class="wrap"><div class="wrap is-anchored"><div class="grid-container"><div class="grid-x grid-padding-x"',
			'close' => '</div></div></div></div>',
		);

	}

	/**
	 * Set default college header logo.
	 *
	 * @since 1.1.7
	 * @param string $logo The full logo output.
	 * @param string $inside The inner HTML of the title.
	 * @param string $logo_html The template string for the default logo.
	 * @param string $home The home URL.
	 * @return string
	 */
	public function default_coals_logo( $logo, $inside, $logo_html, $home ) {

		$logo_field = get_field( 'logos', 'option' );

		// Using a default logo.
		if ( ! $logo_field && ( ! function_exists( 'get_custom_logo' ) || ! has_custom_logo() ) ) {

			$logo_url = COLAF4_DIR_URL . '/images/logo-coals.svg';
			$logo     = sprintf(
				$logo_html,
				$home,
				$name,
				$logo_url
			);

		}

		return $logo;

	}

	/**
	 * Register the Genesis header right widget area.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function register_header_right_widget_area() {

		genesis_register_widget_area(
			[
				'id' => 'header-right',
			]
		);

	}

	/**
	 * Change the open header right widget area structure.
	 *
	 * @since 1.3.0
	 * @param string $open The open HTML content.
	 * @param array  $args The widget area args.
	 * @return string
	 */
	public function header_right_open( $open, $args ) {

		if ( ! empty( $args['open'] ) ) {
			$open = str_replace( 'class="', 'class="cell shrink show-for-medium ', $open ) . '<div class="grid-x grid-padding-x">';
		}

		return $open;

	}

	/**
	 * Change the close header right widget area structure.
	 *
	 * @since 1.3.0
	 * @param string $close The close HTML content.
	 * @param array  $args  The widget area args.
	 * @return string
	 */
	public function header_right_close( $close, $args ) {

		if ( ! empty( $args['close'] ) ) {
			$close = '</div>' . $close;
		}

		return $close;

	}

}

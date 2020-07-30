<?php
/**
 * Load ACF fields.
 *
 * @link       https://github.com/AgriLife/af4-college/blob/master/src/class-custom-fields.php
 * @since      1.4.1
 * @package    af4-college
 * @subpackage af4-college/src
 */

namespace College;

/**
 * Loads required theme assets
 *
 * @package af4-college
 * @since 1.4.1
 */
class Custom_Fields {

	/**
	 * Initialize the class
	 *
	 * @since 1.4.1
	 * @return void
	 */
	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'add_options_between_existing_fields' ) );

	}

	/**
	 * Registers the fields after the theme options load.
	 *
	 * @since 1.4.1
	 * @return void
	 */
	public function add_options_between_existing_fields() {

		add_action( 'acf/init', array( $this, 'footer_logos' ) );

	}

	/**
	 * Registers the theme header logos option.
	 *
	 * @since 1.4.1
	 * @return void
	 */
	public function footer_logos() {

		if ( function_exists( 'acf_add_local_field' ) ) {

			acf_add_local_field(
				array(
					'key'               => 'field_5e14d2dc823a8',
					'label'             => 'Footer Logos',
					'name'              => 'footer_logos',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'collapsed'         => '',
					'min'               => 0,
					'max'               => 2,
					'layout'            => 'block',
					'button_label'      => 'Add Logo',
					'sub_fields'        => array(
						array(
							'key'               => 'field_5e14d324823aa',
							'label'             => 'Image',
							'name'              => 'image',
							'type'              => 'image',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'array',
							'preview_size'      => 'medium',
							'library'           => 'all',
							'min_width'         => '',
							'min_height'        => '',
							'min_size'          => '',
							'max_width'         => '',
							'max_height'        => '',
							'max_size'          => '',
							'mime_types'        => 'png,svg,jpg',
						),
						array(
							'key'               => 'field_5e14d36c823ab',
							'label'             => 'Screen Size',
							'name'              => 'screen_size',
							'type'              => 'select',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'choices'           => array(
								'small'      => 'All',
								'small-only' => 'Small only',
								'medium'     => 'Medium and up',
							),
							'default_value'     => array(
								0 => 'small',
							),
							'allow_null'        => 1,
							'multiple'          => 0,
							'ui'                => 0,
							'return_format'     => 'value',
							'ajax'              => 0,
							'placeholder'       => '',
						),
					),
					'parent'            => 'group_5e14d2d88b326',
				)
			);
		}
	}

}

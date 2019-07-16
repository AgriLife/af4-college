<?php
/**
 * The file that creates a contact widget.
 *
 * @link       https://github.com/AgriLife/af4-college/blob/master/src/class-widget-af4c-contact.php
 * @since      0.1.1
 * @package    af4-college
 * @subpackage af4-college/src
 */

/**
 * Loads theme widgets
 *
 * @package af4-college
 * @since 0.1.1
 */
class Widget_AF4C_Contact extends WP_Widget {

	/**
	 * Default instance.
	 *
	 * @since 0.1.1
	 * @var array
	 */
	protected $default_instance = array(
		'title'   => '',
		'content' => '<div class="icon-location">600 John Kimbrough Blvd, College Station, TX 77843</div><div><a class="icon-phone" href="tel:979-845-4747">(979) 845-4747</a> | <a class="icon-email" href="mailto:aglifesciences@tamu.edu">Contact Us</a></div>',
	);

	/**
	 * Construct the widget
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'college-contact',
			'description'                 => __( 'Contact information for this unit.' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'college_contact', __( 'Contact Us' ), $widget_ops, $control_ops );

	}

	/**
	 * Echoes the widget content
	 *
	 * @since 0.1.1
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$instance = array_merge( $this->default_instance, $instance );
		$title    = $instance['title'];
		$content  = $instance['content'];

		$title = '<div class="title-wrap cell medium-12 small-4-collapse">' . $args['before_title'] . $title . $args['after_title'] . '</div>';

		$args['before_widget'] = str_replace( 'class="widget-wrap', 'class="grid-x widget-wrap', $args['before_widget'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $title );
		}
		echo '<div class="textwidget custom-html-widget cell medium-12">'; // The textwidget class is for theme styling compatibility.
		echo wp_kses(
			$content,
			array(
				'div' => array(
					'class' => array(),
				),
				'a'   => array(
					'class' => array(),
					'href'  => array(),
				),
			)
		);
		echo '</div>';
		echo wp_kses_post( $args['after_widget'] );

	}

	/**
	 * Outputs the settings update form
	 *
	 * @since 0.1.1
	 * @param array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->default_instance );

		$output = '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" class="title widefat" value="%s"/></p><p><textarea id="%s" rows="8" name="%s" class="content widefat">%s</textarea></p>';

		echo wp_kses(
			sprintf(
				$output,
				esc_attr( $this->get_field_id( 'title' ) ),
				esc_attr_e( 'Title:', 'af4-college' ),
				esc_attr( $this->get_field_id( 'title' ) ),
				$this->get_field_name( 'title' ),
				esc_attr( $instance['title'] ),
				$this->get_field_id( 'content' ),
				$this->get_field_name( 'content' ),
				esc_textarea( $instance['content'] )
			),
			array(
				'p'        => array(),
				'label'    => array(
					'for' => array(),
				),
				'input'    => array(
					'type'  => array(),
					'id'    => array(),
					'name'  => array(),
					'class' => array(),
					'value' => array(),
				),
				'textarea' => array(
					'id'    => array(),
					'rows'  => array(),
					'name'  => array(),
					'class' => array(),
				),
			)
		);

	}

	/**
	 * Updates a particular instance of a widget
	 *
	 * @since 0.1.1
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = array_merge( $this->default_instance, $old_instance );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = wp_kses_post( $new_instance['content'] );
		}
		return $instance;

	}
}

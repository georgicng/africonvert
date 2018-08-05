<?php
/**
 * Widget Base Class
 *
 * @package Afriflow
 */


/**
 * Widget base
 */
class Base_Widget extends WP_Widget {

	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;
	public $control_ops;
	public $selective_refresh = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => $this->widget_id,
			'description' => $this->widget_description,
			'customize_selective_refresh' => true
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops, $this->control_ops );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		do_action( 'afriflow_widget_update_before', $instance, $new_instance, $this );

		foreach ( $this->settings as $key => $setting ) {
			switch ( $setting[ 'type' ] ) {
				case 'textarea' :
					if ( current_user_can( 'unfiltered_html' ) )
						$instance[ $key ] = $new_instance[ $key ];
					else
						$instance[ $key ] = wp_kses_data( $new_instance[ $key ] );
				break;
				case 'multicheck' :
					$instance[ $key ] = maybe_serialize( $new_instance[ $key ] );
				break;
				case 'text' :
				case 'checkbox' :
				case 'select' :
				case 'number' :
				case 'colorpicker' :
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				break;
				default :
					$instance[ $key ] = apply_filters( 'afriflow_widget_update_type_' . $setting[ 'type' ], $new_instance[ $key ], $key, $setting );
				break;
			}
		}

		do_action( 'afriflow_widget_update_after', $instance, $new_instance, $this );

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting[ 'std' ];

			switch ( $setting[ 'type' ] ) {
				case 'description' :
					?>
					<p class="description"><?php echo $value; ?></p>
					<?php
				break;
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'checkbox' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>">
							<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="1" <?php checked( 1, esc_attr( $value ) ); ?>/>
							<?php echo $setting[ 'label' ]; ?>
						</label>
					</p>
					<?php
				break;
				case 'multicheck' :
					$value = maybe_unserialize( $value );

					if ( ! is_array( $value ) )
						$value = array();
					?>
					<p><?php echo esc_attr( $setting[ 'label' ] ); ?></p>
					<p>
						<?php foreach ( $setting[ 'options' ] as $id => $label ) : ?>
						<label for="<?php echo sanitize_title( $label ); ?>-<?php echo esc_attr( $id ); ?>">
							<input type="checkbox" id="<?php echo sanitize_title( $label ); ?>-<?php echo esc_attr( $id ); ?>" name="<?php echo $this->get_field_name( $key ); ?>[]" value="<?php echo esc_attr( $id ); ?>" <?php if ( in_array( $id, $value ) ) : ?>checked="checked"<?php endif; ?>/>
							<?php echo esc_attr( $label ); ?><br />
						</label>
						<?php endforeach; ?>
					</p>
					<?php
				break;
				case 'select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting[ 'options' ] as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?>><?php echo esc_attr( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;
				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting[ 'step' ] ); ?>" min="<?php echo esc_attr( $setting[ 'min' ] ); ?>" max="<?php echo esc_attr( $setting[ 'max' ] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'textarea' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
						name="<?php echo $this->get_field_name( $key ); ?>" rows="<?php echo isset( $setting[ 'rows' ] )
						? $setting[ 'rows' ] : 3; ?>"><?php echo esc_html( $value ); ?></textarea>
					</p>
					<?php
				break;
				case 'colorpicker' :
						wp_enqueue_script( 'wp-color-picker' );
						wp_enqueue_style( 'wp-color-picker' );
					?>
						<p style="margin-bottom: 0;">
							<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						</p>
						<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" data-default-color="<?php echo $value; ?>" value="<?php echo $value; ?>" />
						<script>
							jQuery(document).ready(function($){
								$( 'input[name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>"]' ).wpColorPicker();
							});
						</script>
						<p></p>
					<?php
				break;
				case 'image' :
					wp_enqueue_media();
					wp_enqueue_script( 'app-image-widget-admin', get_template_directory_uri() . '/js/source/app-image-widget-admin.js', array( 'jquery' ), '', true );
				?>
					<p style="margin-bottom: 0;">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
					</p>
					<p style="margin-top: 3px;">
						<a href="#" class="button-secondary <?php echo esc_attr( $this->get_field_id( $key ) ); ?>-add"><?php _e( 'Choose Image', 'afriflow' ); ?></a>
					</p>
					<p>
						<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>"value="<?php echo $value; ?>" placeholder="http://" />
					</p>
					<script>
						jQuery(document).on( 'ready', function($) {
							var <?php echo $key; ?> = new cImageWidget.MediaManager({
								target: '<?php echo esc_attr( $this->get_field_id( $key ) ); ?>',
							});
						});
					</script>
				<?php
				break;
				default :
					do_action( 'afriflow_widget_type_' . $setting[ 'type' ], $this, $key, $setting, $instance );
				break;
			}
		}
	}

	
	/**
	 * widget function.
	 *
	 * @see    WP_Widget
	 * @access public
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	 public function widget( $args, $instance ) {}
}

<?php
/**
 * Widget API: WP_Widget_Categories class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Categories widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Af_Category extends Base_Widget {

	/**
	 * Sets up a new Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$this->widget_description = __( 'Display a post categories', 'afriflow' );
        $this->widget_id          = 'afriflow_widget_category';
        $this->widget_name        = __( 'Afriflow: Category', 'afriflow' );

        $this->settings = array(
            'title' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Title:', 'afriflow' )
			),
			'hierarchical' => array(
                'label' => __( 'Show hierarchy', 'afriflow' ),
                'type' => 'checkbox',
                'std'  => 1
            ),
            'count' => array(
                'label' => __( 'Show Post Count', 'afriflow' ),
                'type' => 'checkbox',
                'std'  => 1
            ),
            'limit' => array(
                'type'  => 'number',
                'std'   => 3,
                'min'   => 1,
                'max'   => 5,
                'step'  => 1,
                'label' => __( 'No of Categories to show:', 'afriflow' )
            ),
        );

        parent::__construct();
		
	}

	/**
	 * Outputs the content for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Categories widget instance.
	 */
	public function widget( $args, $instance ) {

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'];

		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$l = ! empty( $instance['limit'] ) ? $instance['limit'] : '5';
	
		$cat_args = array(
			'orderby'      => 'name',
			'pad_count'   => $c,
			'hierarchical' => $h,
			'number' => $l
		);

		$categories = get_categories( $cat_args );
		

?>
		<div class="acc_pannel col-sm-6 col-md-12">
  <div class="acc_tabs_header"> <?php echo $title ?>  <i class="fa fa-minus fa-1x pull-right"></i> </div> 
  <?php
		 foreach ( $categories as $cat ) :
?> 
  <div class="acc_tabs_child"> <a href="blog?cid=<?php echo $cat->term_id ?>"><span><?php echo $cat->name ?></a> </span>  </div>     

  
<?php
		 endforeach;
?>
		</div><!-- /acc-pannel-->
		<?php
	}

}

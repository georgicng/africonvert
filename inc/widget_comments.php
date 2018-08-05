<?php
/**
 * Widget API: WP_Widget_Recent_Comments class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Recent Comments widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Af_Recent_Comments extends Base_Widget {

	/**
	 * Sets up a new Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$this->widget_description = __( 'Show recect Comments', 'afriflow' );
        $this->widget_id          = 'afriflow_widget_comment';
        $this->widget_name        = __( 'Afriflow: Recent Comment', 'afriflow' );

        $this->settings = array(
            'title' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Title:', 'afriflow' )
            ),
            'number' => array(
                'type'  => 'number',
                'std'   => 3,
                'min'   => 1,
                'max'   => 5,
                'step'  => 1,
                'label' => __( 'Number of comments to show:', 'afriflow' )
            ),
        );

        parent::__construct();
	}

	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 */
	public function widget( $args, $instance ) {
		
		$output = '';

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments' );

	
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;

		/**
		 * Filters the arguments for the Recent Comments widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 */
		$comments = get_comments(  array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish',
			'post_type' => 'post'
		) );

		$output .= '<div class="comment_tabs_pannel col-sm-6 col-md-12">';
		if ( $title ) {
			$output .= '<div class="acc_tabs_header">' . $title . '</div>';
		}
		if ( is_array( $comments ) && $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {
				$output .= '<div class="comment_tabs_div">';
				/* translators: comments widget: 1: comment author, 2: post link */
				$output .= sprintf( _x( '%1$s <br> %2$s on %3$s', 'widgets' ),
					'<span class="comment">' . $comment->comment_content . '</span>',
					'<!--a href="members/' . $comment->user_id . '"--><span class="comment-author-link">' . get_comment_author_link( $comment ) . '</span><!--/a-->',
					'<a href="blog/' . $comment->comment_post_ID . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
				);
				$output .= '</div>';
			}
		}
		$output .= '</div>';

		echo $output;
	}

}

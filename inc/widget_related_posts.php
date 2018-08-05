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
class Af_Related_Posts extends Base_Widget
{

    /**
     * Sets up a new Categories widget instance.
     *
     * @since 2.8.0
     * @access public
     */
    public function __construct()
    {
        $this->widget_description = __('Display a related posts', 'afriflow');
        $this->widget_id = 'afriflow_widget_related_posts';
        $this->widget_name = __('Afriflow: Related Posts', 'afriflow');

        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => '',
                'label' => __('Title:', 'afriflow'),
            ),
            'limit' => array(
                'type' => 'number',
                'std' => 3,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'label' => __('No of posts to show:', 'afriflow'),
            ),
            'post' => array(
                'type' => 'number',
                'std' => 3,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'label' => __('Post ID:', 'afriflow'),
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
    public function widget($args, $instance)
    {

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = empty($instance['title']) ? __('SIMILIAR STORIES') : $instance['title'];

        $l = !empty($instance['limit']) ? $instance['limit'] : '5';

        global $post;

        $p = !empty($instance['post']) ? $instance['post'] : $post->ID;

        $type = get_post_type($p);
        $tax = '';
        $terms = null;

        switch ($type) {
            case 'post':
                $tax = 'category';
                $terms = wp_get_post_terms($p, $tax);
                break;

            case 'contests':
                $tax = 'contest_category';
                $terms = wp_get_post_terms($p, $tax);
                break;
            case 'submissions':
                $tax = 'contests';
                $terms = wp_get_post_terms($p, $tax);
                break;
        }


        if (is_null($terms)) {
            return false;
        }

        foreach ($terms as $term) {
            $term_ids[] = $term->term_id;
        }

        // Remove duplicate values from the array
        $unique_array = array_unique($term_ids);

        $args = [
            'post__not_in' => [$p],
            'posts_per_page' => $l, // Note: showposts is depreciated in favor of posts_per_page
            'orderby' => 'title',
            'no_found_rows' => true, // Skip pagination, makes the query faster
            'tax_query' => [
                [
                    'taxonomy' => $tax,
                    'terms' => $unique_array,
                    'include_children' => false,
                ],
            ],
        ];
        $q = new WP_Query($args);
        $default_image = "http://via.placeholder.com/300?text=blank";
        $attachment = wp_get_attachment_image_src(get_alt_image_id());
        if (is_array($attachment)) {
            $default_image = $attachment[0];
        }
        ob_start();
        if ($q->have_posts()) {
?>
            <div class="similer_s_div"> <?php echo $title; ?> </div>
<?php
            while ($q->have_posts()): $q->the_post();
                $image = get_the_post_thumbnail_url(get_the_ID()) ? get_the_post_thumbnail_url(get_the_ID()) : $default_image;
                $link_meta = $this->get_link_meta($type);

?>
		        <div class="col-sm-4 col-md-4" id="">
                    <div class="sh_blog_div">
                        <div class="sh_blog_img"><img src="<?php echo $image ?>" alt="" style="width:100%">
                        </div>
                        <div class="sh_blog_title"><?php the_title();?></div>
                        <div class="sh_blog_read"><a href="<?php echo $link_meta['slug']."/".get_the_ID()?>"><?php echo $link_meta['label']; ?>></a> </div>
                    </div>
                </div>

        <?php
                endwhile;
            } else {
        ?>
        <?php

            }
            wp_reset_query();
            $content = ob_get_clean();
            echo apply_filters($this->widget_id, $content);
       
    }

    public function get_link_meta($post)
    {
        $type = get_post_type($p);
        $slug = "";
        $label = "";

        if($type == "post"){
            $slug = 'blog';
            $label = 'Read More';
        }

        if($type == "submissions"){
            $slug= 'watch';
            $label = 'Watch Now';
        }

        if($type == "contests"){
            $stage = get_field('stage', $p);
            switch ($stage) {
                case 'Submit':
                    $slug = 'submit';
                    $label = 'Enter Now';
                    break;
                case 'Vote':
                    $slug= 'vote';
                    $label = 'Vote Now';
                    break;
                case 'Complete':
                    $slug= 'archive';
                    $label = 'View Now';
                    break;                
            }
        }

        return array(
            'slug' => $slug,
            'label' => $label
        );

    }

}

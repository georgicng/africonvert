<?php
/**
 * Home: Category Image Grid
 *
 * @since afriflow 1.0.0
 */
class Af_Category_Tabs extends Base_Widget {

    public function __construct() {
        $this->widget_description = __( 'Display a tab of post categories', 'afriflow' );
        $this->widget_id          = 'afriflow_widget_category_tab';
        $this->widget_name        = __( 'Afriflow: Categories Tab', 'afriflow' );

        $this->settings = array(
            'title' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Title:', 'afriflow' )
            ),
            'categories' => array(
                'label' => __( 'Catgeories:', 'afriflow' ),
                'type' => 'multicheck',
                'std'  => '',
                'options' => $this->get_categories()
            ),
            'limit' => array(
                'type'  => 'number',
                'std'   => 3,
                'min'   => 1,
                'max'   => 5,
                'step'  => 1,
                'label' => __( 'Number of posts/category:', 'afriflow' )
            ),
        );

        parent::__construct();
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    function widget( $args, $instance ) {
        error_log('widget instance: '.json_encode($instance));
        $this->instance = $instance;

        extract( $args );

        $this->title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );

        $this->categories = isset( $instance[ 'categories' ] ) ? maybe_unserialize($instance[ 'categories' ]) : array();
        $this->limit = isset( $instance[ 'limit' ] ) ? $instance[ 'limit' ] : 3;


        if ( ! is_array( $this->categories ) )
            $this->categories = array();


        ob_start();
        ?>
        <div id="blog_in_div" class="col-sm-12 col-md-12  ">
            <div class="blog_header_div "><?php echo $this->title; ?> </div>
            <div id="blog_div" class="col-sm-12 col-md-12">
            <?php
                $default_image = "http://via.placeholder.com/300?text=blank";
                $attachment = wp_get_attachment_image_src( get_alt_image_id() );
                if(is_array($attachment)){
                    $default_image = $attachment[0];
                }
                $count = 1;
                foreach ( $this->categories as $cat ) :                    
                $category = get_category($cat);
                
                if ($count > 3){
                    break;
                }
            ?>
                <div class= "col-sm-4 col-md-4 blog_div">
                    <div class=" blog_tab_div text-center" id="b_taboff<?php echo $count ?>"><span class="glyphicon glyphicon-music"></span><br><?php echo $category->name ?></div>
                    <div class=" bolg_tab_active text-center" id="b_tabon<?php echo $count ?>"><span class="glyphicon glyphicon-music"></span><br><?php echo $category->name ?></div>
                </div>
            <?php $count++; endforeach;	?>
            </div>
        </div> <!--/From the blog-->
        <div id="blog_main_div">
        <?php                                   
                      
            $args = array(
                'numberposts' => $this->limit,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post_type' => 'post',
            );
            
            $recent_posts = wp_get_recent_posts( $args, OBJECT );
            
            foreach ( $recent_posts as $post ) : 
                $image = get_the_post_thumbnail_url($post->ID)? get_the_post_thumbnail_url($post->ID) : $default_image;
                $categories = get_the_category( $post->ID );
                $category = $categories[0]->name;
        ?>
            <a href="<?php echo "blog/".$post->ID ?>"> 
                <div id="b_contents_div" class="col-md-12">
                    <div class="blog_img text-center">
                        <img src="<?php echo $image ?>" alt="<?php echo $post->post_name ?>" style="width:100%">    
                    </div>
                    <div class="b_contents">
                        <div class="btag"> <?php echo $category; ?>  </div>
                        <div class="b_title"> <?php echo $post->post_title ?>  </div>
                        <!--div class="b_shareit col-sm-5 col-md-5"> <i class=" fa fa-share-alt fa-x"></i> <span>200 Shares  </span>  </div-->
                        <div class="b_comment col-sm-7 col-md-7"> <i class=" fa fa-comments fa-x"></i><span><?php echo $post->comment_count  ?>  Comments  </span>  </div>
                    </div><!--/b_contents-->
                </div>
            </a><!--/b_contents_div -->
        <?php endforeach;	?>
        </div><!-- /blog_maindiv-->
        <?php
                $count = 1;
                foreach ( $this->categories as $cat ) :                    
                $posts = get_posts(
                    array(
                        'posts_per_page' => $this->limit,
                        'category' => $cat
                    )
                );
                
                if ($count > 3){
                    break;
                }
            ?>      
        <div id="blog_main_div_<?php echo $count ?>">
        <?php
                foreach ( $posts as $post ) : 
                    $image = get_the_post_thumbnail_url($post->ID)? get_the_post_thumbnail_url($post->ID) : $default_image;
            ?>
  <a href="blog/<?php echo $post->ID ?>"> 
  <div id="b_contents_div" class="col-md-12">
   <div class="blog_img text-center"><img src="<?php echo $image ?>" alt="<?php echo $post->post_name ?>" style="width:100%">    
   </div>
   <div class="b_contents">
   <div class="b_title"> <?php echo $post->post_title ?>  </div>
    <!--div class="b_shareit col-sm-5 col-md-5"> <i class=" fa fa-share-alt fa-x"></i> <span>200 Shares  </span>  </div-->
    <div class="b_comment col-sm-7 col-md-7"> <i class=" fa fa-comments fa-x"></i><span><?php echo $post->comment_count  ?>  Comments  </span>  </div>
   </div><!--/b_contents-->
  </div>
  </a><!--/b_contents_div -->
  <?php endforeach;	?>
 </div>
 <?php $count++; endforeach; ?>
 <script>
home_side_tab_handler();
</script>
<?php	

        $content = ob_get_clean();

        echo apply_filters( $this->widget_id, $content );
    }


    private function get_categories() {
        $categories = get_categories( );
        $list = array();

        foreach ( $categories as $cat ) {
            $list[ $cat->term_id ] = $cat->name;
        }

        return $list;
    }

}

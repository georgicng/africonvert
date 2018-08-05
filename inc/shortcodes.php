<?php
function contest_tab_shortcode($arg)
{
    extract(shortcode_atts(array(
        'section_title' => "COMPETITIONS",
        'show_ongoing_submit' => true,
        'submit_title' => 'Most Recent',
        'show_ongoing_vote' => true,
        'vote_title' => 'Vote',
        'show_past' => true,
        'past_title' => 'Archive',
        'show_winners' => true,
        'winner_title' => 'Wall of Fame',
    ),
        $arg)
    );

    ob_start();
    ?>
<div class="top_hrader text-center"><?php echo $section_title ?></div>
  <!-- /Tabs-->
  <div id="main_tab_div">
  <?php if ($show_ongoing_submit): ?>
	  <div class=" col-xs-6 col-sm-3 col-md-3 tab_div_active text-center" id="tab_on1">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $submit_title ?>
	  </div>
	  <div class="col-xs-6 col-sm-3 col-md-3 tab_div text-center" id="tab_off1">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $submit_title ?>
	  </div>
  <?php endif;?>
  <?php if ($show_ongoing_vote): ?>
	  <div class=" col-xs-6 col-sm-3 col-md-3 tab_div_active text-center" id="tab_on2">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $vote_title ?>
	  </div>
	  <div class="col-xs-6 col-sm-3 col-md-3 tab_div text-center" id="tab_off2">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $vote_title ?>
	  </div>
  <?php endif;?>
  <?php if ($show_past): ?>
	  <div class=" col-xs-6 col-sm-3 col-md-3 tab_div_active text-center" id="tab_on3">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $past_title ?>
	  </div>
	  <div class="col-xs-6 col-sm-3 col-md-3 tab_div text-center" id="tab_off3">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $past_title ?>
	  </div>
  <?php endif;?>
  <?php if ($show_winners): ?>
	  <div class=" col-xs-6 col-sm-3 col-md-3 tab_div_active text-center" id="tab_on4">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $winner_title ?>
	  </div>
	  <div class="col-xs-6 col-sm-3 col-md-3 tab_div text-center" id="tab_off4">
	  <span class="glyphicon glyphicon-time"></span><br><?php echo $winner_title ?>
	  </div>
  <?php endif;?>
  </div>
	<?php if ($show_ongoing_submit): ?>
	<div id="column_div">
	<?php
        $today = date("Ymd");
        $args = array(
            'post_type' => 'contests',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'stage',
                    'value' => 'Submit',
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'entry_opens',
                        'value' => $today,
                        'compare' => '<=',
                        'type' => 'DATE',
                    ),
                    array(
                        'key' => 'entry_closes',
                        'value' => $today,
                        'compare' => '>=',
                        'type' => 'DATE',
                    ),
                ),
            ),
        );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()):

            while ($the_query->have_posts()): $the_query->the_post();

                ?>

					<div class="col-xs-12 col-sm-6 col-md-4 " id="img_card_div">
					 <div class="card_container">
					  <div class="img_card"><img src="<?php echo wp_get_attachment_image_src(get_post_thumbnail_id())[0] ?>" alt="<?php the_title();?>" style="width:100%">   </div>
					  <div class="img_card_details">
					  <div class="details"> <?php the_title();?> </div>
					  <div class="details"> <?php the_field('price');?>  </div>
					  <div class="enter_link"><a href="submit/<?php the_ID();?> "> ENTER <span class="glyphicon glyphicon-chevron-right"></span></a></div>
					  </div>
					  </div><!--/img_card-->

					 </div><!--/First col-->

					
				<?php endwhile;
            wp_reset_postdata();
        else:
        ?>
		<p class="empty"><?php esc_html_e('Sorry, no post matches your criteria.');?></p>		
		<?php endif;?>
		</div><!--column_div ends-->
	<?php endif;?>
	<?php if ($show_ongoing_vote): ?>
	<div id="column_div_2">
	<?php
        $today = date("Ymd");
        $args = array(
            'post_type' => 'contests',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'stage',
                    'value' => 'Vote',
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'voting_opens',
                        'value' => $today,
                        'compare' => '<=',
                        'type' => 'DATE',
                    ),
                    array(
                        'key' => 'voting_closes',
                        'value' => $today,
                        'compare' => '>=',
                        'type' => 'DATE',
                    ),
                ),
            ),
        );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()):

            while ($the_query->have_posts()): $the_query->the_post();

                ?>
				   
			  <div class="col-xs-12 col-sm-6 col-md-4 " id="img_card_div">
			 <div class="card_container">
			  <div class="img_card"><img src="<?php echo wp_get_attachment_image_src(get_post_thumbnail_id())[0] ?>" alt="<?php the_title();?>" style="width:100%">   </div>
			  <div class="img_card_details">
			  <div class="details"><?php the_title();?> </div>
			  <div class="details"><?php the_field('price');?>   </div>
			  <div class="enter_link"><a href="vote/<?php the_ID();?>"> Vote <span class="glyphicon glyphicon-chevron-right"></span></a></div>
			  </div>
			  </div><!--/img_card-->

			 </div><!--/First col-->

			 
			 <?php endwhile;
            wp_reset_postdata();
        else:
        ?>
		<p class="empty"><?php esc_html_e('Sorry, no post matches your criteria.');?></p>
		<?php endif;?>
		</div><!--column_div_2 ends-->
  <?php endif;?>
  <?php if ($show_past): ?>
  <div id="column_div_3">
  <?php
        $args = array(
            'post_type' => 'contests',
            'meta_query' => array(
                array(
                    'key' => 'stage',
                    'value' => 'Complete',
                ),
            ),
        );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()):

            while ($the_query->have_posts()): $the_query->the_post();

                ?>
				 

			 <div class="col-xs-12 col-sm-6 col-md-4 " id="img_card_div">
			 <div class="card_container">
			  <div class="img_card"><img src="<?php echo wp_get_attachment_image_src(get_post_thumbnail_id())[0] ?>" alt="<?php the_title();?>" style="width:100%">   </div>
			  <div class="img_card_details">
			  <div class="details"><?php the_title();?> </div>
			  <div class="details"><?php the_field('price');?>   </div>
			  <div class="enter_link"><a href="archive/<?php the_ID();?>"> View <span class="glyphicon glyphicon-chevron-right"></span></a></div>
			  </div>
			  </div><!--/img_card-->

			 </div><!--/First col-->
			 <?php endwhile;
            wp_reset_postdata();
        else:
        ?>
		<p class="empty"><?php esc_html_e('Sorry, no post matches your criteria.');?></p>
		<?php endif;?>
		</div><!--column_div_3 ends-->

  <?php endif;?>
  <?php if ($show_winners): ?>
  <div id="column_div_4">
  <?php
        $today = date("Ymd");
        $args = array(
            'post_type' => 'contests',

        );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()):

            while ($the_query->have_posts()): $the_query->the_post();

                ?>
				
			 <div class="col-xs-12 col-sm-6 col-md-4 " id="img_card_div">
			 <div class="card_container">
			  <div class="img_card"><img src="<?php echo wp_get_attachment_image_src(get_post_thumbnail_id())[0] ?>" alt="<?php the_title();?>" style="width:100%">   </div>
			  <div class="img_card_details">
			  <div class="details"><?php the_title();?> </div>
			  <div class="details"><?php the_field('price');?>  </div>
			  <div class="enter_link"><a href="hof/<?php the_ID();?>"> ENTER <span class="glyphicon glyphicon-chevron-right"></span></a></div>
			  </div>
			  </div><!--/img_card-->

			 </div><!--/First col-->
			 
			 <?php endwhile;
            wp_reset_postdata();
        else:
        ?>
		<p class="empty"><?php esc_html_e('Sorry, no post matches your criteria.');?></p>
		<?php endif;?>
		</div><!--column_div_4 ends-->
  <?php endif;?>
  <script>
jQuery(function(){
	home_tab_handler();
}); 
</script>
<?php
$content = ob_get_clean();

    return $content;
}
add_shortcode('contest_tab', 'contest_tab_shortcode');

function ad_banner_shortcode($arg)
{
    extract(shortcode_atts(array(
        'src' => "wp-content/themes/africonvert/img/ad.jpg",
    ),
        $arg)
    );

    ob_start();
    ?>
 <div class="ads_div">
 <img src="<?php echo $src ?>" alt="Norway">
 </div>
 <?php
$content = ob_get_clean();
    return $content;
}
add_shortcode('ad_banner', 'ad_banner_shortcode');
<?php
if ( ! class_exists ( 'WP_List_Table' ) ) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
* List table class
*/
class Submission_Stat extends WP_List_Table {

  function __construct() {
      parent::__construct( array(
          'singular' => 'Contest Stat',
          'plural'   => 'Contest Stats',
          'ajax'     => false
      ) );
  }

  
  /**
   * Render the checkbox column
   *
   * @param  object  $item
   *
   * @return string
   */
  function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="stat_id[]" value="%d" />', $item['id']
        );
    }

	/**
   * Render the designation name column
   *
   * @param  object  $item
   *
   * @return string
   */
  function column_user( $item ) {    
    return sprintf( '<a href="%1$s"><strong>%2$s</strong></a>', admin_url( 'user-edit.php?user_id=' . $item['user_id'] ), $item['user'] );;
  }
  
function column_submission( $item ) {
    $winner = get_field('winner', $item['id']);
    $candidate = get_field('candidate', $item['id']);
    $tag = "";
    
    if($winner){
        $tag = " -- Winner";
    }
    
    if(!$winner && $candidate){
        $tag = " -- Candidate";
        $title = sprintf( '<a href="%1$s"><strong>%2$s</strong></a>', admin_url( 'post.php?action=edit&post=' . $item['id'] ), $item['submission'].$tag );
        $actions = array(
            'pick'      => sprintf('<a href="?post=%s&action=%s&winner=%s">Winner</a>',$_REQUEST['post'],'select_winner', $item['id']),
            //'delete'    => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['post'],'delete',$item['id']),
        );
        return sprintf('%1$s %2$s', $title, $this->row_actions($actions) );
    }
    
    
    return sprintf( '<a href="%1$s"><strong>%2$s</strong></a>', admin_url( 'post.php?action=edit&post=' . $item['id'] ), $item['submission'].$tag );
}


  /**
   * Default column values if no callback found
   *
   * @param  object  $item
   * @param  string  $column_name
   *
   * @return string
   */
  function column_default( $item, $column_name ) {
    switch ( $column_name ) {
        case 'vote_count':
        case 'like_count':
        case 'view_count':
          return $item[ $column_name ];
        default:
          print_r( $item, true );
          return isset( $item[$column_name] ) ? $item[$column_name] : ''; //Show the whole array for troubleshooting purposes
      }

  }

  /**
   * Get the column names
   *
   * @return array
   */
  function get_columns() {
    $columns = array(
        'cb'           => '<input type="checkbox" />',
        'user'      => __( 'User ID', 'gai' ),
        'submission'      => __( 'Entry Name', 'gai' ),
        'vote_count'      => __( 'Vote Count', 'gai' ),
        'like_count'      => __( 'Like Count', 'gai' ),
        'view_count'      => __( 'Total Views', 'gai' ),

    );

    return $columns;
}

/**
   * Get sortable columns
   *
   * @return array
   */
  function get_sortable_columns() {
    $sortable_columns = array(
        'submission' => array( 'submission', true ),
        'user' => array( 'user', true ),
        'like_count' => array( 'like_count', true ),
        'vote_count' => array( 'vote_count', true ),
        'view_count' => array( 'view_count', true ),
    );

    return $sortable_columns;
}

  /**
 * Retrieve customer’s data from the database
 *
 * @param int $per_page
 * @param int $page_number
 *
 * @return mixed
 */
public static function get_items( $per_page = 5, $page_number = 1 ) {
    
    global $wpdb;
    $id = get_the_id()? get_the_id(): $_REQUEST['post'];
	$posts = $cpt_onomy->get_objects_in_term( $id , 'contests' );
    $sql = "SELECT p.ID,  u.user_login, p.post_title,". 
    "(select count(id) from `{$wpdb->prefix}gs_vote_post` as v where v.post_id = p.ID) as vote_count,". 
    "(select sum(`like`) from `{$wpdb->prefix}gs_like_post` as l where l.post_id = p.ID) as like_count,". 
    "(select sum(dislike) from `{$wpdb->prefix}gs_like_post` as l where l.post_id = p.ID) as dislike_count". 
    "FROM `{$wpdb->prefix}posts` as p".
    "left join `{$wpdb->prefix}users` as u on p.post_author = u.ID".
    "where p.ID in (".implode(",", $posts).")";
    
    if ( ! empty( $_REQUEST['orderby'] ) ) {
        $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
        $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
      } else {
          $sql .= ' ORDER BY vote_count DESC';
      }

    $sql .= " LIMIT $per_page";    
    $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

    $result = $wpdb->get_results( $sql, 'ARRAY_A' );
    write_log("Stat query result".json_encode( $result ));
    array_walk($result, function(&$item){        
        $item['view_count'] = 0;
    });

      //loop hrough and add total contest entry per contest and total views per contest
    
    return $result;
}

/**
 * Returns the count of records in the database.
 *
 * @return null|string
 */
public static function record_count() {
    global $wpdb;
    $id = get_the_id()? get_the_id(): $_REQUEST['post'];
	$posts = $cpt_onomy->get_objects_in_term( $id , 'contests' );
    $sql = "SELECT p.ID as id,  u.user_login as user, p.post_title as title,". 
    "(select count(id) from `{$wpdb->prefix}gs_vote_post` as v where v.post_id = p.ID) as vote_count,". 
    "(select sum(`like`) from `{$wpdb->prefix}gs_like_post` as l where l.post_id = p.ID) as like_count,". 
    "(select sum(dislike) from `{$wpdb->prefix}gs_like_post` as l where l.post_id = p.ID) as dislike_count". 
    "FROM `{$wpdb->prefix}posts` as p".
    "left join `{$wpdb->prefix}users` as u on p.post_author = u.ID".
    "where p.ID in (".implode(",", $posts).")";
    $wpdb->get_results( $sql );
    return $wpdb->num_rows;
}

  
  /**
   * Prepare the class items
   *
   * @return void
   */
  function prepare_items() {

      $columns               = $this->get_columns();
      $hidden                = array( );
      $sortable              = $this->get_sortable_columns();
      $this->_column_headers = array( $columns, $hidden, $sortable );

      $per_page              = 10;
      $current_page          = $this->get_pagenum();
      $total_items  = self::record_count();
    
      $this->set_pagination_args( [
        'total_items' => $total_items, //WE have to calculate the total number of items
        'per_page'    => $per_page //WE have to determine how many items to show on a page
      ] );

      $this->items  = $this->get_items( $per_page, $current_page );
      
  }

  /**
   * Message to show if no designation found
   *
   * @return void
   */
  function no_items() {
    _e( 'No entries.', 'gai' );
}


}
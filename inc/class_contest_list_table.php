<?php
if ( ! class_exists ( 'WP_List_Table' ) ) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
* List table class
*/
class Contest_Stat extends WP_List_Table {

  function __construct() {
      parent::__construct( array(
          'singular' => 'Stat',
          'plural'   => 'Stats',
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
  function column_contest( $item ) {
    if($item['contest'] == "Hi"){
        $x = sprintf( '<a href="%1$s"><strong>%2$s</strong></a>', admin_url( 'post.php?action=edit&post=' . $item['id'] ), $item['contest'] );
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&book=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        return sprintf('%1$s %2$s', $x, $this->row_actions($actions) );
    } else {
        return sprintf( '<a href="%1$s"><strong>%2$s</strong></a>', admin_url( 'post.php?action=edit&post=' . $item['id'] ), $item['contest'] );        
    }
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
        case 'contest':
        case 'submission_count':
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
        'contest'      => __( 'Contest Name', 'gai' ),
        'submission_count'      => __( 'Submission Count', 'gai' ),
        'vote_count'      => __( 'Vote Count', 'gai' ),
        //'like_count'      => __( 'Like Count', 'gai' ),
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
        'contest' => array( 'contest', true ),
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
	$table_name = $wpdb->prefix . "gs_vote_post";
    $sql = "SELECT v.contest_id AS `id`, c.post_title AS `contest`, count(Distinct v.post_id) AS `submission_count`, count(v.id) AS `vote_count`"
    ." FROM `{$wpdb->prefix}gs_vote_post` as v" 
    ." left join `{$wpdb->prefix}posts` as c on v.contest_id = c.ID" 
    ." left join `{$wpdb->prefix}posts` as s on v.post_id = s.ID"
    ." group by v.contest_id";
    
    if ( ! empty( $_REQUEST['orderby'] ) ) {
        $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
        $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
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

    $sql = "SELECT * FROM `{$wpdb->prefix}gs_vote_post` as v " 
    ."left join `{$wpdb->prefix}posts` as c on v.contest_id = c.ID " 
    ."left join `{$wpdb->prefix}posts` as s on v.post_id = s.ID "
    ."group by v.contest_id";

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
        _e( 'No Contests.', 'gai' );
    }


}
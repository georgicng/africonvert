<?php
// No, Thanks. Direct file access forbidden.
! defined( 'ABSPATH' ) AND exit;
// INIT
if (class_exists('wp_custom_post_status')){
    add_action( 'after_setup_theme', array( 'Rejected_post_status', 'init' ) );
    class Rejected_post_status extends wp_custom_post_status
    {
        /**
         * @access protected
         * @var string
         */
        static protected $instance;
        /**
         * Creates a new instance. Called on 'after_setup_theme'.
         * May be used to access class methods from outside.
         *
         * @return void
         */
        static public function init()
        {
            null === self :: $instance and self :: $instance = new self;
            return self :: $instance;
        }
        public function __construct()
        {
            // Set your data here. Only "$post_status" is required.
            $this->post_status = 'Rejected';
            // The post types where you want to add the custom status. Allowed are string and array
            $this->post_type = 'submissions';
            // @see parent class: defaults inside add_post_status()
            $this->args = array();
            parent :: __construct();
        }
    }
}

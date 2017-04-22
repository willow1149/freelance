<?php

/*
Plugin Name: Plugin Test
Plugin URI: http://localhost/migara/
Description: This Is A Test Plugin
Author: Migara Karunarathne
Version: 1.0
Author URI: 
*/

// Instantiate new class
$migara = new myplugin();

class myplugin{

	/**
	* DB Version
	*
	* @since 1.0
	* @uses Get DB Version when installing and unsinstalling
	*/
	protected $db_vid = '1.0';
	
	public function __construct(){
		
		//Adding plugin to Menu
		add_action( 'admin_menu', array( &$this, 'add_plugin' ) );

		//Adding Styles and Scripts
		add_action( 'admin_print_styles', array( &$this, 'stylesandscripts' ) );

		//Register Activation Hook
		register_activation_hook(__FILE__, array(&$this, 'install_plugin'));

		//Register Deactivation Hook
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate_plugin'));

		//Uninstall Plugin Hook
	}

	/**
	* Create Menu in Admin Bar
	* 
	* @since 1.0
	* @uses Create Menu in Admin Bar
	*/
	public function add_plugin(){
		add_menu_page( __('Migara','plugintest') , __('Migara','plugintest') , 'manage_options' , 'plugin-test' , array( &$this, 'migara' ) , plugins_url( 'plugin/images/icon.png' ) );
	}

	/**
	* Plugin Body
	* 
	* @since 1.0
	* @uses Plugin Body View
	*/
	public function migara(){
		echo 'Hello World!!!!';
	}

	/**
	* Add Styles and Scripts
	*
	* @since 1.0
	* @uses Add Styles and Scripts to plugin
	*/
	public function stylesandscripts(){
		wp_enqueue_style('migara.css' , plugins_url('/inc/css/migara.css', __FILE__), array(), '1.0');
		wp_enqueue_script('jquery.js' , plugins_url('/inc/js/jquery.min.js',__FILE__), array('jquery'),'3.2.1');
		wp_enqueue_script('scripts.js' , plugins_url('/inc/js/scripts.min.js',__FILE__), array('jquery'),'1.0');
	}

	/**
	* Installing Tables when installing Plugin
	*
	* @uses 1.0
	* @uses Installing Tables when installing Plugin
	*/
	public function install_db(){
		global $wpdb;

		$tbl_migara = $wpdb->prefix . 'tbl_migara';

		// Explicitly set the character set and collation when creating the tables
        $charset = ( defined( 'DB_CHARSET' && '' !== DB_CHARSET ) ) ? DB_CHARSET : 'utf8';
        $collate = ( defined( 'DB_COLLATE' && '' !== DB_COLLATE ) ) ? DB_COLLATE : 'utf8_general_ci';

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $tbl_sql = "CREATE TABLE IF NOT EXISTS $tbl_migara ( 
        	`id` INT(10) NOT NULL AUTO_INCREMENT ,
        	`name` VARCHAR(45) NOT NULL ,
        	`email` VARCHAR(45) NOT NULL ,
        	`tel` VARCHAR(45) NOT NULL ,
        	PRIMARY KEY (`id`)
        	)ENGINE = InnoDB DEFAULT CHARACTER SET $charset COLLATE $collate AUTO_INCREMENT=1;";

        dbDelta($tbl_sql);
	}

	/**
	* Installing Plugin Function
	*
	* @since 1.0
	* @uses Checking whether Current User can Install Plugin & Install DB and Activate Plugin
	*/
	public function install_plugin(){

		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		// Add a database version to help with upgrades and run SQL install
        if ( !get_option( 'db_vid' ) ) {
            update_option( 'db_vid', $this->db_vid );
            $this->install_db();
        }

        // If database version doesn't match, update and run SQL install
        if ( version_compare( get_option( 'db_vid' ), $this->db_vid, '<' ) ) {
            update_option( 'db_vid', $this->db_vid );
            $this->install_db();
        }
	}

	/**
	* Deactivating Plugin
	*
	* @since 1.0
	* @uses Deactivating Plugin and Deleting Database version 
	*/
	public function deactivate_plugin(){
		global $wpdb;

		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		//Delete any options thats stored
        delete_option( 'db_vid');

        $tbl_migara = $wpdb->prefix . 'tbl_migara';
        
        //Drop Table
        $wpdb->query("DROP TABLE IF EXISTS $tbl_migara");
	}

	/**
	* Uninstalling Plugin
	*
	* @since 1.0
	*/
	public function uninstall_plugin(){
		global $wpdb;

		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		//Delete any options thats stored
        delete_option( 'db_vid');
	}
}
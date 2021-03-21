<?php 
/*
Plugin Name: OpenEd.ca Duplicator
Plugin URI:  https://github.com/woodwardtw/opened-duplicator
Description: Let's clone sites via gravity form & NS Cloner
Version:     2.0
Author:      Tom Woodward
Author URI:  http://opened.ca
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: opened-duplicator

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'opened_duplicator_scripts');

$form_id = RGFormsModel::get_form_id('duplicate site');

function opened_duplicator_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('opened-dup-main-js', plugin_dir_url( __FILE__) . 'js/opened-dup-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'opened-dup-main-css', plugin_dir_url( __FILE__) . 'css/opened-dup-main.css');
}

//LOAD THE OTHER PAGES
include_once( plugin_dir_path( __FILE__ ) . 'form-specific.php' ); //gravity form specific elements
include_once( plugin_dir_path( __FILE__ ) . 'clone-details.php' );//display details on clone page view
include_once( plugin_dir_path( __FILE__ ) . 'cpt.php' ); //create clone custom post type

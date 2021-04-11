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


//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

/**
 * THIS GIVES YOU THE OPTION TO PICK FORMS FROM THE OPTIONS AREA
 * Populate ACF select field options with Gravity Forms forms
 * from https://gist.github.com/psaikali/2b29e6e83f50718625af27c2958c828f
 */
function acf_populate_gf_forms_ids( $field ) {
  //write_log($field);
  if ( class_exists( 'GFFormsModel' ) ) {
    $choices = [];
    foreach ( \GFFormsModel::get_forms() as $form ) {
      $choices[ $form->id ] = $form->title;
    }
    $field['choices'] = $choices;
  }

  return $field;
}

add_filter( 'acf/load_field/key=field_60574d267b426', 'acf_populate_gf_forms_ids' );


add_action('wp_enqueue_scripts', 'opened_duplicator_scripts');

//$form_id = get_field('gravity_form_id', 'option');//get the form ID 

function opened_duplicator_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('opened-dup-main-js', plugin_dir_url( __FILE__) . 'js/opened-dup-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'opened-dup-main-css', plugin_dir_url( __FILE__) . 'css/opened-dup-main.css');
}

//CREATE OPTIONS PAGE
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'Clone Zone Settings',
        'menu_title'    => 'Cloner Settings',
        'menu_slug'     => 'clone-zone-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}



//LOAD THE OTHER PAGES
include_once( plugin_dir_path( __FILE__ ) . 'form-specific.php' ); //gravity form specific elements
include_once( plugin_dir_path( __FILE__ ) . 'acf.php' ); //gravity form specific elements
include_once( plugin_dir_path( __FILE__ ) . 'cpt.php' ); //create clone custom post type
include_once( plugin_dir_path( __FILE__ ) . 'clone-details.php' );//display details on clone page view






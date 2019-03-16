<?php 
/*
Plugin Name: OpenEd Duplicator
Plugin URI:  https://github.com/
Description: Let's clone sites via gravity form
Version:     1.0
Author:      Tom Woodward
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: opened-duplicator

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// add_action('wp_enqueue_scripts', 'prefix_load_scripts');

// function load_dictionary_tooltip_script() {                           
//     $deps = array('jquery');
//     $version= '1.0'; 
//     $in_footer = true;    
//     wp_enqueue_script('prefix-main-js', plugin_dir_url( __FILE__) . 'js/prefix-main.js', $deps, $version, $in_footer); 
//     wp_enqueue_style( 'prefix-main-css', plugin_dir_url( __FILE__) . 'css/prefix-main.css');
// }

add_action( 'gform_after_submission_1', 'gform_site_cloner', 10, 2 );//specific to the gravity form id

function gform_site_cloner($entry, $form){
	$clone_source_id = get_blog_id_from_url(rgar( $entry, '1' ));//takes url of the site and gets you the ID made for subdomains
    $_POST =  [
	      'action'         => 'process',
	      'clone_mode'     => 'core',
	      'source_id'      => $clone_source_id, //specific to the form entry fields and should resolve to the ID site to copy
	      'target_name'    => rgar( $entry, '2' ), //specific to the form entry fields - need to parallel site url restrictions
	      'target_title'   => rgar( $entry, '3' ), //specific to the form entry fields
	      'disable_addons' => true,
	      'clone_nonce'    => wp_create_nonce('ns_cloner')
	  ];
	
	// Setup clone process and run it.
	$ns_site_cloner = new ns_cloner();
	$ns_site_cloner->process();

	$site_id = $ns_site_cloner->target_id;
	$site_info = get_blog_details( $site_id );
	if ( $site_info ) {
		// Clone successful!
	}
}


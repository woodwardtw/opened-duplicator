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


add_action('wp_enqueue_scripts', 'opened_duplicator_scripts');

function opened_duplicator_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('opened-dup-main-js', plugin_dir_url( __FILE__) . 'js/opened-dup-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'opened-dup-main-css', plugin_dir_url( __FILE__) . 'css/opened-dup-main.css');
}

add_action( 'gform_after_submission_1', 'gform_site_cloner', 10, 2 );//specific to the gravity form id

function gform_site_cloner($entry, $form){
    $_POST =  [
	      'action'         => 'process',
	      'clone_mode'     => 'core',
	      'source_id'      => rgar( $entry, '1' ), //specific to the form entry fields and should resolve to the ID site to copy
	      'target_name'    => rgar( $entry, '3' ), //specific to the form entry fields - need to parallel site url restrictions URL/DOMAIN
	      'target_title'   => rgar( $entry, '2' ), //specific to the form entry fields TITLE
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



//clone custom post type

// Register Custom Post Type clone
// Post Type Key: clone

function create_clone_cpt() {

  $labels = array(
    'name' => __( 'Clones', 'Post Type General Name', 'textdomain' ),
    'singular_name' => __( 'Clone', 'Post Type Singular Name', 'textdomain' ),
    'menu_name' => __( 'Clone', 'textdomain' ),
    'name_admin_bar' => __( 'Clone', 'textdomain' ),
    'archives' => __( 'Clone Archives', 'textdomain' ),
    'attributes' => __( 'Clone Attributes', 'textdomain' ),
    'parent_item_colon' => __( 'Clone:', 'textdomain' ),
    'all_items' => __( 'All Clones', 'textdomain' ),
    'add_new_item' => __( 'Add New Clone', 'textdomain' ),
    'add_new' => __( 'Add New', 'textdomain' ),
    'new_item' => __( 'New Clone', 'textdomain' ),
    'edit_item' => __( 'Edit Clone', 'textdomain' ),
    'update_item' => __( 'Update Clone', 'textdomain' ),
    'view_item' => __( 'View Clone', 'textdomain' ),
    'view_items' => __( 'View Clones', 'textdomain' ),
    'search_items' => __( 'Search Clones', 'textdomain' ),
    'not_found' => __( 'Not found', 'textdomain' ),
    'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
    'featured_image' => __( 'Featured Image', 'textdomain' ),
    'set_featured_image' => __( 'Set featured image', 'textdomain' ),
    'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
    'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
    'insert_into_item' => __( 'Insert into clone', 'textdomain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this clone', 'textdomain' ),
    'items_list' => __( 'Clone list', 'textdomain' ),
    'items_list_navigation' => __( 'Clone list navigation', 'textdomain' ),
    'filter_items_list' => __( 'Filter Clone list', 'textdomain' ),
  );
  $args = array(
    'label' => __( 'clone', 'textdomain' ),
    'description' => __( '', 'textdomain' ),
    'labels' => $labels,
    'menu_icon' => '',
    'supports' => array('title', 'editor', 'revisions', 'author', 'trackbacks', 'custom-fields', 'thumbnail',),
    'taxonomies' => array(),
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'can_export' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'exclude_from_search' => false,
    'show_in_rest' => true,
    'publicly_queryable' => true,
    'capability_type' => 'post',
    'menu_icon' => 'dashicons-universal-access-alt',
  );
  register_post_type( 'clone', $args );
  
  // flush rewrite rules because we changed the permalink structure
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
add_action( 'init', 'create_clone_cpt', 0 );


//GET URL OF CLONE SITE
function acf_fetch_site_url(){
  global $post;
  $html = '';
  $site_url = get_field('site_url');
    if( $site_url) {      
      $html = $site_url;  
     return $html;    
    }

}

//GET SITE ID OF CLONE SITE
function build_site_clone_button($content){
	global $post;
    if ($post->post_type === 'clone'){
        $url = acf_fetch_site_url($post->ID);
        $parsed = parse_url($url);
    	$site_id = get_blog_id_from_url($parsed['host']);	
    	return $content . '<a class="dup-button" href="https://opened.ca/clone-zone?clone=' . $site_id . '">Clone it to own it!</a>';
    }
    else {
        return $content;
    }
}

add_filter( 'the_content', 'build_site_clone_button' );


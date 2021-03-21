<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*

THIS IS THE FUNCTION THAT BUILDS THE CUSTOM POST TYPE "CLONE"

*/



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
    'taxonomies' => array('category'),
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

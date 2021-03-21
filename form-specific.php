<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*

THESE ARE THE FUNCTIONS THAT PLAY OFF GRAVITY FORMS

*/


//DOES THE DUPLICATION
add_action( 'gform_after_submission_' . $form_id, 'gform_site_cloner', 10, 2 );//specific to the gravity form id

function gform_site_cloner($entry, $form){
    //**FROM https://neversettle.it/documentation/ns-cloner/call-ns-cloner-copy-sites-plugins **//

 $request = array(
      'clone_mode'     => 'core',
      'source_id'      => rgar( $entry, '1' ), // any blog/site id on network
      'target_name'    => rgar( $entry, '3' ),
      'target_title'   => rgar( $entry, '2' ),
      'do_copy_posts' =>  '1',
      'post_types_to_clone' => 'page',
      'post_types_to_clone' => 'post',
      //'debug'          => 1 // optional: enables logs
  );



    // Method 1: immediate.
    // ###################

    // Register request with the cloner.
    foreach ( $request as $key => $value ) {
       ns_cloner_request()->set( $key, $value );
    }

    // Get the cloner process object.
    $cloner = ns_cloner()->process_manager;

    // Begin cloning.
    $cloner->init();

    // Check for errors (from invalid params, or already running process).
    $errors = $cloner->get_errors();
    if ( ! empty( $errors ) ) {
       // Handle error(s) and exit
    }

    // Last you'll need to poll for completion to run the cleanup process
    // when content is done cloning. Could be via AJAX to avoid timeout, or like:
    do {
       // Attempt to run finish, if content is complete.
       $cloner->maybe_finish();
       $progress = $cloner->get_progress();
       // Pause, so we're not constantly hammering the server with progress checks.
       sleep( 5 );
    } while ( 'reported' !== $progress['status'] );

    // Once you've verified that $progress['status'] is 'reported',
    // you can get access the array of report data (whether successful or failed) via:
    $reports = ns_cloner()->report->get_all_reports();

    //add acf examples page items

    //REDIRECT TO THE CREATED SITE
    opened_cloner_redirect(rgar( $entry, '3' ));

}

function opened_cloner_redirect($name){
    $base_url = network_site_url();
    $protocols = array('http://', 'https://', 'http://www.', 'www.');
    $url =  str_replace($protocols, '', $base_url);
    wp_redirect('https://' . $name . '.' . $url . 'wp-admin' ); 
    exit;
}



//add created sites to cloner post type
add_action( 'gform_after_submission_' . $form_id, 'gform_new_site_to_acf', 10, 2 );//specific to the gravity form id

function gform_new_site_to_acf($entry, $form){
    $form_title = rgar( $entry, '2' );
    $form_url = rgar( $entry, '3' );
    $clone_form_id = (int)rgar( $entry, '1');
   
     $posts = get_posts( 'numberposts=99&post_status=publish&post_type=clone' ); 
        foreach ( $posts as $post ) {
            $url = get_field('site_url', $post->ID);
            $main = parse_url($url);//probably need to add a check for trailing slash
            $arg = array(
                'domain' => $main['host'],
                'path' => $main['path']
            );
            $blog_details = get_blog_details($arg);

            $clone_id = (int)$blog_details->blog_id;  

            if ($clone_id === $clone_form_id){
                $post_id = $post->ID;
            }
        }
    
    $base_url = network_site_url();

    $row = array(
        'name'   => $form_title,
        'url'  =>  $base_url . '/' .$form_url,// need to change if not sub domain
        'description' => '',
        'display' => 'False'
    );

    $i = add_row('examples', $row, $post_id);
}

//GRAVITY FORM PROVISIONING BASED ON CLONE POSTS https://docs.gravityforms.com/gform_pre_render/#1-populate-choices
add_filter( 'gform_pre_render_'.$form_id, 'populate_posts' );
add_filter( 'gform_pre_validation_'.$form_id, 'populate_posts' );
add_filter( 'gform_pre_submission_filter_'.$form_id, 'populate_posts' );
add_filter( 'gform_admin_pre_render_'.$form_id, 'populate_posts' );
function populate_posts( $form ) {
 
    foreach ( $form['fields'] as &$field ) {
 
        if ( $field->id != 1 ) {
            continue;
        }
 
        // you can add additional parameters here to alter the posts that are retrieved
        // more info: http://codex.wordpress.org/Template_Tags/get_posts
        $posts = get_posts( 'numberposts=55&post_status=publish&post_type=clone' );
 
        $choices = array();
 
        foreach ( $posts as $post ) {
            $url = get_field('site_url', $post->ID);
            // $parsed = parse_url($url);
            // $clone_id = get_blog_id_from_url($parsed['host']);

            $main = parse_url($url);//probably need to add a check for trailing slash
            $arg = array(
                'domain' => $main['host'],
                'path' => $main['path']
            );
            $blog_details = get_blog_details($arg);

            $clone_id = $blog_details->blog_id;   

            $choices[] = array( 'text' => $post->post_title, 'value' => $clone_id);
        }
 
        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a site to clone';
        $field->choices = $choices;
 
    }
 
    return $form;
}


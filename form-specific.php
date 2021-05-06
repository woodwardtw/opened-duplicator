<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*

THESE ARE THE FUNCTIONS THAT PLAY OFF GRAVITY FORMS

*/

$form_id = get_field('cloner_form', 'option');//get the form ID 


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
      //'do_copy_users' => '0',
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

// function confirm_admin_email($site_url){
    // remove_action( 'add_option_new_admin_email', 'update_option_new_admin_email' );
    // remove_action( 'update_option_new_admin_email', 'update_option_new_admin_email' );
     
    // /**
    //  * Disable the confirmation notices when an administrator
    //  * changes their email address.
    //  *
    //  * @see http://codex.wordpress.com/Function_Reference/update_option_new_admin_email
    //  */
    // function wpdocs_update_option_new_admin_email( $old_value, $value ) {
     
    //     update_option( 'admin_email', $value );
    // }
    // add_action( 'add_option_new_admin_email', 'wpdocs_update_option_new_admin_email', 10, 2 );
    // add_action( 'update_option_new_admin_email', 'wpdocs_update_option_new_admin_email', 10, 2 );


// }

function opened_cloner_redirect($name){
    $base_url = network_site_url();
    $protocols = array('http://', 'https://', 'http://www.', 'www.');
    $url =  str_replace($protocols, '', $base_url);
    update_email_address('https://' . $name . '.' . $url);
    wp_redirect('https://' . $name . '.' . $url . 'wp-admin' ); 
    exit;
}


//because of weird issue admin emails not switching on opened site (can't duplicate locally)
function update_email_address($url){
    write_log($url);
    $current_user = wp_get_current_user();
    write_log($current_user);
    $email = $current_user->user_email;
    write_log($email);
    $blog_id = get_blog_id_from_url($url, '/');
    write_log($blog_id);
    switch_to_blog( $blog_id );
    write_log(update_option('admin_email', $email));
    restore_current_blog();
}

//add created sites to cloner post type
add_action( 'gform_after_submission_' . $form_id, 'gform_new_site_to_acf', 10, 2 );//specific to the gravity form id

function gform_new_site_to_acf($entry, $form){
    $clone_form_id = (int)rgar( $entry, '1');
    $form_title = rgar( $entry, '2' );
    $form_url = rgar( $entry, '3' );
   
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
        'url'  =>  opened_cloner_url($form_url),// need to change if not sub domain
        'description' => '',
        'display' => 'False'
    );

    $i = add_row('examples', $row, $post_id);
}

function opened_cloner_url($name){
    $base_url = network_site_url();
    $protocols = array('http://', 'https://', 'http://www.', 'www.');
    $url =  str_replace($protocols, '', $base_url);
    return 'https://' . $name . '.' . $url;
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
                'path' => '/',
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


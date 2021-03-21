<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*

THIS IS THE FUNCTIONS FOR THE CLONE POST TYPE DISPLAY

*/

function clone_finder(){
    if( have_rows('examples') ): // are there any examples?
    $clone_html = '';
    $title = '';
    $allowed_view = get_field('visible_to');// get the field of allowed viewers
    $current_user = get_current_user_id();
    $view_ok = in_array($current_user, $allowed_view);
    // loop through the rows of data
    while ( have_rows('examples') ) : the_row();
        $name = get_sub_field('name');
        $url = get_sub_field('url');
        $description = get_sub_field('description');
        $display = get_sub_field('display');
        if ($display == "True" || $view_ok || current_user_can('administrator')) {//set to show if set True in ACF, if user is in view list, or if user can admin

        	$title = "<h2>Example Sites</h2>";
            $clone_html = '<div class="clone-example"><a href="'.$url.'"><h3>' . $name . '</h3></a><div class="clone-description">' . $description . '</div></div>' . $clone_html;  
        }

    endwhile;
    return $title . $clone_html;

    else :

        // no rows found

    endif;
}

//GET SITE ID OF CLONE SITE
function build_site_clone_button($content){
    global $post;
    if ($post->post_type === 'clone'){
       $button = clone_button_maker(); 
       $clone_examples = clone_finder(); 
        return $content . $button . $clone_examples;
    }
    else {
        return $content;
    }
}

add_filter( 'the_content', 'build_site_clone_button' );


//builds clone button link
function clone_button_maker(){
    global $post;
    $form_id = RGFormsModel::get_form_id('duplicator');
    $url = acf_fetch_site_url($post->ID);
    $main = parse_url($url);//probably need to add a check for trailing slash
    $arg = array(
        'domain' => $main['host'],
        'path' => $main['path']
    );
    $blog_details = get_blog_details($arg);

    $site_id = $blog_details->blog_id;   

    $clone_page = get_field('cloner_page', 'option');
    $clone_page_slug = $clone_page->post_name;
    //var_dump($clone_page_slug);
    return '<a class="dup-button" href="' . get_site_url() . '/' . $clone_page_slug . '?cloner=' . $site_id . '#field_'. $form_id .'_2">Clone it to own it!</a>';
}

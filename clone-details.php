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
    if (current_user_can('administrator') || in_array($current_user, $allowed_view)){
        $view_ok = TRUE;//is user id in array of allowed viewers?
    }
    // loop through the rows of data
    while ( have_rows('examples') ) : the_row();
        $name = get_sub_field('name');
        $url = get_sub_field('url');
        $description = get_sub_field('description');
        $display = get_sub_field('display');
        if ($display == "True" || $view_ok ) {//set to show if set True in ACF, if user is in view list, or if user can admin

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
function build_site_clone_area($content){
    global $post;
    if ($post->post_type === 'clone'){
       $button = clone_button_maker(); 
       $clone_examples = clone_finder(); 
       $modal = make_modal(); 
        return $content . $button . $clone_examples . $modal;
    }
    else {
        return $content;
    }
}

add_filter( 'the_content', 'build_site_clone_area' );


//builds clone button link
function clone_button_maker(){
    global $post;
    $form_id = get_field('gravity_form_id', 'option');//get the form ID 
   
    set_the_clone_id();
    $clone_page = get_field('cloner_page', 'option');
    $form_id = get_field( 'cloner_form','options');
    //$clone_page_slug = $clone_page->post_name;
    //var_dump($clone_page_slug);
    $form_html = '[gravityform id="'. $form_id . '" title="false" description="false"]';
    return '<button class="dup-button">Clone it to own it!</button>' . $form_html;
}

//auto fill the gravity form field with the dynamic field population set to 'site_id' ***deprecated
add_filter( 'gform_field_value_site_id', 'set_the_clone_id' );
function set_the_clone_id() {
    global $post;
     $url = acf_fetch_site_url($post->ID);
    $main = parse_url($url);//probably need to add a check for trailing slash
    $arg = array(
        'domain' => $main['host'],
        'path' => $main['path']
    );
    $blog_details = get_blog_details($arg);
    $site_id = $blog_details->blog_id;   
    return $site_id;
}

//MODAL MAKER
function make_modal(){
   return '<div id="cloneModal" class="modal">

      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-header">
          <h2>Cloning in process . . . </h2>
        </div>
        <div class="modal-body">
            <div class="half">
              <p>This make take some time.</p> 
              <p>Please be patient & leave this window be.<br>
                You will be taken to your new dashboard when the process is complete.<br>
                I think you\'ll agree that cloning is pretty neat.</p>
            </div>
            <div class="half chicken">
              <svg version="1.1" id="L3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                <circle fill="none" stroke="#424242" stroke-width="4" cx="50" cy="50" r="44" style="opacity:0.5;"></circle>
                  <circle fill="#424242" stroke="#424242" stroke-width="3" cx="8" cy="54" r="6">
                    <animateTransform attributeName="transform" dur="3.5s" type="rotate" from="0 50 48" to="360 50 52" repeatCount="indefinite"></animateTransform>
                    
                  </circle>
                </svg>
            </div>
        </div>      
      </div>
</div>';
}
<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*

THESE ARE THE ACF SPECIFIC FUNCTIONS

*/


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






//*********************ACF SPECIFIC****************//



//ACF JSON SAVER
//This saves the ACF data to a folder so it can synch if you move it to a new site
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
 
function my_acf_json_save_point( $path ) {
    
    // update path
    $path = plugin_dir_path( __FILE__ )  . '/acf-json';
    // return
    return $path;
    
}


add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point( $paths ) {
    
    // remove original path (optional)
    unset($paths[0]);
    
    
    // append path
    $paths[] = plugin_dir_path( __FILE__ )  . '/acf-json';
    
    
    // return
    return $paths;
    
}


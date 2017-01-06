<?php
/*
Plugin Name: contributer-plug
Plugin URI: http://wordpress.org/extend/plugins/contributer-plug/
Description: Allows multiple authors to be assigned to a post. This plugin is by Nikhil Parihar.
Version: 1.0.0
Author: Nikhil Parihar
*/




add_action( 'add_meta_boxes', 'contributer_checkboxes' );
function contributer_checkboxes() {
    add_meta_box(
        'contributer_meta_box_id',          // this is HTML id of the box on edit screen
        'Contributors',    // title of the box
        'contributer_box_content',   // function to be called to display the checkboxes, see the function below
        'post',        // on which edit screen the box should appear
        'normal',      // part of page where the box should appear
        'default'      // priority of the box
    );
}
 
// display the metabox
function  contributer_box_content( $post_id ) {
    // nonce field for security check, you can have the same
    // nonce field for all your meta boxes of same plugin
	wp_nonce_field( plugin_basename( __FILE__ ), 'contributer-plug_nonce' );
	$meta = get_post_meta( get_the_ID() );
	
	
	$metas = get_post( get_the_ID() );

    $main_author = $metas->post_author;
    $users = get_users();
	 //echo '<pre>';print_r($users);
	 	
	foreach ($users as $user) { 
		$user_meta=get_userdata($user->ID);
		$user_roles=$user_meta->roles; 
			if (!in_array("subscriber", $user_roles)){  
		     $cont_id=explode(",",$meta['contributer'][0]);
		//echo '<pre>';print_r($cont_id);die;
		      $sel='';$dis="";
	   foreach($cont_id as $cid ){
		  if($main_author==$user->ID && $cid==$main_author ){
		      $sel="checked"; $dis="disabled"; }
		 if($cid==$user->ID ){
			  $sel="checked"; }
			}
		 echo '<div class="checkbox"><input type="checkbox" name="ids[]"  class="" value="'.$user->ID.'" '.$sel.' '.$dis.' />'.$user->display_name .' ('.$user->user_nicename.')<br />
	<input type="hidden" name="ids_on_page[]" value="'.$user->ID.'"/></div>';
       	}
     }
 }




// save data from checkboxes
add_action( 'save_post', 'contributer_field_data' );
function contributer_field_data() {
global $wpdb;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // security check
    if ( !wp_verify_nonce( $_POST['contributer-plug_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // further checks if you like, 
    // for example particular user, role or maybe post type in case of custom post types

//echo '<pre>';print_r($_POST);die;
	if ( isset( $_POST['ids'] ) ){
	$check_id = implode(", ", $_POST['ids']);	
	
	//Insert or Update in post_meta table
	if ( ! add_post_meta( $_POST['ID'],'contributer',$check_id, true ) ) { 
	   update_post_meta ($_POST['ID'],'contributer',$check_id );
	}
	   } else {
		if ( ! add_post_meta( $_POST['ID'],'contributer',0, true ) ) { 
	   update_post_meta ($_POST['ID'],'contributer',0 );
		   }
	}
	
}

//the_content filter use for show data below the post
function my_the_content_filter($content) {
 
  //Get from post_meta table
  $meta = get_post_meta( get_the_ID() );
  if(!empty($meta['contributer'][0])){
  $uid=explode(",",$meta['contributer'][0]);
  
  $content.='<div><h3>Contributers</h3></div>';
  $content.='<ul style="list-style:none;">';
  
  foreach($uid as $auth){
	  $user_data=get_userdata( $auth );
	//get_avatar use for get user image from gravatar
  $getu=get_the_author_meta( $user_data->ID );
  $content.='<li><a href="'.get_author_posts_url( $user_data->ID ).'"><span>'.get_avatar($user_data->ID)."</span><p>".$user_data->user_nicename.'</p></a></li>';
   }
   $content.='</ul>';
  }
  return $content;
}

add_filter( 'the_content', 'my_the_content_filter', 20 );

function plugin_deactivation()
{ 
    delete_post_meta_by_key( 'contributer' ); 
    flush_rewrite_rules();
}
register_uninstall_hook( __FILE__, 'plugin_deactivation' );

?>
?>

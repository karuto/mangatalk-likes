<?php
/**
 * @package MangaTalk_Likes
 * @version 0.0.1
 */
/*
Plugin Name: MangaTalk Likes
Plugin URI:  http://vincentzh.com
Author: Vincent Zhang
Version: 0.0.1
Author URI: http://vincentzh.com
*/

add_action( 'wp_enqueue_scripts', 'mangatalk_likes_enqueue_scripts' );
function mangatalk_likes_enqueue_scripts() {
  if ( is_single() ) {
    wp_enqueue_script( 'mangatalk_likes', plugins_url( '/likes.js', __FILE__ ), array('jquery'), '1.0', true );
    
    // pass the `ajax-url` from PHP to JS.
    wp_localize_script( 'mangatalk_likes', 'mangatalk_likes_params', array(
      'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
  }
}

$VERY_LOW_PRIORITY = 99;
add_filter( 'the_content', 'post_love_display', $VERY_LOW_PRIORITY );
function post_love_display( $content ) {
	$likesDiv = '';

	if ( is_single() ) {
		$likesCount = get_post_meta( get_the_ID(), 'post_likes', true );
    if ( empty( $likesCount ) ) {
      // let's init and update the likesCount
      $viewsCount = get_post_meta( get_the_ID(), '_post_views', true );
      $initLikesCount = ceil($viewsCount / 900);
      update_post_meta( get_the_ID(), 'post_likes', $initLikesCount );
      $likesCount = $initLikesCount;
    }
    $submitLikeUrl = admin_url( 'admin-ajax.php?action=mangatalk_submit_like&post_id=' . get_the_ID() );

		$likesDiv = '<p><a class="submit-like" href="' . $submitLikeUrl . '" data-id="' . get_the_ID() . '">喜欢 <span id="likes-count">' . $likesCount . '</span></a></p>';
	}

	return $content . $likesDiv;
}


add_action( 'wp_ajax_nopriv_mangatalk_submit_like', 'mangatalk_submit_like' );
add_action( 'wp_ajax_mangatalk_submit_like', 'mangatalk_submit_like' );

function mangatalk_submit_like() {
	$love = get_post_meta( $_REQUEST['post_id'], 'post_likes', true );
	$love++;
	update_post_meta( $_REQUEST['post_id'], 'post_likes', $love );
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
		echo $love;
		die();
	}
	else {
		wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
		exit();
	}
}
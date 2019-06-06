<?php
/**
 * @package MangaTalk_Likes
 * @version 0.0.1
 */
/*
Plugin Name: MangaTalk Likes
Plugin URI: http://vincentzh.com
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
		$postId = get_the_ID();
		
		$icThumbsUp = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="icon/ic_favorite" fill="none" fill-rule="evenodd"><path d="M18.34,11 L12,17.34 L5.6,11 C4.83723797,10.2408106 4.53743949,9.13236201 4.81353576,8.09219602 C5.08963203,7.05203003 5.89967746,6.23817263 6.93853576,5.95719602 C7.97739407,5.67621941 9.08723797,5.97081062 9.85,6.73 L12,8.85 L14.12,6.73 C14.8737603,5.93361184 16.0019708,5.61191332 17.0623938,5.8910043 C18.1228168,6.17009529 18.9464895,7.00550672 19.2105473,8.06977276 C19.4746051,9.1340388 19.1369743,10.2575842 18.33,11 L18.34,11 Z M19.75,5.34 C17.7975007,3.38809031 14.6324993,3.38809031 12.68,5.34 L12,6 L11.29,5.29 C9.32810557,3.39513895 6.20957611,3.42223813 4.28090712,5.35090712 C2.35223813,7.27957611 2.32513895,10.3981056 4.22,12.36 L12,20.16 L19.78,12.38 C21.7236496,10.4192284 21.7102195,7.25420636 19.75,5.31 L19.75,5.34 Z" id="ic_favorite" fill="#FFF" fill-rule="nonzero"></path></g></svg>';

		$likesDiv = 
		"<div style='display: flex; justify-content: center; margin: 48px 0 -24px;'>
		<a class='submit-like button button--primary' href='{$submitLikeUrl}' data-id='{$postId}'>
			<span style='height: 16px; width: 16px; display: inline-block; vertical-align: middle;'>{$icThumbsUp}</span>
			<span>喜欢</span>
			<span id='likes-count'>{$likesCount}</span>
		</a>
		</div>";
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
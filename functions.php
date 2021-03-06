<?php

/*
Plugin Name: WPSE53245 - Set ...from Twitter category posts as status
Plugin URI: http://http://wordpress.stackexchange.com/questions/53235
Description: "...from Twitter" category posts in "Status" format
Version: 0.1
Author: Ashfame
Author URI: http://wordpress.stackexchange.com/users/1521/ashfame
*/

function atjine_set_twitter_post_format( $postID ) {
	if ( has_post_format( 'status', $postID ) || !has_term( 'from-twitter', 'category', $postID ) )
            return;
	set_post_format( $postID, 'status' );
}
add_action( 'save_post', 'atjine_set_twitter_post_format' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function graphy_atjine_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the site name.
	// $title .= get_bloginfo( 'name' );

	// get ride of dangling separator
	$title = str_replace(" $sep ", "", $title);

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( is_home() || is_front_page() ) {
		$title = get_bloginfo( 'name' );
		if ( $site_description )
			$title .= " $sep $site_description";
	}

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'graphy' ), max( $paged, $page ) );

	return $title;
}
/* now actually install the filter, which is a mess */
add_action('after_setup_theme','atjine_override_wp_title_filter');

function atjine_override_wp_title_filter() {
	remove_filter( 'wp_title', 'graphy_wp_title', 10 );
	add_filter( 'wp_title', 'graphy_atjine_wp_title', 5, 2 );
}



/* 
 * Make the fonts come from us, not Google
 */
function ghostery_atjine_replace_fonts() {

    wp_deregister_style( array('graphy-fonts') );
    wp_dequeue_style( array('graphy-fonts') );

    wp_register_style( 'graphy-atjine-lora', '/files/lora/stylesheet.css');
    wp_enqueue_style( 'graphy-atjine-lora' );

    wp_register_style( 'graphy-atjine-bitter', '/files/bitter/stylesheet.css');
    wp_enqueue_style( 'graphy-atjine-bitter' );

}
add_action( 'wp_print_styles', 'ghostery_atjine_replace_fonts', 99999 );

/*
 * Support the Twitter Card plugin from Niall: https://github.com/niallkennedy/twitter-cards
 */
if ( ! function_exists( 'add_twitter_card_properties' ) ) {
add_filter( 'twitter_cards_properties', 'add_twitter_card_properties' );
function add_twitter_card_properties( $twitter_card ) {
	if ( is_array( $twitter_card ) ) {
		$twitter_card['site'] = '@ghelleks';
		$twitter_card['site:id'] = '7373472';
		$twitter_card['creator'] = '@ghelleks';
		$twitter_card['creator:id'] = '7373472';
	}
	return $twitter_card;
} }

/*
 * We get a syndicated feed from Tumblr (using tumblr-rss)
 * This extracts the post format from the list of categories.
 */
if ( ! function_exists( 'add_post_format_feedwordpress_syndicated_post' ) ) {
add_filter( 'syndicated_post' , 'add_post_format_feedwordpress_syndicated_post' ); 
function add_post_format_feedwordpress_syndicated_post ( $data ) {
	$feed = $data['tax_input']['syndication_feed'];
	$tag_ids = $data['tax_input']['post_tag'];
   $format = '';

   // maybe someday we'd check if this feed is tumblr or not.

	// look through the tags for a "format-*" so we can alter the post format accordingly
   foreach ($tag_ids as $i => $tag_id) {
		if (empty($format)) {
	      $tag = get_term_by('id', $tag_id, 'post_tag');

			//print_r($tag);

			switch ($tag->slug) {
				# aside, chat, gallery, link, image, quote, status, video
				case 'format-regular': $format = ''; break;
				case 'format-link': $format = 'link'; break;
				case 'format-quote'; $format = 'quote'; break;
				case 'format-photo': $format = 'image'; break;
				case 'format-conversation': $format = 'chat'; break;
				case 'format-video': $format = 'video'; break;
				case 'format-audio': $format = 'audio'; break;
				case 'format-answer': $format = 'aside'; break;
			}

	      // if we found a format, remove the format-* tag from the list of tags, we're done with it.
			if (! empty($format)) {
				unset($data['tax_input']['post_tag'][$i]);
				break; // ok, we're all done here.
			}
		}
   }
   
   if (empty($format) ) {
      // the format is "no format"
	   unset($data['tax_input']['post_format']);
	}
	else {
      // announce our post format
	   $data['tax_input']['post_format'] = 'post-format-' . $format;
	}

	print_r($data);
 
	return $data;
} }
 
/*
FWP+: Strip excerpts from syndicated posts
*/
add_filter(
/*hook=*/ 'syndicated_item_excerpt',
/*function=*/ 'fwp_strip_excerpt',
/*order=*/ 10,
/*arguments=*/ 2
);

/**

 fwp_strip_excerpt: Strips the excerpt for syndicated posts.

 @param string $excerpt The current excerpt for the syndicated item.
 @param SyndicatedPost $post An object representing the syndicated post.
  The syndicated item data is contained in $post->item
  The syndication feed channel data is contained in $post->feed
  The subscription data is contained in $post->link
 @return string The new content to give the syndicated item.

**/



function fwp_strip_excerpt ($excerpt, $post) {
	// Strip it
	$excerpt = '';

	// Send it back
	return $excerpt;
} /* fwp_strip_excerpt() */

?>

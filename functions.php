<?php

/* 
 * Make the fonts come from us, not Google
 */
function ghostery_atjine_replace_fonts() {

    wp_deregister_style( array('graphy-fonts', 'open-sans') );
    wp_dequeue_style( array('graphy-fonts', 'open-sans') );

    /*
     * must replace with our own, because opensans has dependencies in the admin panel
     * http://wordpress.org/support/topic/turning-off-open-sans-for-the-38-dashboard
     */
    wp_register_style( 'open-sans', '/files/opensans_regular_roman/stylesheet.css');
    wp_enqueue_style( 'open-sans' );

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

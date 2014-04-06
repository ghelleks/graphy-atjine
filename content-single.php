<?php
/**
 * @package Graphy
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php graphy_header_meta(); ?>
<?php /* GH: got rid of thumbnail here */ ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
<?php  /* Here comes the twitter hack that is insanely specific to me, but mostly harmless */
	/* Gunnar: listen. Make this a content filter. */
	if ( get_post_format() === 'status' )   {
		$tweet_id = get_post_custom_values('tweet_id');
		if ($tweet_id) {
			$url = 'https://twitter.com/#!/twitter/status/' . $tweet_id[0];
		}
		else {
			$url = trim(strip_tags(get_the_content()));
		}
		echo wp_oembed_get($url);
?> 
<?php } else { ?>
		<?php the_content(); ?>
<?php } ?>
		<?php wp_link_pages( array(	'before' => '<div class="page-links">' . __( 'Pages:', 'graphy' ), 'after'  => '</div>', ) ); ?>
	</div><!-- .entry-content -->
	<?php graphy_footer_meta(); ?>
</article><!-- #post-## -->

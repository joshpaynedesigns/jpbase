<?php

add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );

add_action( 'genesis_before_content', 'cgd_add_archive_title' );
function cgd_add_archive_title() {
	$arch_title = get_the_archive_title();

	if ( ! empty( $arch_title ) ) { ?>
		<header class="archiveTypeTitle">
			<h2><?php echo $arch_title ?></h2>
		</header>
		<?php
	}
}

//* Position post info above post title
remove_action( 'genesis_entry_header', 'genesis_post_info', 12);
add_action( 'genesis_entry_header', 'genesis_post_info', 9 );

add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	$post_info = '[post_date]';
	return $post_info;
}

/* Display Featured Image on top of the post */
add_action( 'genesis_entry_header', 'cgd_featured_post_image', 8 );
function cgd_featured_post_image() { ?>

	<?php
	$permalink = get_permalink($post->ID);
    $thumbnail = get_the_post_thumbnail_url( $post->ID, 'medium' );
    $title = get_the_title( $post->ID );
	if ( empty( $thumbnail ) ) {
		$default_img = get_field( 'default_banner_image_blog', 'option' )['sizes']['medium'];
		$thumbnail = $default_img;
	}
	?>

    <?php if ( ! empty( $thumbnail ) ) : ?>
        <a href="<?php echo $permalink ?>">
            <div class="post-image" style="background-image: url(' <?php echo $thumbnail ?> ')"></div>
        </a>
    <?php endif; ?>

	<?php
}

// Shorten the length of the excerpts
function cgd_custom_excerpt_length( $length ) {
    return 15;
}
add_filter( 'excerpt_length', 'cgd_custom_excerpt_length', 999 );

// Add Read More Link to Excerpts
add_filter('excerpt_more', 'cgd_get_read_more_link');
// add_filter( 'the_content_more_link', 'cgd_get_read_more_link' );
function cgd_get_read_more_link() {
   return '...<br/><a class="uppercase" href="' . get_permalink() . '" >Read More</a>';
}

genesis();

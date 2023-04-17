<?php

add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');
remove_action('genesis_entry_footer', 'genesis_post_meta');
remove_action('genesis_entry_footer', 'genesis_post_meta');
remove_action('genesis_before_loop', 'genesis_do_date_archive_title');

add_filter('genesis_post_info', 'sp_post_info_filter');
function sp_post_info_filter($post_info)
{
    $post_info = '[post_date]';
    return $post_info;
}

add_action('genesis_loop', 'custom_archive_intro_text');
function custom_archive_intro_text()
{
    $arch_cont = ns_get_field('archive_intro_text_projects', 'option');
    ?>
    <?php if (! empty($arch_cont)) : ?>
        <section class="archIntroText lastMNone">
            <?php echo $arch_cont ?>
        </section>
    <?php endif; ?>
    <?php
}

// Remove the loop and replace it with our own.
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'projects_archive_custom_loop');
function projects_archive_custom_loop()
{

    // get the pest types
    global $post;
    $args = array(
        'post_type'  => 'projects',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order'   => 'ASC'
    );
    $projects = get_posts($args);

    if (! empty($projects)) { ?>
        <div class="projects-grid one2grid">
            <?php foreach ($projects as $post) {
                setup_postdata($post);
                $project_blurb = ns_get_field('project_blurb');
                $thumb = get_the_post_thumbnail(
                    $post->ID,
                    'medium',
                    ['class' => 'project-img']
                );
                ?>
                <div class="project">
                    <?php echo $thumb; ?>
                    <div class="project-info">
                        <h4 class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <p class="project-blurb"><?php echo $project_blurb; ?></p>
                        <span class="primary-button small-button">
                            <a href="<?php the_permalink(); ?>">View Project</a>
                        </span>
                    </div>
                </div>
                <?php
            } wp_reset_postdata(); ?>
        </div>
    <?php }
}

genesis();

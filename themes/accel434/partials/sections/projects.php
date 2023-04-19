<?php
$section_title = get_sub_field('section_title');
$projects_per_feed = get_sub_field('projects_per_feed');
$category_filter = get_sub_field('category_filter');

if (!empty($category_filter)) {
    $args = array(
        'post_type'  => 'projects',
        'numberposts' => $projects_per_feed,
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'tax_query' => array(
            array(
                'taxonomy' => 'projects-cat',
                'field' => 'term_id',
                'terms' => $category_filter,
            ),
        )
    );
    $arch_link = get_term_link($category_filter);
} else {
    $args = array(
    'post_type'  => 'projects',
    'numberposts' => $projects_per_feed,
    'order' => 'ASC',
    'orderby' => 'menu_order',
    );
    $arch_link = '/projects/';
}

$projects = get_posts($args);

$section_classes = ns_decide_section_classes();
?>

<section class="projects-feed-section <?php echo $section_classes; ?>">
    <div class="wrap">
        <?php if (! empty($section_title)) : ?>
            <h2 class="section-title"><?php echo $section_title ?></h2>
        <?php endif; ?>

        <div class="projects-feed-outer ns-slider-arrows-wrap">
            <div class="projects-feed">
                <?php foreach ($projects as $project) : ?>
                    <?php
                        $project_id = $project->ID;
                        $project_title = $project->post_title;
                        $project_sub_title = ns_get_field('project_sub_title', $project_id);
                        $project_blurb = ns_get_field('project_blurb', $project_id);
                        $project_url = get_the_permalink($project_id);
                        $thumb = get_the_post_thumbnail_url(
                            $project_id,
                            'medium',
                            ['class' => 'project-img']
                        );
                        $project_cats = get_the_terms($project_id, 'projects-cat');
                        // init counter
                        $i = 1;
                    ?>
                    <a href="<?php echo $project_url; ?>" class="project">
                        <span class="project-img" style="background-image: url(<?php echo $thumb ?>);"></span>
                        <span class="project-info">
                            <h6 class="project-title"><?php echo $project_title ?></h6>
                            <p class="project-cats">
                                <?php foreach ($project_cats as $project_cat) :
                                    echo $project_cat->name;
                                    echo ($i < count($project_cats))? " | " : "";
                                    $i++;
                                endforeach; ?>
                            </p>
                            <span class="project-link" href="<?php echo $project_url; ?>">View Project</span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php ns_slider_arrows(32, 32); ?>
        </div>

        <div class="projects-feed-bottom">
            <span class="medium-gray-button small-button">
                <a href="<?php echo $arch_link ?>">View All Projects</a>
            </span>
        </div>
    </div>
</section>

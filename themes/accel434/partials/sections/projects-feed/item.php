<section class="projects-feed-section page-flexible-section <?php echo $padding_classes; ?>">
	<div class="wrap">
		<?php if ( ! empty( $section_title ) ) : ?>
			<h2 class="section-title"><?php echo $section_title ?></h2>
		<?php endif; ?>

		<div class="projects-feed-outer">
			<div class="projects-feed">
				<?php foreach ( $projects as $project ) : ?>
					<?php
						$project_id = $project->ID;
						$project_title = $project->post_title;
						$project_sub_title = ns_get_field( 'project_sub_title', $project_id );
						$project_blurb = ns_get_field( 'project_blurb', $project_id );
						$project_url = get_the_permalink( $project_id );
						$thumb = get_the_post_thumbnail_url(
					        $project_id,
					        'medium',
					        ['class' => 'project-img']
					    );
					    $project_cats = get_the_terms( $project_id, 'projects-cat' );
						// init counter
						$i = 1;
					?>
					<a href="<?php echo $project_url; ?>" class="project">
						<span class="project-img" style="background-image: url(<?php echo $thumb ?>);"></span>
						<span class="project-info">
							<h6 class="project-title"><?php echo $project_title ?></h6>
							<p class="project-cats">
								<?php foreach ( $project_cats as $project_cat ):
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
			<div class="left-arrow">
		        <?php echo get_svg_icon( 'arrow-white', '', 22, 22 ); ?>
		    </div>
		    <div class="right-arrow">
		        <?php echo get_svg_icon( 'arrow-white', '', 22, 22 ); ?>
		    </div>
		</div>

		<div class="projects-feed-bottom">
			<span class="medium-gray-button small-button">
				<a href="<?php echo $arch_link ?>">View All Projects</a>
			</span>
		</div>
	</div>
</section>

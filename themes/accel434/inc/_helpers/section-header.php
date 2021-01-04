<?php 

function obj_section_header( $title = null ) {
    
    if (!empty($title)) : ?>
        <header class="section-header">
            <h2 class="section-title"><?php echo $title ?></h2>
        </header>
    <?php endif; 
}
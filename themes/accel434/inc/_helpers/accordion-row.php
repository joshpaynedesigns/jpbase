<?php 

function obj_accordion_row( $details = null ) {
    if (!empty($details)) { ?>
        <div class="accordion-row smallmb lotmb0 base-border">
            <div class="accordion-row-header basepa">
                <h6 class="ac-row-title mb0"><?php echo $details['ac_row_title'] ?></h6>
                <div class="ac-row-toggle"></div>
            </div>
            <div class="accordion-row-content basept basepb baseml basemr base-border-top lcmb0">
                <?php echo $details['ac_row_content']; ?>
            </div>
        </div> 
    <?php
    }
}
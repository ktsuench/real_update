<?php 
    echo heading($title, 2); 
//------------------------------------------------------------------------//
?>
<?php if(!empty($announcement)){?>
    <?php $i = 1; ?>
    <div id='display' class="flex-container">
        <div id="ann-col-1" class="flex-col scroll-content-parent">
            <div class="scroll-content">
                <?php $announcement = array_reverse($announcement); ?>
                <?php foreach($announcement as $a){ ?>
                    <div class="announcement">
                        <div class="flex-center">
                            <div class="content">
                                <h4><?php echo $a['title'] ?></h4>
                                <div><?php echo $a['content'] ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div id="ann-col-2" class="flex-col scroll-content-parent">
            <div class="scroll-content">
                <?php $announcement[] = array_shift($announcement);?>
                <?php foreach($announcement as $a){ ?>
                    <div class="announcement">
                        <div class="flex-center">
                            <div class="content">
                                <h4><?php echo $a['title'] ?></h4>
                                <div><?php echo $a['content'] ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div id="ann-col-3" class="flex-col">
            <h2>Ignore this column, it is in development</h2>
            <?php $i=0;?>
            <?php foreach($announcement as $a){ ?>
                <?php $i++;?>
                <div class="announcement <?php echo $i===2?"active":''; ?>">
                    <h4><?php echo $a['title'] ?></h4>
                    <div><?php echo $a['content'] ?></div>
                </div>
            <?php } ?>
        </div>
        <!--<br class="clearfix">-->
<?php }else{ ?>
    <h3>There are no announcements to be shown.</h3>
<?php } ?>
    </div>
<!--<?php
//------------------------------------------------------------------------//    
    //Announcement List
    $link_content = 'Back to Announcement List';
    echo anchor(base_url('announcement'), $link_content, 'title="'.$link_content.'"');
?>-->
<script>var update_display = '<?php echo base_url('announcement/display/update'); ?>';</script>
<script src="<?php echo base_url(''); ?>/assets/js/ann_disp.js"></script>
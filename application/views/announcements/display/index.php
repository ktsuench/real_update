<?php 
    echo heading($title, 2, 'id="school"'); 
//------------------------------------------------------------------------//
?>
<div id='display' class="flex flex-row flex-container center">
    <div id="ann-col-1" class="scroll-content-parent">
        <div class="scroll-content">
            <?php if(!empty($announcement)){?>
                <?php $announcement = array_reverse($announcement); ?>
                <?php foreach($announcement as $a){ ?>
                    <div class="flex-panel">
                        <div class="flex flex-col flex-center">
                            <div class="content">
                                <h4><?php echo $a['title'] ?></h4>
                                <div><?php echo $a['content'] ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{ ?>
                <div class="flex-panel">
                    <div class="flex flex-col flex-center">
                        <div class="content">
                            <?php
                                /*
                                 *  TODO: Add in setting to set the default 
                                 *        message when there is no content to show
                                 */
                            ?>
                            <h4>No Announcements on Display</h4>
                            <div>There's nothing to show</div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="ann-col-2" class="scroll-content-parent">
        <div class="scroll-content">
            <?php if(!empty($announcement)){?>
                <?php $announcement[] = array_shift($announcement);?>
                <?php foreach($announcement as $a){ ?>
                    <div class="flex-panel">
                        <div class="flex flex-col flex-center">
                            <div class="content">
                                <h4><?php echo $a['title'] ?></h4>
                                <div><?php echo $a['content'] ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{ ?>
                <div class="flex-panel">
                    <div class="flex flex-col flex-center">
                        <div class="content">
                            <?php
                                /*
                                 *  TODO: Add in setting to set the default 
                                 *        message when there is no content to show
                                 */
                            ?>
                            <h4>No Announcements on Display</h4>
                            <div>There's nothing to show</div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="ann-col-3" class="flex flex-col">
        <div id="datetime-weather" class="flex-panel side-content">
            <div class="flex flex-col flex-center">
                <div id='datetime' class='text-center'>
                    <div id='date'></div>
                    <div id='time'>
                        <span id='hour'></span>
                        <span>:</span>
                        <span id='minute'></span>
                        <span>:</span>
                        <span id='second'></span>
                        <span id='meridian'></span>
                    </div>
                </div>
                <div id='weather' class='text-center'>
                </div>
            </div>
        </div>
        <div id="img-vid-slideshow" class="flex-panel side-content">
            <div class="flex flex-col">
                <div class="">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!--<?php
//------------------------------------------------------------------------//    
    //Announcement List
    $link_content = 'Back to Announcement List';
    echo anchor(base_url('announcement'), $link_content, 'title="'.$link_content.'"');
?>-->
<link rel="stylesheet" type="text/css" href='<?php echo base_url(''); ?>assets/vendor/css/weather-icons.min.css'>
<script>var update_display = '<?php echo base_url('announcement/display/update'); ?>';</script>
<script>var update_weather = '<?php echo base_url('announcement/display/update/weather'); ?>';</script>
<script src="<?php echo base_url(''); ?>assets/js/ann_disp.js"></script>
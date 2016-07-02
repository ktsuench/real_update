<?php 
    echo heading($title, 2, 'id="school"'); 
//------------------------------------------------------------------------//
    function build_slider($content, $path){
        foreach($content as $a){
?>
            <div class="flex-panel">
                <div class="flex flex-col flex-center">
                    <div class="content">
                        <div class='content-title'><?php echo $a['title'] ?></div>
                        <div class='content-material'><?php echo $a['content'] ?></div>
                        <?php if(isset($a['image'])){ ?>
                            <img src='<?php echo $path.$a['image']; ?>'>
                        <?php } ?>
                    </div>
                </div>
            </div>
<?php
        }
    }

    function default_slide(){
?>
        <div class="flex-panel">
            <div class="flex flex-col flex-center">
                <div class="content">
                    <?php
                        /*
                         *  TODO: Add in setting to set the default 
                         *        message when there is no content to show
                         */
                    ?>
                    <div class='content-title'>No Announcements on Display</div>
                    <div class='content-material'>There's nothing to show</div>
                </div>
            </div>
        </div>
<?php
    }
//------------------------------------------------------------------------//
?>
<div id='display' class="flex flex-row flex-container center">
    <div id="ann-col-1" class="scroll-content-parent">
        <div class="scroll-content">
            <?php 
                if(!empty($announcement)){
                    $announcement[] = array_shift($announcement);
                    $announcement = array_reverse($announcement);
                    build_slider($announcement, $upload_path);
                }else default_slide();
            ?>
        </div>
    </div>
    <div id="ann-col-2" class="scroll-content-parent">
        <div class="scroll-content">
            <?php 
                if(!empty($announcement)){
                    $announcement[] = array_shift($announcement);
                    build_slider($announcement, $upload_path);
                }else default_slide();
            ?>
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
<link rel="stylesheet" type="text/css" href='<?php echo base_url(''); ?>assets/vendor/weather_icons/css/weather-icons.min.css'>
<script>
    var update_display = '<?php echo base_url('announcement/display/update'); ?>';
    var update_weather = '<?php echo base_url('announcement/display/update/weather'); ?>';
    var upload_path = '<?php echo $upload_path; ?>';
</script>
<script src="<?php echo base_url(''); ?>assets/js/ann_disp.js"></script>
<?php 
    echo heading($title, 2); 

    //Form Validation Errors Display Location
    echo validation_errors();

    //Beginning of Form
    echo form_open_multipart($page_action);
//------------------------------------------------------------------------//
        /*if(isset($this->session->ann_create)){
            echo '<pre>';
            print_r($this->session->ann_create);
            echo '</pre>';
        }*/
        echo '<pre>';
        if(isset($crap)) print_r($crap);
        echo '</pre>';
//------------------------------------------------------------------------//
        //Announcement title
        echo form_label('Title', 'title').LINEBREAK;
        
        //Title Settings
        {
            $data = array(  'type'          =>  'text',
                            'id'            =>  'title',
                            'name'          =>  'title',
                            'placeholder'   =>  'Ex. Exam Season Coming Up',
                            'maxlength'     =>  $title_max_length,
                            'autocomplete'  =>  'off');
                            
            if(empty($ann_title)) $ann_title = $this->input->post('title');
            if(empty($ann_title) && isset($ann_data) && property_exists($ann_data, 'title'))
                $ann_title = $ann_data->title;
            if(empty($ann_title)) $ann_title = $this->session->ann_create['title'];
            $data['value'] = $ann_title;
        }
        
        echo form_input($data).LINEBREAK;
        echo '<span id="title_ch_count"></span>'.LINEBREAK;
//------------------------------------------------------------------------//
        //Announcement content
        echo form_label('Content', 'content').LINEBREAK;
        
        //Content Settings
        {
            $data = array(  'id'            =>  'content',
                            'name'          =>  'content',
                            'placeholder'   =>  'Ex. Rest up because exams are coming soon!',
                            'rows'          =>  '5',
                            'cols'          =>  '40',
                            'maxlength'     =>  $content_max_length,
                            'autocomplete'  =>  'off');
                            
            if(empty($ann_content)) $ann_content = $this->input->post('content');
            if(empty($ann_content) && isset($ann_data) && property_exists($ann_data, 'content'))
                $ann_content = $ann_data->content;
            if(empty($ann_content)) $ann_content = $this->session->ann_create['content'];
        }
        
        echo form_textarea($data, !empty($ann_content) ? $ann_content : '').LINEBREAK;
        echo '<span id="content_ch_count"></span>'.LINEBREAK;
//------------------------------------------------------------------------//
        //Announcement Type
        echo form_label('Type', 'type').LINEBREAK;
        
        //Type Settings
        {
            if(empty($ann_type)) $ann_type = $this->input->post('type');
            if(empty($ann_type) && isset($ann_data) && property_exists($ann_data, 'type'))
                $ann_type = $ann_data->type;
            if(empty($ann_type)) $ann_type = $this->session->ann_create['type'];
        }
        
        echo form_dropdown('type', $type_options, !empty($ann_type) ? $ann_type : 'daily').LINEBREAK;
//------------------------------------------------------------------------//
        //Announcement Image
        echo form_label('Image', 'image').LINEBREAK;
        
        //Image Settings
        {
            $data = array(  'name'  =>  'image',
                            'accept' => $image_file_types);

            if(empty($ann_img) && isset($ann_data) && property_exists($ann_data, 'image')){
                if(isset($ann_data->image)) $ann_img = $upload_path.$ann_data->image;
            }
            if(empty($ann_img) && !empty($this->session->ann_create['image'])){
                $ann_img = $upload_path_temp.$this->session->ann_create['image'];
            }
            if(!empty($ann_img)){
                $img = '<img src="'.$ann_img.'" style="height:20vh;">';
            }
        }

        if(empty($ann_img)){
            echo 'None'.LINEBREAK;
        }else{
            echo $img.LINEBREAK;
            echo form_checkbox('remove_image', 1, FALSE).'Remove Image'.LINEBREAK;
        }

        echo form_upload($data).LINEBREAK;
//------------------------------------------------------------------------//
        $date_id = array(array('start-datetime', 'start-calendar'), array('end-datetime', 'end-calendar'));
        $date_label = array(array('Start Date', 'Start Time'), array('End Date', 'End Time'));
        $date_id_prefix = array('start'.$date_id_prefix, 'end'.$date_id_prefix);
        $datetime_ref_props = array('start_datetime', 'end_datetime');

        foreach ($date_fields as $index => $field) {
            //Retrieving existing data
            if(isset($ann_data)){
                $datetime_ref = $ann_data->$datetime_ref_props[$index];
            }
//------------------------------------------------------------------------//
?>
        <div id='<?php echo $date_id[$index][0]; ?>' class='date-selector'>
<?php
            //Announcement Date
            echo form_label($date_label[$index][0]).LINEBREAK;

            //Date Hidden
            $data = array(
                'type'  =>  'hidden',
                'name'  =>  $field[0],
                'id'    =>  $field[0],
            );

            $ann_date = '';
            if(empty($ann_date) && empty(form_error('date'))) $ann_date = $this->input->post($field[0]);
            if(empty($ann_date) && isset($datetime_ref)) $ann_date = $datetime_ref->format('j');
            if(empty($ann_date)) $ann_date = $now->format('j');
            $data['value'] = $ann_date;
            
            echo form_input($data);

//------------------------------------------------------------------------//
            
            //Month and Year Dropdowns
            {
                //Month Dropdown
                $extra = array('id' => $field[1]);

                $ann_month = '';
                if(empty($ann_month)) $ann_month = $this->input->post($field[1]);
                if(empty($ann_month) && isset($datetime_ref)) $ann_month = $datetime_ref->format('m');
                if(empty($ann_month)) $ann_month = $now->format('m');
                
                echo form_dropdown($field[1], $month_options, $ann_month, $extra);
                
                //Year
                $extra = array('id' => $field[2]);

                $ann_year = '';
                if(empty($ann_year)) $ann_year = $this->input->post($field[2]);
                if(empty($ann_year) && isset($datetime_ref)) $ann_year = $datetime_ref->format('Y');
                if(empty($ann_year) || !array_key_exists($ann_year, $year_options)) $ann_year = $now->format('Y');
                
                echo form_dropdown($field[2], $year_options, $ann_year, $extra);
            }
//------------------------------------------------------------------------//
            //Calendar
            echo '<div id="'.$date_id[$index][1].'">'.$this->calendar->generate($ann_year, $ann_month).'</div>';
//------------------------------------------------------------------------//
            //Announcement Time
            echo form_label($date_label[$index][1]).LINEBREAK;
            
            //Hour, Minute, and Meridian Dropdowns
            {
                //Hour
                $extra = array('id' => $field[3], 'class' =>  'ann_time');

                $ann_hour = '';
                if(empty($ann_hour)) $ann_hour = $this->input->post($field[3]);
                if(empty($ann_hour) && isset($datetime_ref)) $ann_hour = $datetime_ref->format('h');
                if(empty($ann_hour)) $ann_hour = '12';
                echo form_dropdown($field[3], $hour_options, $ann_hour, $extra);
                
                //Minute
                $extra = array('id' => $field[4], 'class' =>  'ann_time');

                $ann_minute = '';
                if(empty($ann_minute)) $ann_minute = $this->input->post($field[4]);
                if(empty($ann_minute) && isset($datetime_ref)) $ann_minute = $datetime_ref->format('i');
                if(empty($ann_minute)) $ann_minute = '00';
                echo form_dropdown($field[4], $minute_options, $ann_minute, $extra);
                
                //Meridian
                $extra = array('id' => $field[5], 'class' =>  'ann_time');

                $ann_meridian = '';
                if(empty($ann_meridian)) $ann_meridian = $this->input->post($field[5]);
                if(empty($ann_meridian) && isset($datetime_ref)) $ann_meridian = $datetime_ref->format('a');
                if(empty($ann_meridian)) $ann_meridian = 'am';
                echo form_dropdown($field[5], $meridian_options, $ann_meridian, $extra).LINEBREAK;
            }
?>
        </div>
<?php
        }
//------------------------------------------------------------------------//
//TODO - move this style somewhere else or deal with it later
?>
    <style>
        .selected{font-weight: bolder;}
        .date-selector{float:left; padding:5px;}
    </style>
    </div>
<?php
//------------------------------------------------------------------------//
        //Submit Button
        $btn_val = 'Submit Announcement';
        echo form_submit('', $btn_val, array('style' => 'display:block; clear:both;'));
//------------------------------------------------------------------------//
    //End of Form
    echo form_close();
//------------------------------------------------------------------------//
    $link_content = 'Back to Announcement List';
    echo anchor(base_url('announcement'), $link_content, 'title="'.$link_content.'"');
?>
<link rel='stylesheet' type='text/css' href='<?php echo base_url().'assets/vendor/text_counter/css/text-counter.css'; ?>'>
<script src='<?php echo base_url().'assets/vendor/text_counter/js/text-counter.js'; ?>'></script>
<script>
    [
        {
            'field':        document.getElementById('title'),
            'counter':      document.getElementById('title_ch_count'),
            'max_length':   <?php echo $title_max_length; ?>,
        },
        {
            'field':        document.getElementById('content'),
            'counter':      document.getElementById('content_ch_count'),
            'max_length':   <?php echo $content_max_length; ?>,
        }
    ].forEach(function(e, index){
        text_counter(e.field, e.counter, e.max_length);
    });
</script>
<script src='<?php echo base_url().'assets/js/calendar.js'; ?>'></script>
<script>
    var get_cal_url = '<?php echo base_url('announcement/get_calendar'); ?>';
    [
        <?php foreach ($date_fields as $index => $field) { ?>
        {
            'date':     '<?php echo $field[0]; ?>',
            'month':    '<?php echo $field[1]; ?>',
            'year':     '<?php echo $field[2]; ?>',
            'calendar': '<?php echo $date_id[$index][1]; ?>',
            'prefix':   '<?php echo $date_id_prefix[$index]; ?>'
        },
        <?php } ?>
    ].forEach(function(e, index){
        initiate_calendar(e.year, e.month, e.calendar, e.date, e.prefix);
    });
</script>
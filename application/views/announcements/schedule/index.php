<?php 
    echo heading($title, 2); 
    
    //Form Validation Errors Display Location
    echo validation_errors();

    //Beginning of Form
    echo form_open($page_action);
//------------------------------------------------------------------------//
        //Retrieving existing data
        if(isset($this->session->ann_data)){
            $ann_data = $this->session->ann_data;
            
            if(empty($this->session->ann_create['schedule']['start'])) $datetime_ref = $ann_data->start_datetime;
            else $datetime_ref = $ann_data->end_datetime;
        }else if(isset($prev_start_date)) $datetime_ref = $prev_start_date;
//------------------------------------------------------------------------//
        //Announcement Date
        echo form_label('Date').LINEBREAK;
        
        //Month and Year Dropdowns
        {
            //Month Dropdown
            $extra = array('id' => 'month');
            if(empty($ann_month)) $ann_month = $this->input->post('month');
            if(empty($ann_month) && isset($datetime_ref)) $ann_month = $datetime_ref->format('m');
            if(empty($ann_month)) $ann_month = $now->format('m');
            
            echo form_dropdown('month', $month_options, $ann_month, $extra);
            
            //Year
            $extra = array('id' => 'year');
            if(empty($ann_year)) $ann_year = $this->input->post('year');
            if(empty($ann_year) && isset($datetime_ref)) $ann_year = $datetime_ref->format('Y');
            if(empty($ann_year) || !array_key_exists($ann_year, $year_options)) $ann_year = $now->format('Y');
            
            echo form_dropdown('year', $year_options, $ann_year, $extra);
        }
//------------------------------------------------------------------------//
        //Calendar
        echo '<div id="calendar">'.$this->calendar->generate($ann_year, $ann_month).'</div>';
//------------------------------------------------------------------------//
        //Announcement Time
        echo form_label('Time').LINEBREAK;
        
        //Hour, Minute, and Meridian Dropdowns
        {
            //Hour
            $extra = array('id' => 'hour', 'class' =>  'ann_time');
            if(empty($ann_hour)) $ann_hour = $this->input->post('hour');
            if(empty($ann_hour) && isset($datetime_ref)) $ann_hour = $datetime_ref->format('h');
            if(empty($ann_hour)) $ann_hour = $hour_options['12'];
            echo form_dropdown('hour', $hour_options, $ann_hour, $extra);
            
            //Minute
            $extra = array('id' => 'minute', 'class' =>  'ann_time');
            if(empty($ann_minute)) $ann_minute = $this->input->post('minute');
            if(empty($ann_minute) && isset($datetime_ref)) $ann_minute = $datetime_ref->format('i');
            if(empty($ann_minute)) $ann_minute = $minute_options['00'];
            echo form_dropdown('minute', $minute_options, $ann_minute, $extra);
            
            //Meridian
            $extra = array('id' => 'meridian', 'class' =>  'ann_time');
            if(empty($ann_meridian)) $ann_meridian = $this->input->post('meridian');
            if(empty($ann_meridian) && isset($datetime_ref)) $ann_meridian = $datetime_ref->format('a');
            if(empty($ann_meridian)) $ann_meridian = $meridian_options['am'];
            echo form_dropdown('meridian', $meridian_options, $ann_meridian, $extra).LINEBREAK;
        }
//------------------------------------------------------------------------//
        //Date Hidden
        if(empty($ann_date)) $ann_date = $this->input->post('date');
        $data = array(
            'type'  =>  'hidden',
            'name'  =>  'date',
            'id'    =>  'date',
        );
        
        if(empty($ann_date)) $ann_date = $this->input->post('date');
        if(empty($ann_date) && isset($datetime_ref)) $ann_date = $datetime_ref->format('j');
        if(empty($ann_date)) $ann_date = $now->format('j');
        $data['value'] = $ann_date;
        
        echo form_input($data);
//------------------------------------------------------------------------//
//TODO - move this style somewhere else or deal with it later
?>
<style>
    .selected{font-weight: bolder;}
</style>
<?php
//------------------------------------------------------------------------//
        //Submit Button
        if(empty($this->session->ann_create['schedule']['start']))
            echo form_submit('', 'Schedule End Date');
        else
            echo form_submit('', 'Submit Announcement');
//------------------------------------------------------------------------//
    //End of Form
    echo form_close();
//------------------------------------------------------------------------//
    //Announcement Schedule Start Date
    if(!empty($this->session->ann_create['schedule']['start'])){
        $link_content = 'Edit Announcement Scheduled Start Date';
        echo anchor(base_url('announcement/schedule'), $link_content, 'title="'.$link_content.'"').LINEBREAK;
    }
    
    //Announcement Update
    $url = 'announcement/'.$this->session->ann_create['op']['name'];
    if(isset($this->session->ann_data)) $url .= '/'.$this->session->ann_data->slug;
    $link_content = 'Edit Announcement Content';
    echo anchor(base_url($url), $link_content, 'title="'.$link_content.'"').LINEBREAK;
    
    //Announcement List
    $link_content = 'Back to Announcement List';
    echo anchor(base_url('announcement'), $link_content, 'title="'.$link_content.'"');
?>
<script>var get_cal_url = '<?php echo base_url('announcement/get_calendar'); ?>';</script>
<script src='<?php echo base_url(''); ?>/assets/js/ann_schedule.js'></script>
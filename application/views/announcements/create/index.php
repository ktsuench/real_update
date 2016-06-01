<?php 
    echo heading($title, 2); 

    //Form Validation Errors Display Location
    echo validation_errors();

    //Beginning of Form
    echo form_open($page_action); 
//------------------------------------------------------------------------//
        /*if(isset($this->session->ann_create)){
            echo '<pre>';
            print_r($this->session->ann_create);
            echo '</pre>';
        }*/
//------------------------------------------------------------------------//
        //Article title
        echo form_label('Title', 'title').LINEBREAK;
        
        //Title Settings
        {
            $data = array(  'type'          =>  'text',
                            'name'          =>  'title',
                            'placeholder'   =>  'Ex. Exam Season Coming Up',
                            'autocomplete'  =>  'off');
                            
            if(empty($ann_title)) $ann_title = $this->input->post('title');
            if(empty($ann_title) && isset($ann_data) && property_exists($ann_data, 'title'))
                $ann_title = $ann_data->title;
            if(empty($ann_title)) $ann_title = $this->session->ann_create['title'];
            $data['value'] = $ann_title;
        }
        
        echo form_input($data).LINEBREAK;
//------------------------------------------------------------------------//
        //Article content
        echo form_label('Content', 'content').LINEBREAK;
        
        //Content Settings
        {
            $data = array(  'name'          =>  'content',
                            'placeholder'   =>  'Ex. Rest up because exams are coming soon!',
                            'rows'          =>  '5',
                            'cols'          =>  '40',
                            'autocomplete'  =>  'off');
                            
            if(empty($ann_content)) $ann_content = $this->input->post('content');
            if(empty($ann_content) && isset($ann_data) && property_exists($ann_data, 'content'))
                $ann_content = $ann_data->content;
            if(empty($ann_content)) $ann_content = $this->session->ann_create['content'];
        }
        
        echo form_textarea($data, !empty($ann_content) ? $ann_content : '').LINEBREAK;
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
        
        echo form_dropdown('type', $type_options, !empty($ann_type) ? $ann_type : $type_options['daily']).LINEBREAK;
//------------------------------------------------------------------------//
        //Submit Button
        if(!isset($this->session->ann_create) || empty($this->session->ann_create['schedule']['start']))
            $btn_val = 'Schedule Start Date';
        else if(!empty($this->session->ann_create['schedule']['start']))
            $btn_val = 'Schedule End Date';
        echo form_submit('', $btn_val);
//------------------------------------------------------------------------//
    //End of Form
    echo form_close();
//------------------------------------------------------------------------//
    $link_content = 'Back to Announcement List';
    echo anchor(base_url('announcement'), $link_content, 'title="'.$link_content.'"');
?>
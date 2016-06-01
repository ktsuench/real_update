<?php
    echo heading($page_title, 2); 
    
    //Form Validation Errors Display Location
    echo validation_errors();

    //Beginning of Form
    echo form_open($page_action);
        //Email
        echo form_label('Email', 'email').LINEBREAK;

        $data = array(  'type'          =>  'text',
                        'name'          =>  'email',
                        'maxlength'     =>  '50',
                        'autocomplete'  =>  'off');
                        
        if(!empty($this->input->post('email'))) $data['value'] = $this->input->post('email');

        echo form_input($data).LINEBREAK;
        
        //Password
        echo form_label('Password', 'password').LINEBREAK;

        $data = array(  'name'          =>  'password',
                        'maxlength'     =>  '30',
                        'autocomplete'  =>  'off');

        echo form_password($data).LINEBREAK;

//Submit Button
        echo form_submit('', 'Login');

    //End of Form
    echo form_close();
    
    $link_content = 'Home';
    echo anchor(base_url(''), $link_content, 'title="'.$link_content.'"');
?>
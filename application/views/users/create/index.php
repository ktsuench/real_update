<?php
    $update = (stripos($page_title, 'update') !== false) ? true : false;
    
    echo heading($page_title, 2); 
    
    //Form Validation Errors Display Location
    echo validation_errors();

    //Beginning of Form
    echo form_open($page_action);
//------------------------------------------------------------------------//
        //First Name
        echo form_label('First Name', 'first_name').LINEBREAK;
        
        //First Name Settings
        {
            $data = array(  'type'          =>  'text',
                            'name'          =>  'first_name',
                            'maxlength'     =>  '50',
                            'autocomplete'  =>  'off');
            if(empty($user_first_name)) $user_first_name = $this->input->post('first_name');
            if(empty($user_first_name) && isset($user_data) && property_exists($user_data, 'first_name'))
                $user_first_name = $user_data->first_name;
            $data['value'] = $user_first_name;
        }
        
        echo form_input($data).LINEBREAK;
//------------------------------------------------------------------------//
        //Last Name
        echo form_label('Last Name', 'last_name').LINEBREAK;
        
        //Last Name Settings
        {
            $data = array(  'type'          =>  'text',
                            'name'          =>  'last_name',
                            'maxlength'     =>  '50',
                            'autocomplete'  =>  'off');
            if(empty($user_last_name)) $user_last_name = $this->input->post('last_name');
            if(empty($user_last_name) && isset($user_data) && property_exists($user_data, 'last_name'))
                $user_last_name = $user_data->last_name;
            $data['value'] = $user_last_name;
        }
        
        echo form_input($data).LINEBREAK;
//------------------------------------------------------------------------//
        //Email
        echo form_label('Email', 'email').LINEBREAK;
        
        //Email Settings
        {
            $data = array(  'type'          =>  'text',
                            'name'          =>  'email',
                            'maxlength'     =>  '50',
                            'autocomplete'  =>  'off');
            if(empty($user_email)) $user_email = $this->input->post('email');
            if(empty($user_email) && isset($user_data) && property_exists($user_data, 'email'))
                $user_email = $user_data->email;
            $data['value'] = $user_email;
        }

        echo form_input($data).LINEBREAK;
//------------------------------------------------------------------------//
        //Current Password (shows up on update)
        if($update){
            echo form_label('Current Password', 'password_current').LINEBREAK;
            
            //Current Password Settings
            $data = array(  'name'          =>  'password_current',
                            'maxlength'     =>  '30',
                            'autocomplete'  =>  'off');

            echo form_password($data).LINEBREAK;
        }
//------------------------------------------------------------------------//
        //Password
        echo form_label('Password', 'password').LINEBREAK;

        //Password Settings
        $data = array(  'name'          =>  'password',
                        'maxlength'     =>  '30',
                        'autocomplete'  =>  'off');

        echo form_password($data).LINEBREAK;
//------------------------------------------------------------------------//
        //Confirm Password
        echo form_label('Confirm Password', 'password_confirm').LINEBREAK;

        //Confirm Password Settings
        $data = array(  'name'          =>  'password_confirm',
                        'maxlength'     =>  '30',
                        'autocomplete'  =>  'off');

        echo form_password($data).LINEBREAK;
//------------------------------------------------------------------------//
        //User Type
        echo form_label('User Type', 'type').LINEBREAK;
        
        //Type Settings
        {
            if(empty($user_type)) $user_type = $this->input->post('type');
            if(empty($user_type) && isset($user_data) && property_exists($user_data, 'type'))
                $user_type = $user_data->type;
            if(empty($user_type)) $user_type = $type_options['guest'];
        }
        
        echo form_dropdown('type', $type_options, $user_type).LINEBREAK;
//------------------------------------------------------------------------//
        //Submit Button
        echo form_submit('', ($update ? 'Update' : 'Add').' User');
//------------------------------------------------------------------------//
    //End of Form
    echo form_close();
//------------------------------------------------------------------------//
    $link_content = 'Back to User List';
    echo anchor(base_url('user'), $link_content, 'title="'.$link_content.'"');
?>
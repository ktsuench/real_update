<?php
    echo heading($page_title, 2); 
    
    //Form Validation Errors Display Location
    echo validation_errors();
    if(isset($error)) echo $error;

    //Beginning of Form
    echo form_open_multipart($page_action);
//------------------------------------------------------------------------//
        //First Name
        echo form_label('Upload CSV File (*.csv)', $field_name).LINEBREAK;
        
        //First Name Settings
        $data = array(
            'name'      =>  $field_name,
            'accept'    =>  '.csv'
        );
        
        echo form_upload($data).LINEBREAK;
//------------------------------------------------------------------------//
        //Submit Button
        echo form_submit('', 'Add Users');
//------------------------------------------------------------------------//
    //End of Form
    echo form_close();
//------------------------------------------------------------------------//
    $link_content = 'Back to User List';
    echo anchor(base_url('user'), $link_content, 'title="'.$link_content.'"');
?>
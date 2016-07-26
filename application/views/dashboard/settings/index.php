<?php
    echo heading($title, 2); 
    
    //Form Validation Errors Display Location
    echo validation_errors();

    //Beginning of Form
    echo form_open_multipart($page_action);
//------------------------------------------------------------------------//
        /*echo '<pre>';
        print_r($settings);
        echo '</pre>';*/
//------------------------------------------------------------------------//
        //Institution
        echo form_label('Institution', 'institution').LINEBREAK;
        
        //Institution Settings
        {
            $data = array(  'type'          =>  'text',
                            'id'            =>  'institution',
                            'name'          =>  'institution',
                            'placeholder'   =>  'Ex. Toronto District School Board',
                            'maxlength'     =>  $institution_max_length,
                            'autocomplete'  =>  'off');
                            
            if(empty($institution)) $institution = $this->input->post('institution');
            if(empty($institution) && isset($settings) && property_exists($settings, 'institution'))
                $institution = $settings->institution;
            $data['value'] = $institution;
        }
        
        echo form_input($data).LINEBREAK;
        echo '<span id="institution_ch_count"></span>'.LINEBREAK;
//------------------------------------------------------------------------//
        //Message
        echo form_fieldset('Message');

        //Title
        {
            echo form_label('Title', 'mtitle').LINEBREAK;
            
            //Title Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'mtitle',
                                'name'          =>  'mtitle',
                                'placeholder'   =>  'Ex. Studia Adolescentiam Alunt',
                                'maxlength'     =>  $title_max_length,
                                'autocomplete'  =>  'off');
                if(empty($mtitle)) $mtitle = $this->input->post('mtitle');
                if(empty($mtitle) && isset($settings) && property_exists($settings, 'message')){
                    if(isset($settings->message) && property_exists($settings->message, 'title')){
                        if(isset($settings->message->title)) $mtitle = $settings->message->title;
                    }
                }
                $data['value'] = $mtitle;
            }
            
            echo form_input($data).LINEBREAK;
            echo '<span id="mtitle_ch_count"></span>'.LINEBREAK;
        }

        //Content
        {
            echo form_label('Content', 'mcontent').LINEBREAK;
            
            //Content Settings
            {
                $data = array(  'id'            =>  'mcontent',
                                'name'          =>  'mcontent',
                                'placeholder'   =>  'Ex. Education Nourishes Youth',
                                'rows'          =>  '5',
                                'cols'          =>  '40',
                                'maxlength'     =>  $content_max_length,
                                'autocomplete'  =>  'off');
                                
                if(empty($mcontent)) $mcontent = $this->input->post('mcontent');
                if(empty($mcontent) && isset($settings) && property_exists($settings, 'message')){
                    if(isset($settings->message) && property_exists($settings->message, 'content')){
                        if(isset($settings->message->content)) $mcontent = $settings->message->content;
                    }
                }
            }
            
            echo form_textarea($data, !empty($mcontent) ? $mcontent : '').LINEBREAK;
            echo '<span id="mcontent_ch_count"></span>'.LINEBREAK;
        }

        //Image
        {
            echo form_label('Image', 'mimage').LINEBREAK;
            
            //Image Settings
            {
                $data = array(  'name'  =>  'mimage',
                                'accept' => $image_file_types);

                if(empty($mimage) && isset($settings) && property_exists($settings, 'message')){
                    if(isset($settings->message) && property_exists($settings->message, 'image')){
                        if(!empty($settings->message->image)) $mimage = $upload_path.$settings->message->image;
                    }
                }
                
                if(!empty($mimage)){
                    $img = '<img src="'.$mimage.'" style="height:20vh;">';
                }
            }

            if(empty($mimage)){
                echo 'None'.LINEBREAK;
            }else{
                echo $img.LINEBREAK;
                echo form_checkbox('m_remove_image', 1, FALSE).'Remove Image'.LINEBREAK;
            }

            echo form_upload($data).LINEBREAK;
        }

        echo form_fieldset_close();
//------------------------------------------------------------------------//
        //Emergency Announcement
        echo form_fieldset('Emergency Announcement');

        //Title
        {
            echo form_label('Title', 'etitle').LINEBREAK;
            
            //Title Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'etitle',
                                'name'          =>  'etitle',
                                'placeholder'   =>  'Ex. Fire Alarm Procedure',
                                'maxlength'     =>  $title_max_length,
                                'autocomplete'  =>  'off');
                if(empty($etitle)) $etitle = $this->input->post('etitle');
                if(empty($etitle) && isset($settings) && property_exists($settings, 'emergency')){
                    if(isset($settings->emergency) && property_exists($settings->emergency, 'title')){
                        if(isset($settings->emergency->title)) $etitle = $settings->emergency->title;
                    }
                }
                $data['value'] = $etitle;
            }
            
            echo form_input($data).LINEBREAK;
            echo '<span id="etitle_ch_count"></span>'.LINEBREAK;
        }

        //Content
        {
            echo form_label('Content', 'econtent').LINEBREAK;
            
            //Content Settings
            {
                $data = array(  'id'            =>  'econtent',
                                'name'          =>  'econtent',
                                'placeholder'   =>  'Ex. Exit the building as quickly and orderly as possible.',
                                'rows'          =>  '5',
                                'cols'          =>  '40',
                                'maxlength'     =>  $content_max_length,
                                'autocomplete'  =>  'off');
                                
                if(empty($econtent)) $econtent = $this->input->post('econtent');
                if(empty($econtent) && isset($settings) && property_exists($settings, 'emergency')){
                    if(isset($settings->emergency) && property_exists($settings->emergency, 'content')){
                        if(isset($settings->emergency->content)) $econtent = $settings->emergency->content;
                    }
                }
            }
            
            echo form_textarea($data, !empty($econtent) ? $econtent : '').LINEBREAK;
            echo '<span id="econtent_ch_count"></span>'.LINEBREAK;
        }

        //Image
        {
            echo form_label('Image', 'eimage').LINEBREAK;
            
            //Image Settings
            {
                $data = array(  'name'  =>  'eimage',
                                'accept' => $image_file_types);

                if(empty($eimage) && isset($settings) && property_exists($settings, 'emergency')){
                    if(isset($settings->emergency) && property_exists($settings->emergency, 'image')){
                        if(!empty($settings->emergency->image)) $eimage = $upload_path.$settings->emergency->image;
                    }
                }
                
                if(!empty($eimage)){
                    $img = '<img src="'.$eimage.'" style="height:20vh;">';
                }
            }

            if(empty($eimage)){
                echo 'None'.LINEBREAK;
            }else{
                echo $img.LINEBREAK;
                echo form_checkbox('e_remove_image', 1, FALSE).'Remove Image'.LINEBREAK;
            }

            echo form_upload($data).LINEBREAK;
        }

        echo form_fieldset_close();
//------------------------------------------------------------------------//
        //Colour
        echo form_fieldset('Colour');

        //Foreground
        {
            echo form_label('Foreground', 'foreground').LINEBREAK;
            
            //Foreground Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'foreground',
                                'name'          =>  'foreground',
                                'placeholder'   =>  'Ex. #ffffff',
                                'autocomplete'  =>  'off');

                if(empty($foreground)) $foreground = $this->input->post('foreground');
                if(empty($foreground) && isset($settings) && property_exists($settings, 'colour')){
                    if(isset($settings->colour) && property_exists($settings->colour, 'foreground')){
                        if(isset($settings->colour->foreground)) $foreground = $settings->colour->foreground;
                    }
                }
                $data['value'] = $foreground;
            }
            
            echo form_input($data).LINEBREAK;
        }

        //Background
        {
            echo form_label('Background', 'background').LINEBREAK;
            
            //Background Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'background',
                                'name'          =>  'background',
                                'placeholder'   =>  'Ex. #000000',
                                'autocomplete'  =>  'off');

                if(empty($background)) $background = $this->input->post('background');
                if(empty($background) && isset($settings) && property_exists($settings, 'colour')){
                    if(isset($settings->colour) && property_exists($settings->colour, 'background')){
                        if(isset($settings->colour->background)) $background = $settings->colour->background;
                    }
                }
                $data['value'] = $background;
            }
            
            echo form_input($data).LINEBREAK;
        }

        echo form_fieldset_close();
//------------------------------------------------------------------------//
        //Location
        echo form_fieldset('Location');

        //City
        {
            echo form_label('City', 'city').LINEBREAK;
            
            //City Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'city',
                                'name'          =>  'city',
                                'placeholder'   =>  'Ex. Toronto',
                                'autocomplete'  =>  'off');

                if(empty($city)) $city = $this->input->post('city');
                if(empty($city) && isset($settings) && property_exists($settings, 'location')){
                    if(isset($settings->location) && property_exists($settings->location, 'city')){
                        if(isset($settings->location->city)) $city = $settings->location->city;
                    }
                }
                $data['value'] = $city;
            }
            
            echo form_input($data).LINEBREAK;
        }

        //Country
        {
            echo form_label('Country', 'country').LINEBREAK;
            
            //Country Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'country',
                                'name'          =>  'country',
                                'placeholder'   =>  'Ex. Canada',
                                'autocomplete'  =>  'off');

                if(empty($country)) $country = $this->input->post('country');
                if(empty($country) && isset($settings) && property_exists($settings, 'location')){
                    if(isset($settings->location) && property_exists($settings->location, 'country')){
                        if(isset($settings->location->country)) $country = $settings->location->country;
                    }
                }
                $data['value'] = $country;
            }
            
            echo form_input($data).LINEBREAK;
        }

        //Zip Code
        {
            echo form_label('Postal/Zip Code', 'loczip').LINEBREAK;
            
            //Zip Code Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'loczip',
                                'name'          =>  'loczip',
                                'placeholder'   =>  'Ex. M4M2A1',
                                'autocomplete'  =>  'off');

                if(empty($loczip)) $loczip = $this->input->post('loczip');
                if(empty($loczip) && isset($settings) && property_exists($settings, 'location')){
                    if(isset($settings->location) && property_exists($settings->location, 'zip')){
                        if(isset($settings->location->zip)) $loczip = $settings->location->zip;
                    }
                }
                $data['value'] = $loczip;
            }
            
            echo form_input($data).LINEBREAK;
        }

        echo form_fieldset_close();
//------------------------------------------------------------------------//
        //Weather
        echo form_fieldset('Weather');

        //Search Type
        {
            echo form_label('Search Type', 'stype').LINEBREAK;
            
            //Search Type Settings
            {
                $data =  array( 'name'    =>  'stype');
                if(empty($stype)) $stype = $this->input->post('stype');
                if(empty($stype) && isset($settings) && property_exists($settings, 'weather')){
                    if(isset($settings->weather) && property_exists($settings->weather, 'search_type')){
                        if(isset($settings->weather->search_type)) $stype = $settings->weather->search_type;
                    }
                }
                if(empty($stype)) $stype = BY_ZIP;
            }

            $i = 0;
            foreach($type_options['search'] as $key => $value){
                $data['id'] = $data['name'].'['.$i++.']';
                echo form_radio($data, $key, $key == $stype).'<span>'.$value.'</span>'.LINEBREAK;
            }
        }

        //Unit Type
        {
            echo form_label('Unit Type', 'utype').LINEBREAK;
            
            //Unit Type Settings
            {
                $data =  array( 'name'    =>  'utype');
                if(empty($utype)) $utype = $this->input->post('utype');
                if(empty($utype) && isset($settings) && property_exists($settings, 'weather')){
                    if(isset($settings->weather) && property_exists($settings->weather, 'units')){
                        if(isset($settings->weather->units)) $utype = $settings->weather->units;
                    }
                }
                if(empty($utype)) $utype = BY_METRIC;
            }
            
            $i = 0;
            foreach($type_options['units'] as $key => $value){
                $data['id'] = $data['name'].'['.$i++.']';
                echo form_radio($data, $key, $key == $utype).'<span>'.$value.'</span>'.LINEBREAK;
            }
        }

        echo form_fieldset_close();
//------------------------------------------------------------------------//
        //Content Scrolling
        echo form_fieldset('Content Scrolling');

        /*//Display Type
        {
            echo form_label('Display Type', 'dtype').LINEBREAK;
            
            //Display Type Settings
            {
                $data =  array( 'name'    =>  'dtype');
                if(empty($dtype)) $dtype = $this->input->post('dtype');
                if(empty($dtype) && isset($settings) && property_exists($settings, 'content_scrolling')){
                    if(isset($settings->content_scrolling) && property_exists($settings->content_scrolling, 'discontinuous')){
                        if(isset($settings->content_scrolling->discontinuous)) $dtype = $settings->content_scrolling->discontinuous;
                    }
                }
                if(empty($dtype)) $dtype = TRUE;
            }
            
            $i = 0;
            foreach($type_options['discontinuous'] as $key => $value){
                $data['id'] = $data['name'].'['.$i++.']';
                echo form_radio($data, $key, $key == $dtype).'<span>'.$value.'</span>'.LINEBREAK;
            }
        }*/

        //Speed
        {
            echo form_label('Speed (Seconds)', 'speed').LINEBREAK;
            
            //Speed Settings
            {
                $data = array(  'type'          =>  'text',
                                'id'            =>  'speed',
                                'name'          =>  'speed',
                                'placeholder'   =>  'Ex. 5',
                                'autocomplete'  =>  'off');

                if(empty($speed)) $speed = $this->input->post('speed');
                if(empty($speed) && isset($settings) && property_exists($settings, 'content_scrolling')){
                    if(isset($settings->content_scrolling) && property_exists($settings->content_scrolling, 'speed')){
                        if(isset($settings->content_scrolling->speed)) $speed = $settings->content_scrolling->speed;
                    }
                }
                if(empty($speed)) $speed = TRUE;
            }
            $data['value'] = $speed;

            echo form_input($data).LINEBREAK;
        }

        echo form_fieldset_close();
//------------------------------------------------------------------------//
        //Submit Button
        echo form_submit('', 'Update Settings');
//------------------------------------------------------------------------//
    //End of Form
    echo form_close();
//------------------------------------------------------------------------//
    $link_content = 'Back to Dashboard';
    echo anchor(base_url('dashboard'), $link_content, 'title="'.$link_content.'"');
?>
<link rel='stylesheet' type='text/css' href='<?php echo base_url().'assets/vendor/text_counter/css/text-counter.css'; ?>'>
<script src='<?php echo base_url().'assets/vendor/text_counter/js/text-counter.js'; ?>'></script>
<script>
    [
        {
            'field':        document.getElementById('institution'),
            'counter':      document.getElementById('institution_ch_count'),
            'max_length':   <?php echo $institution_max_length; ?>,
        },
        {
            'field':        document.getElementById('mtitle'),
            'counter':      document.getElementById('mtitle_ch_count'),
            'max_length':   <?php echo $title_max_length; ?>,
        },
        {
            'field':        document.getElementById('mcontent'),
            'counter':      document.getElementById('mcontent_ch_count'),
            'max_length':   <?php echo $content_max_length; ?>,
        },
        {
            'field':        document.getElementById('etitle'),
            'counter':      document.getElementById('etitle_ch_count'),
            'max_length':   <?php echo $title_max_length; ?>,
        },
        {
            'field':        document.getElementById('econtent'),
            'counter':      document.getElementById('econtent_ch_count'),
            'max_length':   <?php echo $content_max_length; ?>,
        }
    ].forEach(function(e, index){
        text_counter(e.field, e.counter, e.max_length);
    });
</script>
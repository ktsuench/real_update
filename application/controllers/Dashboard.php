<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Navigation.php';

class Dashboard extends Navigation{
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $data['title'] = 'Dashboard';
        //$data['stylesheet'][] = 'ann_list.css';

        if(isset($this->session->settings_update)){
            $succ = 'Successfully updated settings.';
            $fail = 'Failed to update settings';
            $data['settings_res'] = $this->session->settings_update !== FALSE ? $succ : $fail;
        }

        $this->load_view('dashboard', $data, TRUE);
    }

    public function settings(){
        if($this->session->user->type != ADMIN) redirect('dashboard');

        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Settings';
        $data['page_action'] = 'settings';
        //$data['stylesheet'][] = 'ann_list.css';

        $data['upload_path'] = base_url().UPLOAD_ANN;
        $data['upload_path_temp'] = base_url().UPLOAD_TMP;
        
        $data['institution_max_length'] = 75;
        $data['title_max_length'] = 50;
        $data['content_max_length'] = 150;

        //Array List of Types
        $data['type_options'] = array( 
            'search'    =>  array(
                BY_CITY =>  'City', 
                BY_ZIP  =>  'Zip'
            ),
            'units' =>  array(
                BY_METRIC   =>  'Metric', 
                BY_IMPERIAL =>  'Imperial'
            ),
            'discontinuous'     =>  array(
                TRUE    =>  'Discontinuous',
                FALSE   =>  'Continuous'
            )
        );
        
        //Retrieving settings
        foreach($this->config->item('ru_settings') as $key => $val){
            $settings[$key] = $val;
        }
        $data['settings'] = (object) $settings;

        //String of Allowed Image Types
        $data['image_file_types'] = 'image/*';

        //Form Rule Configuration
        {
            $message_ann_rules = array(
                array(
                    'field' =>  'mtitle',
                    'label' =>  'mTitle',
                    'rules' =>  array('max_length['.$data['title_max_length'].']')
                ),
                array(
                    'field' =>  'mcontent',
                    'label' =>  'mContent',
                    'rules' =>  array('max_length['.$data['content_max_length'].']')
                )
            );

            $emergency_ann_rules = array(
                array(
                    'field' =>  'etitle',
                    'label' =>  'Emergency Announcement Title',
                    'rules' =>  array('max_length['.$data['title_max_length'].']')
                ),
                array(
                    'field' =>  'econtent',
                    'label' =>  'Emergency Announcement Content',
                    'rules' =>  array('max_length['.$data['content_max_length'].']')
                )
            );

            $colour_rules = array(
                array(
                    'field' =>  'foreground',
                    'label' =>  'Foreground Colour',
                    //'rules' =>  array('callback_[some colour val fn]')
                ),
                array(
                    'field' =>  'background',
                    'label' =>  'Background Colour',
                    //'rules' =>  array('callback_[some colour val fn]')
                )
            );

            //TODO: Validate City, Country, and Postal/Zip Code
            $location_rules = array(
                array(
                    'field' =>  'city',
                    'label' =>  'City',
                    'rules' =>  array('required')
                ),
                array(
                    'field' =>  'country',
                    'label' =>  'Country',
                    'rules' =>  array('required')
                ),
                array(
                    'field' =>  'loczip',
                    'label' =>  'Postal/Zip Code',
                    'rules' =>  array('required')
                )
            );

            $weather_rules = array(
                array(
                    'field' =>  'stype',
                    'label' =>  'Search Type',
                    'rules' =>  array('required', 'in_list['.implode(',', array_keys($data['type_options']['search'])).']'),
                    'errors'=>  array('in_list' => 'Search Type mst be one of: '.implode(', ', $data['type_options']['search']))
                ),
                array(
                    'field' =>  'utype',
                    'label' =>  'Unit Type',
                    'rules' =>  array('required', 'in_list['.implode(',', array_keys($data['type_options']['units'])).']'),
                    'errors'=>  array('in_list' => 'Unit Type mst be one of: '.implode(', ', $data['type_options']['units']))
                )
            );

            $content_scrolling_rules = array(
                /*array(
                    'field' =>  'dtype',
                    'label' =>  'Display Type',
                    'rules' =>  array('required', 'in_list['.implode(',', array_keys($data['type_options']['discontinuous'])).']'),
                    'errors'=>  array('in_list' => 'Display Type mst be one of: '.implode(', ', $data['type_options']['discontinuous']))
                ),*/
                array(
                    'field' =>  'speed',
                    'label' =>  'Speed',
                    'rules' =>  array('required', 'numeric', 'greater_than[0]')
                )
            );

            //All Form Rules
            $form_rules_config = array(
                array(
                    'field' =>  'institution',
                    'label' =>  'Institution',
                    'rules' =>  array('required', 'max_length['.$data['institution_max_length'].']')
                )
            );

            $form_rules_config = array_merge(
                $form_rules_config,
                $message_ann_rules,
                $emergency_ann_rules,
                $colour_rules,
                $location_rules,
                $weather_rules,
                $content_scrolling_rules
            );
        }
        
        $this->form_validation->set_rules($form_rules_config);
        
        if($this->form_validation->run() == FALSE){
            $this->load_view('dashboard/settings', $data, TRUE);
        }else{
            $this->load->helper('path');

            //Process images
            $image_rules = array(
                array(
                    'field' =>  'mimage',
                    'label' =>  'Message Image',
                    'rules' =>  array('callback_process_image_upload[mimage,mimg,'.FALSE.','.UPLOAD_ANN.',mimg]')
                ),
                array(
                    'field' =>  'eimage',
                    'label' =>  'Emergency Image',
                    'rules' =>  array('callback_process_image_upload[eimage,eimg,'.FALSE.','.UPLOAD_ANN.',eimg]')
                )
            );
            $this->form_validation->set_data($_POST);
            $this->form_validation->set_rules($image_rules);
            if($this->form_validation->run() == FALSE) $this->load_view('dashboard/settings', $data, TRUE);

            //Retrieve image names
            $img = (object) array('e'=>'', 'm'=>'');

            if(isset($this->session->mimg)){
                $img->m = $this->session->mimg;
                unset($_SESSION['mimg']);
            }

            if(isset($this->session->eimg)){
                $img->e = $this->session->eimg;
                unset($_SESSION['eimg']);
            }

            //Build Settings
            $settings = (object) array(                
                'institution'   =>  $this->input->post('institution'),
                'message'       =>  (object) array(
                    'title'     =>  $this->input->post('mtitle'),
                    'content'   =>  $this->input->post('mcontent'),
                    'image'     =>  $img->m
                ),
                'emergency'     =>  (object) array(
                    'title'     =>  $this->input->post('etitle'),
                    'content'   =>  $this->input->post('econtent'),
                    'image'     =>  $img->e
                ),
                'colour'        =>  (object) array(
                    'foreground'    =>   $this->input->post('foreground'),
                    'background'    =>   $this->input->post('background')
                ),
                'location'      =>  (object) array( 
                    'city'      =>  $this->input->post('city'),
                    'country'   =>  $this->input->post('country'),
                    'zip'       =>  $this->input->post('loczip')
                ),
                'weather'       =>  (object) array(
                    'search_type'   =>  intval($this->input->post('stype')),
                    'units'         =>  $this->input->post('utype')
                ),
                'content_scrolling' =>  (object) array(
                    'discontinuous' =>  TRUE, //$this->input->post('dtype'),
                    'speed'         =>  floatval($this->input->post('speed'))
                )
            );

            //Check if existing image is to be removed
            $remove_image = array(
                'message'   =>  $this->input->post('m_remove_image'),
                'emergency' =>  $this->input->post('e_remove_image')
            );

            foreach ($remove_image as $field => $rem_img) {
                if($rem_img !== NULL && boolval($rem_img) == TRUE){
                    $path = './'.UPLOAD_ANN.$settings->$field->image;
                    if(file_exists($path)) unlink($path);

                    $settings->$field->image = '';
                }
            }

            $config_json = fopen(set_realpath(SETTINGS_PATH).'config.json', 'w');
            $res = fwrite($config_json, json_encode($settings, JSON_PRETTY_PRINT));         
            fclose($config_json);
            
            //Updating settings and reporting completion
            $this->session->settings_update = $res;
            $this->session->mark_as_flash('res');

            redirect('dashboard');
        }
    }
}
?>
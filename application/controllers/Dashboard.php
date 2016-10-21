<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Navigation.php';

class Dashboard extends Navigation{
    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Dashboard Home
     */
    public function index(){
        $data = self::$_data;
        $data['title'] = 'Dashboard';
        //$data['stylesheet'][] = 'ann_list.css';

        /**
         * For debugging purposes
         */
        if(ENVIRONMENT == ENV_DEVELOPMENT){
            $data['ru_settings'] = print_r($this->config->item('ru_settings'), TRUE);
        }

        /**
         * Notify user that they do not have access to the requested operation
         */
        if(isset($this->session->access_flag)){
            $data['access_flag'] = $this->session->access_flag;
        }

        // Notify user on result of modifying settings
        if(isset($this->session->settings_update)){
            $succ = 'Successfully updated settings.';
            $fail = 'Failed to update settings';
            $data['settings_res'] = $this->session->settings_update !== FALSE ? $succ : $fail;
        }

        // Load view
        $this->load_view('dashboard', $data, TRUE);
    }

    /**
     * Announcement Display Settings
     */
    public function settings(){
        // Redirect user if user is not an Admin
        if($this->session->user->type != ADMIN) redirect('dashboard');

        /**
         * Load helpers and libraries
         */
        //$this->load->helper('form');
        $this->load->library('form_validation');

        /**
         * View data
         */
        $data = self::$_data;
        $data['title'] = 'Settings';
        $data['page_title'] = 'Settings';
        $data['page_action'] = 'settings';
        //$data['stylesheet'][] = 'ann_list.css';

        /**
         * View constants
         */
        {
            /**
             * Upload paths
             */
            $data['upload_path'] = base_url().UPLOAD_ANN;
            $data['upload_path_temp'] = base_url().UPLOAD_TMP;
            
            /**
             * Array of max lengths of content
             */
            $data['max_length'] = array(
                'institution'   => 75,
                'title'         => 50,
                'content'       => 150
            );

            /**
             * Array List of Types
             */
            $data['type_options'] = array( 
                'search' => array(
                    BY_CITY =>  'City', 
                    BY_ZIP  =>  'Zip'
                ),
                'units' => array(
                    BY_METRIC   =>  'Metric', 
                    BY_IMPERIAL =>  'Imperial'
                ),
                'discontinuous' => array(
                    TRUE    =>  'Discontinuous',
                    FALSE   =>  'Continuous'
                )
            );

            /**
             * String of Allowed Image Types
             */
            $data['image_file_types'] = 'image/*';
        }

        /**
         * Form Rule Configuration
         */
        {
            $message_ann_rules = array(
                array(
                    'field' =>  'message[title]',
                    'label' =>  'Message Title',
                    'rules' =>  array('max_length['.$data['max_length']['title'].']')
                ),
                array(
                    'field' =>  'message[content]',
                    'label' =>  'Message Content',
                    'rules' =>  array('max_length['.$data['max_length']['content'].']')
                )
            );

            $emergency_ann_rules = array(
                array(
                    'field' =>  'emergency[title]',
                    'label' =>  'Emergency Announcement Title',
                    'rules' =>  array('max_length['.$data['max_length']['title'].']')
                ),
                array(
                    'field' =>  'emergency[content]',
                    'label' =>  'Emergency Announcement Content',
                    'rules' =>  array('max_length['.$data['max_length']['content'].']')
                )
            );

            /**
             * @todo Validate colour
             */
            $colour_rules = array(
                array(
                    'field' =>  'colour[foreground]',
                    'label' =>  'Foreground Colour',
                    //'rules' =>  array('callback_[some colour validation fn]')
                ),
                array(
                    'field' =>  'colour[background]',
                    'label' =>  'Background Colour',
                    //'rules' =>  array('callback_[some colour validation fn]')
                )
            );

            /**
             * @todo Validate City, Country, and Postal/Zip Code
             * @var array
             */
            $location_rules = array(
                array(
                    'field' =>  'location[city]',
                    'label' =>  'City',
                    'rules' =>  array('required')
                ),
                array(
                    'field' =>  'location[country]',
                    'label' =>  'Country',
                    'rules' =>  array('required')
                ),
                array(
                    'field' =>  'location[zip]',
                    'label' =>  'Postal/Zip Code',
                    'rules' =>  array('required')
                )
            );

            $weather_rules = array(
                array(
                    'field' =>  'weather[search]',
                    'label' =>  'Search Type',
                    'rules' =>  array('required', 'in_list['.implode(',', array_keys($data['type_options']['search'])).']'),
                    'errors'=>  array('in_list' => 'Search Type mst be one of: '.implode(', ', $data['type_options']['search']))
                ),
                array(
                    'field' =>  'weather[units]',
                    'label' =>  'Unit Type',
                    'rules' =>  array('required', 'in_list['.implode(',', array_keys($data['type_options']['units'])).']'),
                    'errors'=>  array('in_list' => 'Unit Type mst be one of: '.implode(', ', $data['type_options']['units']))
                )
            );

            $content_rules = array(
                /*array(
                    'field' =>  'content[discontinuous]',
                    'label' =>  'Display Type',
                    'rules' =>  array('required', 'in_list['.implode(',', array_keys($data['type_options']['discontinuous'])).']'),
                    'errors'=>  array('in_list' => 'Display Type mst be one of: '.implode(', ', $data['type_options']['discontinuous']))
                ),*/
                array(
                    'field' =>  'content[speed]',
                    'label' =>  'Speed',
                    'rules' =>  array('required', 'numeric', 'greater_than[0]')
                )
            );

            //All Form Rules
            $form_rules_config = array(
                array(
                    'field' =>  'institution',
                    'label' =>  'Institution',
                    'rules' =>  array('required', 'max_length['.$data['max_length']['institution'].']')
                )
            );

            $form_rules_config = array_merge(
                $form_rules_config,
                $message_ann_rules,
                $emergency_ann_rules,
                $colour_rules,
                $location_rules,
                $weather_rules,
                $content_rules
            );
        }
        
        $this->form_validation->set_rules($form_rules_config);

        //Loading form with either settings data or previously submitted data
        $data['settings'] = (object) array(
            'institution'   => $this->load_settings_data('institution'),
            'message' => (object) array(
                'title'     =>  $this->load_settings_data('message', 'title'),
                'content'   =>  $this->load_settings_data('message', 'content'),
                'image'     =>  $this->load_settings_data('message', 'image')
            ),
            'emergency' => (object) array(
                'title'     =>  $this->load_settings_data('emergency', 'title'),
                'content'   =>  $this->load_settings_data('emergency', 'content'),
                'image'     =>  $this->load_settings_data('emergency', 'image')
            ),
            'colour' => (object) array(
                'foreground'    =>  $this->load_settings_data('colour', 'foreground'),
                'background'    =>  $this->load_settings_data('colour', 'background')
            ),
            'location' => (object) array(
                'city'      =>  $this->load_settings_data('location', 'city'),
                'country'   =>  $this->load_settings_data('location', 'country'),
                'zip'       =>  $this->load_settings_data('location', 'zip')
            ),
            'weather' => (object) array(
                'search'    =>  $this->load_settings_data('weather', 'search'),
                'units'     =>  $this->load_settings_data('weather', 'units')
            ),
            'content' => (object) array(
                'speed'     =>  $this->load_settings_data('content', 'speed'),
            )
        );

        //Remove later, don't need code
        {
            $data['dump_settings'] = print_r((object) $data['settings'], TRUE);
        }

        if($this->form_validation->run() == FALSE){
            $data['validation_errors'] = validation_errors();

            $this->load_view('dashboard/settings', $data, TRUE);
        }else{
            $this->load->helper('path');

            //Process images
            {
                $image_rules = array(
                    array(
                        'field' =>  'message[image]',
                        'label' =>  'Message Image',
                        'rules' =>  array('callback_process_image_upload[message[image],mimg,'.FALSE.','.UPLOAD_ANN.',mimg]')
                    ),
                    array(
                        'field' =>  'emergency[image]',
                        'label' =>  'Emergency Image',
                        'rules' =>  array('callback_process_image_upload[emergency[image],eimg,'.FALSE.','.UPLOAD_ANN.',eimg]')
                    )
                );

                $this->form_validation->set_data($_POST);
                $this->form_validation->set_rules($image_rules);
                
                if($this->form_validation->run() == FALSE){
                    $data['validation_errors'] = validation_errors();
                    $this->load_view('dashboard/settings', $data, TRUE);
                }
            }

            //Retrieve image names
            {
                $img = (object) array('e'=>'', 'm'=>'');

                if(isset($this->session->mimg)){
                    $img->m = $this->session->mimg;
                    unset($_SESSION['mimg']);
                }

                if(isset($this->session->eimg)){
                    $img->e = $this->session->eimg;
                    unset($_SESSION['eimg']);
                }
            }

            //Build Settings
            $settings = (object) array(
                'institution' =>  $this->input->post('institution'),
                'message' =>  (object) array(
                    'title'     =>  $this->input->post('message[title]'),
                    'content'   =>  $this->input->post('message[content]'),
                    'image'     =>  empty(trim($img->m)) ? $this->load_settings_data('message', 'image') : $img->m
                ),
                'emergency' =>  (object) array(
                    'title'     =>  $this->input->post('emergency[title]'),
                    'content'   =>  $this->input->post('emergency[content]'),
                    'image'     =>  empty(trim($img->e)) ? $this->load_settings_data('emergency', 'image') : $img->e
                ),
                'colour' =>  (object) array(
                    'foreground'    =>   $this->input->post('colour[foreground]'),
                    'background'    =>   $this->input->post('colour[background]')
                ),
                'location' =>  (object) array( 
                    'city'      =>  $this->input->post('location[city]'),
                    'country'   =>  $this->input->post('location[country]'),
                    'zip'       =>  $this->input->post('location[zip]')
                ),
                'weather' =>  (object) array(
                    'search'    =>  intval($this->input->post('weather[search]')),
                    'units'     =>  $this->input->post('weather[units]')
                ),
                'content' =>  (object) array(
                    'discontinuous' =>  TRUE, //$this->input->post('content[type]'),
                    'speed'         =>  floatval($this->input->post('content[speed]'))
                )
            );

            //Check if existing image is to be removed
            {
                $remove_image = array(
                    'message'   =>  $this->input->post('message[remove_image]'),
                    'emergency' =>  $this->input->post('emergency[remove_image]')
                );

                foreach ($remove_image as $field => $rem_img) {
                    if($rem_img !== NULL && boolval($rem_img) == TRUE){
                        $path = './'.UPLOAD_ANN.$settings->$field->image;

                        if(file_exists($path)) unlink($path);

                        $settings->$field->image = '';
                    }
                }
            }

            //Write settings to config json file
            $config_json = fopen(set_realpath(SETTINGS_PATH).'config.json', 'w');
            $res = fwrite($config_json, json_encode($settings, JSON_PRETTY_PRINT));         
            fclose($config_json);
            
            //Updating settings and reporting completion
            $this->session->settings_update = $res;
            $this->session->mark_as_flash('settings_update');

            redirect('dashboard');
        }
    }

    /**
     * Retrieves the value of the required setting
     * @param  String   $category   name of setting category
     * @param  String   $name       name of setting in specified category
     * @return Mixed    value of setting
     */
    private function load_settings_data($category, $name = NULL){
        foreach($this->config->item('ru_settings') as $key => $val){
            $settings[$key] = $val;
        }
        $settings = (object) $settings;

        if(!is_null($name)){
            $val = $this->input->post($category.'['.$name.']');

            return $val !== NULL && trim($val) !== '' ? $val : $settings->$category->$name;
        }else{
            $val = $this->input->post($category);

            return $val !== NULL && trim($val) !== '' ? $val : $settings->$category;
        }
    }
}
?>
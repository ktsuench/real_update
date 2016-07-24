<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Navigation.php';

class Announcement extends Navigation{
    const CALENDAR_DAY_ID_PREFIX= '-cal-day-';
    const CALENDAR_DAY_CLASS = 'cal-day';

    public function __construct(){
        parent::__construct();
        $this->load->model('announcement_model');
    }
    
    public function index($admin_mode = FALSE){
        if($admin_mode == FALSE || $this->session->user->type != ADMIN){
            $data['announcement'] = $this->announcement_model->get_announcement();
        }else if($this->session->user->type == ADMIN){
            $data['announcement'] = $this->announcement_model->get_announcement(FALSE, TRUE);
        }
        $data['title'] = 'Announcements List';
        $data['stylesheet'][] = 'ann_list.css';
        $data['admin_mode'] = $this->session->user->type != ADMIN ? FALSE : $admin_mode;
        
        if(isset($this->session->res)){
            if($this->session->op == OP_CREATE || $this->session->op == OP_CREATE_BATCH){
                $f = 'submit';
                $s = $f.'ted';
            }else if($this->session->op == OP_UPDATE){
                $f = 'update';
                $s = $f.'d';
            }else if($this->session->op == OP_VERIFY){
                $f = 'verify';
                $s = 'verified';
            }else if($this->session->op == OP_DELETE || $this->session->op == OP_DELETE_ALL){
                $f = 'remove';
                $s = $f.'d';
            }
            
            $f = $s .= ' announcement';
            
            if($this->session->op == OP_CREATE_BATCH || $this->session->op == OP_DELETE_ALL) $f = $s .= 's';
            
            $succ = 'Sucessfully '.$s.'.';
            $fail = 'Failed to '.$f.', try again later.';
            $data['announcement_res'] = $this->session->res ? $succ : $fail ;
        }
        
        $this->load_view('announcements', $data, TRUE);
    }

    //Public method because it is accessed in JS script
    //Used by create_template Method
    public function get_calendar(){
        //Redirect user when trying to directly access method
        $condition_a = $this->allow_access_route($this->agent->referrer(), array('announcement/create', 'announcement/update'), '', TRUE);
        $condition_b = $this->allow_access_class('announcement', array('create', 'update'), '', TRUE);
        
        if($condition_a === FALSE && $condition_b === FALSE) redirect('announcement');

        //Template for no content cells in Calendar
        $tid = self::CALENDAR_DAY_ID_PREFIX;
        $tcls = self::CALENDAR_DAY_CLASS;
        
        $prefs = array(
            'show_next_prev'    =>  TRUE,
            'next_prev_url'     =>  'jump_to_date_btn()',
            'show_other_days'   =>  FALSE,
            'template'          =>  array(
                'heading_previous_cell'     => '<th><span class="prev-month-url month-url" onclick="{previous_url}">&lt;&lt;</span></th>',
                'heading_next_cell'         => '<th><span class="next-month-url month-url" onclick="{next_url}">&gt;&gt;</span></th>',
                'cal_cell_no_content'       =>  '<span class="'.$tcls.'" href="">{day}</span>',
                'cal_cell_no_content_today' =>  '<span class="'.$tcls.' selected" href="">{day}</span>'
            )
        );
        
        $this->load->library('calendar', $prefs);
        
        if($this->input->post('echo')){
            $uri_month = $this->uri->segment(4);
            $uri_year = $this->uri->segment(3);
            
            echo $this->calendar->generate($uri_year, $uri_month);
        }
    }

    /**
     * [build_datetime_rules description]
     * @param  array  $list   list of valid values
     * @param  array  $err    list of valid values in readable format
     * @param  string $prefix input field name prefix
     * @return array  $result rules and field names
     */
    protected function build_datetime_rules($list, $err, $prefix){
        $date_rule = array(
            array(
                'rules'     =>  array('required', 'callback_verify_date['.$prefix.']'),
                'errors'    =>  array('required'  =>  'A '.ucfirst($prefix).' Date needs to be selected.')
            ),
            array(
                'rules'     =>  array('required', 'in_list['.$list['month'].']'),
                'errors'    =>  array('in_list' =>  'The '.ucfirst($prefix).' Month field must be one of: '.$err['month'].'.')
            ),
            array(
                'rules'     =>  array('required', 'in_list['.$list['year'].']'),
                'errors'    =>  array('in_list' =>  'The '.ucfirst($prefix).' Year field must be one of: '.$err['year'].'.')
            ),
            array(
                'rules'     =>  array('required', 'in_list['.$list['hour'].']'),
                'errors'    =>  array('in_list' =>  'The '.ucfirst($prefix).' Hour field must be one of: '.$err['hour'].'.')
            ),
            array(
                'rules'     =>  array('required', 'in_list['.$list['minute'].']'),
                'errors'    =>  array('in_list' =>  'The '.ucfirst($prefix).' Minute field must be one of: '.$err['minute'].'.')
            ),
            array(
                'rules'     =>  array('required', 'in_list['.$list['meridian'].']'),
                'errors'    =>  array('in_list' =>  'The '.ucfirst($prefix).' Meridian field must be one of: '.$err['meridian'])
            )
        );

        $date_suffix = array('date', 'month', 'year', 'hour', 'minute', 'meridian');

        foreach($date_suffix as $suffix){
            $date_fields[$prefix.'-'.$suffix] = ucfirst($prefix).' '.ucfirst($suffix);
        }

        $result['fields'] = array_keys($date_fields);

        $i = 0;
        foreach($date_fields as $field => $label){
            if($i == count($date_rule)) $i = 0;

            $date_rule[$i]['field'] = $field;
            $date_rule[$i]['label'] = $label;

            $result['config'][] = $date_rule[$i++];
        }

        return $result;
    }

    //Set the datetime string using the submitted form values
    //Used by create_template Method
    protected function set_datetime($index){
        $hf_val = $this->input->post($index.'-hour');
        //Retrieving session variable values: due to direct modification of session variables not allowed
        //$create = $this->session->ann_create;
                
        //Switch hour format from 12 hour format to 24 hour format
        if($this->input->post($index.'-meridian') == 'am'){
            if(intval($hf_val) == 12) $hf_val = '0';
            if(intval($hf_val) < 10) $hf_val = '0' . $hf_val;
        }else if(intval($hf_val) < 12) 
            $hf_val = '' . (intval($hf_val) + 12);
        
        //Build datetime string from form inputs
        $datetime_str = $this->input->post($index.'-year') . '-' . $this->input->post($index.'-month') . '-' . $this->input->post($index.'-date');
        $datetime_str .= 'T' . $hf_val . ':' . $this->input->post($index.'-minute') . ':00';

        $date = new DateTime($datetime_str);

        return $date;
    }
    
    //Used by create_template Method for validation purposes
    public function verify_date($date, $order){
        //Redirect user when trying to directly access method
        $this->allow_access_route($this->agent->referrer(), array('announcement/create', 'announcement/update'), 'announcement');

        try{
            $this->set_datetime($order);
            return TRUE;
        }catch(Exception $e){
            $err = 'Please select a valid '.$order.' date.';
            $this->form_validation->set_message('verify_date', $err);
            return FALSE;
        }
    }

    //Used by create_template Method for validation purposes
    public function check_dateinterval($datetime){
        //Redirect user when trying to directly access method
        $this->allow_access_route($this->agent->referrer(), array('announcement/create', 'announcement/update'), 'announcement');

        $datetime = explode(',', $datetime);
        $start_datetime = new DateTime($datetime[0]);
        $end_datetime = new DateTime($datetime[1]);
        $interval = $start_datetime->diff($end_datetime);
        $res = FALSE;
        
        if($interval->y >= 0 && $interval->m >= 0 && $interval->d >= 0 &&
            $interval->h >= 0 && $interval->i >= 0 && $interval->invert == 0) $res = TRUE;
            
        if($res === FALSE){
            $err = 'The scheduled start date must be before the scheduled end date.';
            $this->form_validation->set_message('check_dateinterval', $err);
        }
        
        return $res;
    }

    //Template for Create and Update Methods
    protected function create_template($settings, $op, $slug = FALSE){
        $data = $settings;
        $data['upload_path'] = base_url().UPLOAD_ANN;
        $data['upload_path_temp'] = base_url().UPLOAD_TMP;
        
        $data['title_max_length'] = 50;
        $data['content_max_length'] = 150;

        //Array List of Types
        //TODO: Put this in the settings page so that it can be changed by admins
        $data['type_options'] = array(  'daily'     =>  'Daily',
                                        'important' =>  'Important',
                                        'meeting'   =>  'Meeting',
                                        'sports'    =>  'Sports',
                                        'other'     =>  'Other');
        $type_list = implode(',', array_keys($data['type_options']));
        $type_err_msg = implode(', ', $data['type_options']);
        
        //String of Allowed Image Types
        $data['image_file_types'] = 'image/*';
        
        //Calendar Settings
        {
            self::get_calendar();

            //Reference DateTime
            $data['now'] = $now = new DateTime('now');
            
            //Array List of Months
            for($i = 1; $i < 13; $i++){
                $m = ($i < 10 ? '0' : '').$i;
                $data['month_options'][$m] = $this->calendar->get_month_name($m);
            }
            $list['month'] = implode(',', array_keys($data['month_options']));
            $err['month'] = implode(', ', $data['month_options']);
            
            //Array List of Years
            for($i = intval($now->format('Y')); $i < intval($now->format('Y')) + 11; $i++) 
                $data['year_options'][$i] = $i;
            $list['year'] = implode(',', array_keys($data['year_options']));
            $err['year'] = implode(', ', $data['year_options']);
            
            //Array List of Hours
            for($i = 1; $i < 13; $i++) $data['hour_options'][$i] = $i;
            $list['hour'] = implode(',', array_keys($data['hour_options']));
            $err['hour'] = implode(', ', $data['hour_options']);
            
            //Array List of Minutes
            for($i = 0; $i < 60; $i += 15) $data['minute_options'][($i < 10 ? '0' : '').$i] = ($i < 10 ? '0' : '').$i;
            $list['minute'] = implode(',', array_keys($data['minute_options']));
            $err['minute'] = implode(', ', $data['minute_options']);
            
            //Array List of Meridians
            $data['meridian_options'] = array(
                'am'    =>  'A.M.',
                'pm'    =>  'P.M.'
            );
            $list['meridian'] = implode(',', array_keys($data['meridian_options']));
            $err['meridian'] = implode(', ', $data['meridian_options']);
        }

        //Form Rule Configuration
        {
            $form_rules_config = array(
                array(
                    'field' =>  'title',
                    'label' =>  'Title',
                    'rules' =>  array('required', 'max_length['.$data['title_max_length'].']')
                ),
                array(
                    'field' =>  'content',
                    'label' =>  'Content',
                    'rules' =>  array('required', 'max_length['.$data['content_max_length'].']')
                ),
                array(
                    'field' =>  'type',
                    'label' =>  'Type',
                    'rules' =>  array('required', 'in_list['.$type_list.']'),
                    'errors'    =>  array('in_list' =>  'The Type field must be one of: '.$type_err_msg.'.')
                )
            );

            $start_rules = self::build_datetime_rules($list, $err, 'start');
            $end_rules = self::build_datetime_rules($list, $err, 'end');

            $form_rules_config = array_merge($form_rules_config, $start_rules['config'], $end_rules['config']);
        }
        
        $data['date_fields'] = array($start_rules['fields'], $end_rules['fields']);
        $data['date_id_prefix'] = self::CALENDAR_DAY_ID_PREFIX;

        //Validate that the start date is before the end date
        try{
            $start_datetime = self::set_datetime('start')->format('Y-m-d\TH:i:sP');
            $end_datetime = self::set_datetime('end')->format('Y-m-d\TH:i:sP');

            $this->form_validation->set_data(array('dateinterval' => $start_datetime.','.$end_datetime));
            $this->form_validation->set_rules('dateinterval', 'Date Interval','callback_check_dateinterval');
            $this->form_validation->run();
        }catch(Exception $e){
            //if(ENVIRONMENT != ENV_PRODUCTION) echo $e->getMessage();
        }

        $this->form_validation->set_data($_POST);
        $this->form_validation->set_rules($form_rules_config);

        if($this->form_validation->run() == FALSE){
            $this->load_view('announcements/create', $data, TRUE);
        }else{
            $this->form_validation->set_data($_POST['image']);
            $this->form_validation->set_rules('image', 'Image', 'callback_process_image_upload[,TRUE,,ann_img]');
            if($this->form_validation->run() == FALSE) $this->load_view('announcements/create', $data, TRUE);

            /**
             * TODO: fixup the submission process
             */
            $create = array(
                'op'        =>  $op,
                'verified'  =>  $this->session->user->type == ADMIN ? 1 : 0,
                'schedule'  =>  array(
                    'start' =>  self::set_datetime('start'),
                    'end'   =>  self::set_datetime('end')
                )
            );

            //Check if existing image is to be removed
            if($this->input->post('remove_image') !== NULL && boolval($this->input->post('remove_image')) == TRUE){
                $create['remove_image'] = $remove_image = TRUE;
            }else $create['remove_image'] = $remove_image = FALSE;

            //Set image if there is one
            if(isset($this->session->ann_img)){
                $create['image'] = $this->session->ann_img;

                unset($_SESSION['ann_img']);
            }else if($remove_image == TRUE){
                if(isset($create['image'])) unset($create['image']);
            }else if(isset($this->session->ann_create['image'])){
                $create['image'] = $this->session->ann_create['image'];
            }else if(!empty($data['ann_data'])){
                $create['image'] = $data['ann_data']->image;
            }

            foreach($this->input->post(array('title', 'content', 'type')) as $key => $val) $create[$key] = $val;
            
            //Insert primary key if provided
            if($slug !== FALSE) $create['slug'] = $slug;
            
            $this->session->ann_create = $create;
            $this->session->mark_as_flash('ann_create');
            
            //Pass ann_data on to schedule method (used when updating announcements)
            if(!empty($data['ann_data'])){
                $this->session->ann_data = $data['ann_data'];
                $this->session->mark_as_flash('ann_data');
            }
            
            //Storing announcement data into db and reporting completion
            $this->session->op = $this->session->ann_create['op']['type'];
            
            $slug = $this->session->op == OP_UPDATE ? $this->session->ann_create['slug'] : FALSE;
            $this->session->res = $this->announcement_model->set_announcement($slug) ? TRUE : FALSE;
            
            $this->session->mark_as_flash(array('op', 'res'));
            redirect('announcement');
        }
    }

    public function create(){
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['title'] = 'Announcement Create';
        $data['page_action'] = 'announcement/create';
        
        self::create_template($data, array('name' => 'create', 'type' => OP_CREATE));
    }
    
    public function update($slug = NULL){
        if(!is_null($slug)){
            if(!empty($this->announcement_model->get_announcement($slug))){
                foreach($this->announcement_model->get_announcement($slug) as $key => $val){
                    $ann_data[$key] = $val;
                }
                $ann_data['start_datetime'] = new DateTime($ann_data['start_datetime']);
                $ann_data['end_datetime'] = new DateTime($ann_data['end_datetime']);
                $data['ann_data'] = (object) $ann_data;
                
                $this->load->helper('form');
                $this->load->library('form_validation');
                
                $data['title'] = 'Announcement Update';
                $data['page_action'] = 'announcement/update/'.$slug;
                
                self::create_template($data, array('name' => 'update', 'type' => OP_UPDATE), $slug);
            }else{
                redirect('announcement/create');
            }
        }else{
            redirect('announcement/create');
        }
    }

    //TODO: Add instructions to the index page
    //TODO: Validate csv files (i.e. correct format, valid data)
    public function create_batch(){
        $this->load->library('form_validation');
        
        $data['title'] = 'Announcement Create (Batch)';
        
        $data['page_title'] = 'Announcement Create (Batch)';
        $data['page_action'] = 'announcement/create/batch';
        $data['admin_access_only'] = TRUE;
        $data['field_name'] = $field_name = 'Upload';
        
        //Upload Library Config
        {
            $config['upload_path'] = './'.UPLOAD_TMP;
            $config['allowed_types'] = 'csv';
            $config['overwrite'] = TRUE;
            $config['encrypt_name'] = TRUE;
        }
        
        $this->load->library('upload', $config);
        
        if(!$this->upload->do_upload($field_name)){
            $data['error'] = isset($_FILES[$field_name]) ? $this->upload->display_errors() : '';
            $this->load_view('announcements/create/batch', $data, TRUE);
        }else{
            //File information
            $file_info = $this->upload->data();
            
            //File Pointer
            $csv_file = fopen($file_info['full_path'], 'r');
            
            //Read file
            while(($line = fgetcsv($csv_file)) !== FALSE && !empty($line[0])){
                //TODO: Validate csv files (i.e. correct format, valid data)
                //validate_csv($line);
                $announcements[] = array(
                    'title'             =>  $line[0],
                    'content'           =>  $line[1],
                    'type'              =>  $line[2],
                    'author'            =>  $line[3],
                    'start_datetime'    =>  $line[4],
                    'end_datetime'      =>  $line[5]
                );
            }
            
            //Close and delete file after finished reading
            fclose($csv_file);
            unlink($file_info['full_path']);
            
            //Add announcements to db
            $this->session->op = OP_CREATE_BATCH;
            $this->session->res = $this->announcement_model->set_announcement_batch($announcements) ? TRUE : FALSE;
            $this->session->mark_as_flash(array('op', 'res'));
            redirect('announcement');
        }
    }
    
    //Used for verifying and deleting announcements
    protected function operation_template($op = FALSE, $model_method = FALSE){
        if($op !== FALSE && $model_method != FALSE){
            $this->session->op = $op;
            $this->session->res = $model_method;
            $this->session->mark_as_flash(array('op', 'res'));
        }
    }

    public function verify($slug = NULL, $status = 0){
        if($this->session->user->type == ADMIN){
            self::operation_template(OP_VERIFY, $this->announcement_model->verify_announcement($slug, intval($status)));
        }

        $routes = array('announcement', 'announcement/all');
        $this->redirect_to($this->agent->referrer(), 'announcement', $routes);
    }

    public function delete($slug = NULL){
        if(!is_null($slug)){
            self::operation_template(OP_DELETE, $this->announcement_model->rem_announcement($slug));
        }

        $routes = array('announcement', 'announcement/all');
        $this->redirect_to($this->agent->referrer(), 'announcement', $routes);
    }
    
    public function delete_all(){
        if($this->session->user->type == ADMIN){
            $ref = $this->agent->referrer();
            if($ref != FALSE){
                $route = substr($ref, strlen($ref) - stripos(strrev($ref), '/'));
                if($route == 'announcement'){
                    self::operation_template(OP_DELETE_ALL, $this->announcement_model->rem_announcement_all($this->session->user->email));
                }else if(stripos($ref, 'announcement/all') !== FALSE){
                    self::operation_template(OP_DELETE_ALL, $this->announcement_model->rem_announcement_all());
                }
            }
        }

        $routes = array('announcement', 'announcement/all');
        $this->redirect_to($this->agent->referrer(), 'announcement', $routes);
    }
    
    //Used by Display
    public function update_list(){
        //Redirect user when trying to directly access method
        $this->allow_access_route($this->agent->referrer(), 'display', 'display');
        
        $announcement = $this->announcement_model->get_announcement_display();
        $info_req = array();

        if(isset($announcement)){
            foreach($announcement as $a){
                $info_req[] = (object) array(
                    'title'     =>  $a['title'],
                    'content'   =>  $a['content'],
                    'image'     =>  $a['image']
                );
            }
        }
        echo json_encode($info_req, JSON_PRETTY_PRINT);
    }

    //Used by Display
    public function update_weather(){
        //Redirect user when trying to directly access method
        $this->allow_access_route($this->agent->referrer(), 'display', 'display');

        $appid = '0d93580d7ee4d84bdc222908774fc07b';
        $zip = 'M4M2A1,ca';
        $units = 'metric';
        $link = 'http://api.openweathermap.org/data/2.5/weather?zip='.$zip.'&units='.$units.'&appid='.$appid;

        try{
            echo @file_get_contents($link);
        }catch(Exception $e){
            if(ENVIRONMENT != ENV_PRODUCTION) throw $e;
            log_message('debug', $e->getMessage());
        }
    }

    public function display(){
        $data['announcement'] = $this->announcement_model->get_announcement_display();
        $data['title'] = 'Riverdale Collegiate Institute Announcements Display';
        $data['stylesheet'][] = 'ann_disp.css';
        $data['upload_path'] = base_url().UPLOAD_ANN;

        //Do not display the copyright in footer template file
        $data['do_not_display'] = TRUE;

        $this->load_view('announcements/display', $data);
    }
}
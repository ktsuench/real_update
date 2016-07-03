<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Navigation.php';

class Announcement extends Navigation{
    const TMP_UPLOAD_PATH = './uploads/tmp/';

    protected static $upload_path;
    protected static $upload_path_temp;

    public function __construct(){
        parent::__construct();
        $this->load->model('announcement_model');

        self::$upload_path = base_url().'uploads/ann_content/';
        self::$upload_path_temp = base_url().'uploads/tmp/';
    }
    
    //TODO: move the session var dumping into construct
    //NOTE: cannot do it without constantly erasing data while creating/updating data
    public function index($admin_mode = FALSE){
        if($admin_mode == FALSE || $this->session->user->type != self::ADMIN){
            $data['announcement'] = $this->announcement_model->get_announcement();
        }else if($this->session->user->type == self::ADMIN){
            $data['announcement'] = $this->announcement_model->get_announcement(FALSE, TRUE);
        }
        $data['title'] = 'Announcements List';
        $data['stylesheet'][] = 'ann_list.css';
        $data['admin_mode'] = $admin_mode;
        
        if(isset($this->session->ann_create)){
            //$this->session->unset_tempdata('ann_create');
            unset($_SESSION['ann_create']);
        }
        
        if(isset($this->session->ann_data)){
            //$this->session->unset_tempdata('ann_data');
            unset($_SESSION['ann_data']);
        }
        
        if(isset($this->session->res)){
            if($this->session->op == self::OP_CREATE || $this->session->op == self::OP_CREATE_BATCH){
                $f = 'submit';
                $s = $f.'ted';
            }else if($this->session->op == self::OP_UPDATE){
                $f = 'update';
                $s = $f.'d';
            }else if($this->session->op == self::OP_VERIFY){
                $f = 'verify';
                $s = 'verified';
            }else if($this->session->op == self::OP_DELETE || $this->session->op == self::OP_DELETE_ALL){
                $f = 'remove';
                $s = $f.'d';
            }
            
            $f = $s .= ' announcement';
            
            if($this->session->op == self::OP_CREATE_BATCH || $this->session->op == self::OP_DELETE_ALL) $f = $s .= 's';
            
            $succ = 'Sucessfully '.$s.'.';
            $fail = 'Failed to '.$f.', try again later.';
            $data['announcement_res'] = $this->session->res ? $succ : $fail ;
        }
        
        $this->load_view('announcements', $data, TRUE);
    }

    public function process_image_upload(){
        try{
            $err = array_key_exists('image', $_FILES) ? $_FILES['image']['error'] : UPLOAD_ERR_NO_FILE;

            $tmp_name = $_FILES['image']['tmp_name'];
            $name = basename($_FILES['image']['name']);

            //Error messages
            if($err != UPLOAD_ERR_OK && ENVIRONMENT == 'development'){
                switch($err){ 
                    case UPLOAD_ERR_INI_SIZE:
                        $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = "The uploaded file was only partially uploaded";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        //$message = "No file was uploaded";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $message = "Missing a temporary folder";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $message = "Failed to write file to disk";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $message = "File upload stopped by extension";
                        break;
                    default:
                        $message = "Unknown upload error";
                        break;
                }
            }else if($err != UPLOAD_ERR_OK){
                switch($err){ 
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = "Failed to upload image. Try selecting a smaller image.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    case UPLOAD_ERR_PARTIAL:
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                    case UPLOAD_ERR_EXTENSION:
                        $message = "Failed to upload file. Try again later.";
                        break;
                    default:
                        $message = "Unknown upload error";
                        break;
                }
            }

            $err = isset($message) ? 'Upload error. '.$message : $err;

            if($err === UPLOAD_ERR_OK){
                if(strpos($_FILES['image']['type'], 'image') === 0){
                    if(move_uploaded_file($tmp_name, self::TMP_UPLOAD_PATH.$name)){
                        //Update the temp files session variable for garbage collection later
                        $tmp = isset($this->session->temp_files) ? $this->session->temp_files : array();
                        $tmp[] = $name;
                        $this->session->temp_files = $tmp;

                        //Store in session variable for later use
                        $this->session->ann_img = $name;
                    }else $upload_err = (ENVIRONMENT == 'development') ? 'Could not move uploaded file.' : $err;
                }else $upload_err = 'Please upload a valid image file.';
            }else $upload_err = $err === UPLOAD_ERR_NO_FILE ? NULL : $err;

            if(isset($upload_err)){
                $this->form_validation->set_message('process_image_upload', $upload_err);
                return FALSE;
            }else return TRUE;
        }catch(Exception $e){
            if(ENVIRONMENT != 'production') throw $e;
        }
    }

    //Template for Create and Update Methods
    protected function create_template($settings, $op, $slug = FALSE){
        $data = $settings;
        $data['upload_path'] = self::$upload_path;
        $data['upload_path_temp'] = self::$upload_path_temp;
        
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

        //Form Rule Configuration
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
            ),
            array(
                'field' =>  'image',
                'label' =>  'Image',
                'rules' =>  array('callback_process_image_upload')
            )
        );
        
        $this->form_validation->set_rules($form_rules_config);
        
        if($this->form_validation->run() == FALSE){
            $this->load_view('announcements/create', $data, TRUE);
        }else{
            $create = array(
                'op'        =>  $op,
                'verified'  =>  $this->session->user->type == self::ADMIN ? 1 : 0,
                'schedule'  =>  array(
                    'start' =>  isset($this->session->ann_create) ? $this->session->ann_create['schedule']['start'] : '',
                    'end'   =>  isset($this->session->ann_create) ? $this->session->ann_create['schedule']['end'] : ''
                )
            );

            //Check if existing image is to be removed
            if($this->input->post('remove_image') !== NULL && boolval($this->input->post('remove_image')) == TRUE){
                $remove_image = TRUE;
            }else $remove_image = FALSE;

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

            //Clean up image directories
            if((isset($this->session->ann_create['image']) && $this->session->ann_create['image'] != $create['image']) || $remove_image){
                unlink(self::TMP_UPLOAD_PATH.$this->session->ann_create['image']);
                
                //Remove image file from garbage collection
                $tmp = $this->session->temp_files;
                unset($tmp[$this->session->ann_create['image']]);
                $this->session->temp_files = $tmp;
            }

            foreach($this->input->post() as $key => $val) $create[$key] = $val;
            
            //Insert primary key if provided
            if($slug !== FALSE) $create['slug'] = $slug;
            
            $this->session->ann_create = $create;
            //$this->session->mark_as_temp('ann_create');
            
            //Pass ann_data on to schedule method (used when updating announcements)
            if(!empty($data['ann_data'])){
                $this->session->ann_data = $data['ann_data'];
                //$this->session->mark_as_temp('ann_data');
            }
            
            redirect('announcement/schedule');
        }
    }

    public function create(){
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['title'] = 'Announcement Create';
        $data['page_action'] = 'announcement/create';
        
        self::create_template($data, array('name' => 'create', 'type' => self::OP_CREATE));
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
                
                self::create_template($data, array('name' => 'update', 'type' => self::OP_UPDATE), $slug);
            }else{
                redirect('announcement/create');
            }
        }else{
            redirect('announcement/create');
        }
    }

    //Public method because it is accessed in JS script
    //Used by Schedule Method
    public function get_calendar(){
        //Redirect user when trying to directly access method
        if(stripos($this->agent->referrer(), 'announcement/create') !== FALSE ||
            stripos($this->agent->referrer(), 'announcement/update') !== FALSE ||
            stripos($this->agent->referrer(), 'announcement/schedule') !== FALSE){
            //Template for no content cells in Calendar
            $tid = 'cal_day_{day}';
            $tcls = 'cal_day';
            
            $prefs = array(
                'show_next_prev'    =>  TRUE,
                'next_prev_url'     =>  'jump_to_date()',
                'show_other_days'   =>  FALSE,
                'template'          =>  array(
                    'heading_previous_cell'     => '<th><span onclick="{previous_url}">&lt;&lt;</span></th>',
                    'heading_next_cell'         => '<th><span onclick="{next_url}">&gt;&gt;</span></th>',
                    'cal_cell_no_content'       =>  '<span id="'.$tid.'" class="'.$tcls.'" href="">{day}</span>',
                    'cal_cell_no_content_today' =>  '<span id="'.$tid.'" class="'.$tcls.' selected" href="">{day}</span>'
                )
            );
            
            $this->load->library('calendar', $prefs);
            
            if($this->input->post('echo')){
                $uri_month = $this->uri->segment(4);
                $uri_year = $this->uri->segment(3);
                
                echo $this->calendar->generate($uri_year, $uri_month);
            }
        }else{
            redirect('announcement');
        }
    }

    //Set the datetime string using the submitted form values
    //Used by Schedule Method
    protected function set_datetime($index){
        $hf_val = $this->input->post('hour');
        //Retrieving session variable values: due to direct modification of session variables not allowed
        $create = $this->session->ann_create;
                
        //Switch hour format from 12 hour format to 24 hour format
        if($this->input->post('meridian') == 'am'){
            if(intval($hf_val) == 12) $hf_val = '0';
            if(intval($hf_val) < 10) $hf_val = '0' . $hf_val;
        }else if(intval($hf_val) < 12) 
            $hf_val = '' . (intval($hf_val) + 12);
        
        //Build datetime string from form inputs
        $datetime_str = $this->input->post('year') . '-' . $this->input->post('month') . '-' . $this->input->post('date');
        $datetime_str .= 'T' . $hf_val . ':' . $this->input->post('minute') . ':00';
        
        $create['schedule'][$index] = new DateTime($datetime_str);
        
        return $create;
    }
    
    //Used by Schedule Method for validation purposes
    public function verify_date(){
        //Redirect user when trying to directly access method
        if(stripos($this->agent->referrer(), 'announcement/create') !== FALSE ||
            stripos($this->agent->referrer(), 'announcement/update') !== FALSE ||
            stripos($this->agent->referrer(), 'announcement/schedule') !== FALSE){
            try{
                $this->set_datetime('start');
                return TRUE;
            }catch(Exception $e){
                $err = 'Please select a valid date.';
                $this->form_validation->set_message('verify_date', $err);
                return FALSE;
            }
        }else{
            redirect('announcement');
        }
    }

    //Used by Schedule Method for validation purposes
    public function check_dateinterval($date, $extra){
        //Redirect user when trying to directly access method
        if(stripos($this->agent->referrer(), 'announcement/create') !== FALSE ||
            stripos($this->agent->referrer(), 'announcement/update') !== FALSE ||
            stripos($this->agent->referrer(), 'announcement/schedule') !== FALSE){
            $extra = explode(',', $extra);
            $start_datetime = new DateTime($extra[0]);
            $end_datetime = new DateTime($extra[1]);
            $interval = $start_datetime->diff($end_datetime);
            $res = FALSE;
            
            if($interval->y >= 0 && $interval->m >= 0 && $interval->d >= 0 &&
                $interval->h >= 0 && $interval->i >= 0 && $interval->invert == 0) $res = TRUE;
                
            if($res === FALSE){
                $err = 'The scheduled start date must be before the scheduled end date.';
                $this->form_validation->set_message('check_dateinterval', $err);
            }
            
            return $res;
        }else{
            redirect('announcement');
        }
    }

    public function schedule(){
        if(!empty($this->session->ann_create)){
            $this->load->helper('form');
            $this->load->library('form_validation');
            
            $data['title'] = 'Announcement Scheduling';
            $data['page_action'] = 'announcement/schedule';
            
            //Clear existing scheduled start date so that it may be edited
            if(empty($this->input->post()) && stripos($this->agent->referrer(), 'schedule') !== FALSE){
                $create = $this->session->ann_create;
                $data['prev_start_date'] = $create['schedule']['start'];
                $create['schedule']['start'] = '';
                $this->session->ann_create = $create;
            }
            
            self::get_calendar();
            
            //Form Reference and Default Values
            {
                //Reference DateTime
                $data['now'] = $now = new DateTime('now');
                
                //Array List of Months
                for($i = 1; $i < 13; $i++){
                    $m = ($i < 10 ? '0' : '').$i;
                    $data['month_options'][$m] = $this->calendar->get_month_name($m);
                }
                $month_list = implode(',', array_keys($data['month_options']));
                $month_err_msg = implode(', ', $data['month_options']);
                
                //Array List of Years
                for($i = intval($now->format('Y')); $i < intval($now->format('Y')) + 11; $i++) 
                    $data['year_options'][$i] = $i;
                $year_list = implode(',', array_keys($data['year_options']));
                $year_err_msg = implode(', ', $data['year_options']);
                
                //Array List of Hours
                for($i = 1; $i < 13; $i++) $data['hour_options'][$i] = $i;
                $hour_list = implode(',', array_keys($data['hour_options']));
                $hour_err_msg = implode(', ', $data['hour_options']);
                
                //Array List of Minutes
                for($i = 0; $i < 60; $i += 15) $data['minute_options'][($i < 10 ? '0' : '').$i] = ($i < 10 ? '0' : '').$i;
                $minute_list = implode(',', array_keys($data['minute_options']));
                $minute_err_msg = implode(', ', $data['minute_options']);
                
                //Array List of Meridians
                $data['meridian_options'] = array(
                    'am'    =>  'A.M.',
                    'pm'    =>  'P.M.'
                );
                $meridian_list = implode(',', array_keys($data['meridian_options']));
                $meridian_err_msg = implode(', ', $data['meridian_options']);
            }
            
            //Form Rule Configuration
            $form_rules_config = array(
                array(
                    'field' =>  'date',
                    'label' =>  'Date',
                    'rules' =>  array('required', 'callback_verify_date[]'),
                    'errors'    =>  array(
                        'required'  =>  'A Date needs to be selected.'
                    )
                ),
                array(
                    'field' =>  'month',
                    'label' =>  'Month',
                    'rules' =>  array('required', 'in_list['.$month_list.']'),
                    'errors'    =>  array('in_list' =>  'The Month field must be one of: '.$month_err_msg.'.')
                ),
                array(
                    'field' =>  'year',
                    'label' =>  'Year',
                    'rules' =>  array('required', 'in_list['.$year_list.']'),
                    'errors'    =>  array('in_list' =>  'The Year field must be one of: '.$year_err_msg.'.')
                ),
                array(
                    'field' =>  'hour',
                    'label' =>  'Hour',
                    'rules' =>  array('required', 'in_list['.$hour_list.']'),
                    'errors'    =>  array('in_list' =>  'The Hour field must be one of: '.$hour_err_msg.'.')
                ),
                array(
                    'field' =>  'minute',
                    'label' =>  'Minute',
                    'rules' =>  array('required', 'in_list['.$minute_list.']'),
                    'errors'    =>  array('in_list' =>  'The Minute field must be one of: '.$minute_err_msg.'.')
                ),
                array(
                    'field' =>  'meridian',
                    'label' =>  'Meridian',
                    'rules' =>  array('required', 'in_list['.$meridian_list.']'),
                    'errors'    =>  array('in_list' =>  'The Meridian field must be one of: '.$meridian_err_msg)
                )
            );
            
            //If end date is not a valid date then let the form validation procedure catch the error
            //Thus the catch in this try clause does nothing
            try{
                //Validate scheduled start date is before scheduled end date
                if(!empty($this->session->ann_create['schedule']['start']) && !empty($this->input->post())){
                    //Set end datetime
                    $this->session->ann_create = self::set_datetime('end');
                    
                    //Store datetime strings of scheduled dates
                    $start_datetime = $this->session->ann_create['schedule']['start']->format('Y-m-d\TH:i:sP');
                    $end_datetime = $this->session->ann_create['schedule']['end']->format('Y-m-d\TH:i:sP');
                    
                    //Set form validation rule to check date
                    $form_rules_config[0]['rules'][] = 'callback_check_dateinterval['.$start_datetime.','.$end_datetime.']';
                }
            }catch(Exception $e){}

            $this->form_validation->set_rules($form_rules_config);

            if(empty($this->session->ann_create['schedule']['start']) || $this->form_validation->run() == FALSE){
                if(empty($this->session->ann_create['schedule']['start']) && $this->form_validation->run() == FALSE)
                    $data['title'] .= ' - Start Date';
                else{
                    if(empty($this->session->ann_create['schedule']['start'])){
                        $this->session->ann_create = self::set_datetime('start');
                        if($_POST !== NULL) unset($_POST);
                    }
                    
                    $data['title'] .= ' - End Date';
                }
                
                $this->load_view('announcements/schedule', $data, TRUE);
            }else{
                //Storing announcement data into db and reporting completion
                $this->session->op = $this->session->ann_create['op']['type'];
                
                $slug = $this->session->op == self::OP_UPDATE ? $this->session->ann_create['slug'] : FALSE;
                $this->session->res = $this->announcement_model->set_announcement($slug) ? TRUE : FALSE;
                
                $this->session->mark_as_flash(array('op', 'res'));
                redirect('announcement');
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
            $config['upload_path'] = './uploads/';
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
            $this->session->op = self::OP_CREATE_BATCH;
            $this->session->res = $this->announcement_model->set_announcement_batch($announcements) ? TRUE : FALSE;
            $this->session->mark_as_flash(array('op', 'res'));
            redirect('announcement');
        }
    }
    
    //Used for verifying and deleting announcements
    protected function operation_template($op = FALSe, $model_method = FALSE){
        if($op !== FALSE && $model_method != FALSE){
            $this->session->op = $op;
            $this->session->res = $model_method;
            $this->session->mark_as_flash(array('op', 'res'));
        }
    }

    //Used to redirect user to previous paget hey were on
    protected function redirect_to($ref = FALSE){
        if($ref != FALSE){
            $route = substr($ref, strlen($ref) - stripos(strrev($ref), '/'));
            if($route == 'announcement' || stripos($ref, 'announcement/view/all') !== FALSE){
                redirect($this->agent->referrer());
            }
        }
        redirect('announcement');
    }

    public function verify($slug = NULL, $status = 0){
        if($this->session->user->type == self::ADMIN){
            self::operation_template(self::OP_VERIFY, $this->announcement_model->verify_announcement($slug, intval($status)));
        }

        self::redirect_to($this->agent->referrer());
    }

    public function delete($slug = NULL){
        if(!is_null($slug)){
            self::operation_template(self::OP_DELETE, $this->announcement_model->rem_announcement($slug));
        }

        self::redirect_to($this->agent->referrer());
    }
    
    public function delete_all(){
        if($this->session->user->type == self::ADMIN){
            self::operation_template(self::OP_DELETE_ALL, $this->announcement_model->rem_announcement_all());
        }

        self::redirect_to($this->agent->referrer());
    }
    
    //Used by Display
    public function update_list(){
        //Redirect user when trying to directly access method
        if(stripos($this->agent->referrer(), 'announcement/display') !== FALSE){
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
        }else{
            redirect('announcement/display');
        }
    }

    //Used by Display
    public function update_weather(){
        //Redirect user when trying to directly access method
        if(stripos($this->agent->referrer(), 'announcement/display') !== FALSE){
            $appid = '0d93580d7ee4d84bdc222908774fc07b';
            $query = 'toronto,ca';
            $units = 'metric';
            $link = 'http://api.openweathermap.org/data/2.5/weather?q='.$query.'&units='.$units.'&appid='.$appid;

            echo file_get_contents($link);
        }else{
            redirect('announcement/display');
        }
    }

    public function display(){
        $data['announcement'] = $this->announcement_model->get_announcement_display();
        $data['title'] = 'Riverdale Collegiate Institute Announcements Display';
        $data['stylesheet'][] = 'ann_disp.css';
        $data['upload_path'] = self::$upload_path;

        //Do not display the copyright in footer template file
        $data['do_not_display'] = TRUE;

        $this->load_view('announcements/display', $data);
    }
}
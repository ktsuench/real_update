<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Navigation.php';

class User extends Navigation{
    protected static $type_options = array(
        'guest'    =>  'Guest',
        'admin'    =>  'Admin'
    );
    protected static $form_rules_config = array(
        array(
            'field' =>  'first_name',
            'label' =>  'First Name',
            'rules' =>  array('required', 'max_length[50]')
        ),
        array(
            'field' =>  'last_name',
            'label' =>  'Last Name',
            'rules' =>  array('required', 'max_length[50]')
        ),
        array(
            'field' =>  'email',
            'label' =>  'Email',
            'rules' =>  array('required','valid_email', 'max_length[50]')
        ),
        array(
            'field' =>  'password_current',
            'label' =>  'Password'
        ),
        array(
            'field' =>  'password',
            'label' =>  'Password',
            'rules' =>  array('min_length[8]','max_length[30]')
        ),
        array(
            'field' =>  'password_confirm',
            'label' =>  'Password',
            'rules' =>  array('matches[password]'),
            'errors'    =>  array('matches' =>  'The Passwords provided are not matching.')
        ),
    );
    
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        //Setting Type Field Rules
        {
            $type_list = implode(',', array_keys(self::$type_options));
            $type_err_msg = implode(', ', self::$type_options);
            
            self::$form_rules_config[count(self::$form_rules_config)] = array(
                'field' =>  'type',
                'label' =>  'User Type',
                'rules' =>  array('required', 'in_list['.$type_list.']'),
                'errors'    =>  array('in_list' =>  'The Type field must be one of: '.$type_err_msg.'.')
            );
        }
    }
    
    public function index($sub_res = NULL, $re_type = NULL){
        $data['user_list'] = $this->user_model->get_user();
        $data['title'] = 'Users List';
        
        if(isset($this->session->res)){
            if($this->session->op == OP_CREATE || $this->session->op == OP_CREATE_BATCH){
                $f = 'submit';
                $s = $f.'ted';
            }else if($this->session->op == OP_UPDATE){
                $f = 'update';
                $s = $f.'d';
            }else if($this->session->op == OP_DELETE){
                $f = 'remove';
                $s = $f.'d';
            }
            
            $f = $s .= ' user'.($this->session->op == OP_CREATE_BATCH ? 's' : '');
            
            $succ = 'Sucessfully '.$s.'.';
            $fail = 'Failed to '.$f.', try again later.';
            $data['user_res'] = $this->session->res ? $succ : $fail ;
        }
        
        $this->load_view('users', $data, TRUE);
    }
    
    public function create(){
        $data['title'] = 'Users Create';
        
        $data['page_title'] = 'Users Create';
        $data['page_action'] = 'user/create';
        $data['admin_access_only'] = TRUE;
        
        //Array List of Member Types
        $data['type_options'] = self::$type_options;
        
        //Additional Form Rules
        {
            //Email
            self::$form_rules_config[2]['rules'][] = 'is_unique[news_users.email]';
            self::$form_rules_config[2]['errors']['is_unique'] = 'There is an account with that Email already.';
           
            //Password
            self::$form_rules_config[4]['rules'][] = 'required';
           
            //Password Confirm
            self::$form_rules_config[5]['rules'][] = 'required';
            self::$form_rules_config[5]['errors']['required'] = 'You must confirm your %s.';
        }
        
        $this->form_validation->set_rules(self::$form_rules_config);
        
        if($this->form_validation->run() == FALSE){
            $this->load_view('users/create', $data, TRUE);
        }else{
            $this->session->op = OP_CREATE;
            $this->session->res = $this->user_model->set_user() ? TRUE : FALSE;
            $this->session->mark_as_flash(array('op', 'res'));
            redirect('user');
        }
    }
    
    //TODO: Add instructions to the index page
    //TODO: Validate csv files (i.e. correct format, valid data)
    public function create_batch(){
        $data['title'] = 'Users Create (Batch)';
        
        $data['page_title'] = 'Users Create (Batch)';
        $data['page_action'] = 'user/create/batch';
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
            $this->load_view('users/create/batch', $data, TRUE);
        }else{
            //File information
            $file_info = $this->upload->data();
            
            //File Pointer
            $csv_file = fopen($file_info['full_path'], 'r');
            
            //Read file
            while(($line = fgetcsv($csv_file)) !== FALSE && !empty($line[0])){
                //TODO: Validate csv files (i.e. correct format, valid data)
                //validate_csv($line);
                $users[] = array(
                    'first_name'    =>  $line[0],
                    'last_name'     =>  $line[1],
                    'email'         =>  $line[2],
                    'type'          =>  $line[3],
                    'password'      =>  $line[4]
                );
            }
            
            //Close and delete file after finished reading
            fclose($csv_file);
            unlink($file_info['full_path']);
            
            //Add users to db
            $this->session->op = OP_CREATE_BATCH;
            $this->session->res = $this->user_model->set_user_batch($users) ? TRUE : FALSE;
            $this->session->mark_as_flash(array('op', 'res'));
            redirect('user');
        }
    }
    
    public function update($email = NULL){
        $data['title'] = 'User Update';
        
        if(!is_null($email)){
            $email = base64_decode($email);
            if(!empty($this->user_model->get_user($email))){
                foreach($this->user_model->get_user($email) as $key => $val){
                    $user_data[$key] = $val;
                }
                $data['user_data'] = (object) $user_data;
                
                $data['page_title'] = 'User Update';
                $data['page_action'] = 'user/update/'.rtrim(base64_encode($email), '=');
                
                //Array List of Meridians
                $data['type_options'] = self::$type_options;

                //Set additional form rules if password field is filled
                if(!empty($this->input->post('password'))){
                    //Password Current
                    self::$form_rules_config[3]['rules'] = array('required', 'callback_verify_pass[email,update]');
                    self::$form_rules_config[3]['errors'] = array('required'  =>  'You must provide your current %s.');
                    //Password Confirm
                    self::$form_rules_config[5]['rules'][] = 'required';
                    self::$form_rules_config[5]['errors']['required'] = 'You must confirm your %s.';
                }
                
                $this->form_validation->set_rules(self::$form_rules_config);
                
                if($this->form_validation->run() == FALSE){
                    $this->load_view('users/create', $data, TRUE);
                }else{
                    $this->session->op = OP_UPDATE;
                    $this->session->res = $this->user_model->set_user($email);
                    $this->session->mark_as_flash(array('op', 'res'));
                    redirect('user');
                }
            }else{
                redirect('user/create');
            }
        }else{
            redirect('user/create');
        }
    }
    
    public function delete($email = NULL){
        if(!is_null($email) && $this->session->user->type == ADMIN && $email !== $this->session->user->email){
            $email = base64_decode($email);
            
            //Check that the account to be removed is no the current session owner
            if($email !== $this->session->user->email){
                $this->session->op = OP_DELETE;
                $this->session->res = $this->user_model->rem_user($email);
                $this->session->mark_as_flash(array('op', 'res'));
            }
        }
        redirect('user');
    }
    
    public function verify_pass($pass, $extra){
        //Redirect user when trying to directly access method
        $this->allow_access_route($this->agent->referrer(), array('login', 'user/update'), 'user');
        
        $extra = explode(',', $extra);
        $field = $extra[0]; $method = $extra[1];
        
        $email = isset($this->form_validation->get_data()[$field], $this->form_validation->get_data()[$field]['postdata'])
            ? $this->form_validation->get_data()[$field]['postdata']
            : FALSE;
        $user = $this->user_model->get_user($email);
        $hash = isset($user) ? $user['hash'] : FALSE;

        $res = password_verify($pass, $hash);
        if($res === FALSE){
            $err = $method == 'update' ? 'You provided the incorrect current password.' : 'Incorrect Email or Password.';
            $this->form_validation->set_message('verify_pass', $err);
        }
        
        return $res;
    }
    
    public function login(){
        if(!isset($this->session->user)){
            $data['title'] = 'Login';
            
            $data['page_title'] = 'Login';
            $data['page_action'] = 'login';
            
            $login_rules_config = array(
                array(
                    'field' =>  'email',
                    'label' =>  'Email',
                    'rules' =>  array('required','valid_email')
                ),
                array(
                    'field' =>  'password',
                    'label' =>  'Password',
                    'rules' =>  array('required','callback_verify_pass[email,login]')
                )           
            );
            
            $this->form_validation->set_rules($login_rules_config); 
            
            if($this->form_validation->run() == FALSE){
                $this->load_view('users/login', $data);
            }else{
                setlocale(LC_ALL, 'en_EN');
                $user = $this->user_model->get_user($this->input->post('email'));
                $fn = $user['first_name'];
                $ln = $user['last_name'];
                
                $this->session->authenticated = TRUE;
                $this->session->user = (object) array(
                    'email' =>  $this->input->post('email'),
                    'name'  =>  ctype_alpha($ln) ? $ln.', '.substr($fn, 0, 1) : $fn,
                    'type'  =>  $user['type']
                );
                redirect('dashboard');
            }
        }else{
            redirect('dashboard');
        }
    }
    
    public function logout(){
        //NOTE: may need to change in the future if session variables are used else where other than dashboard
        if(isset($this->session->temp_files)){
            foreach($this->session->temp_files as $t){
                if(file_exists('./'.UPLOAD_TMP.$t)) unlink('./'.UPLOAD_TMP.$t);
            }
        }
        session_unset();
        session_destroy();
        redirect('');
    }
}
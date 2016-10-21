<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once './application/config/app/constants.php';

class Navigation extends CI_Controller{
    /**
     * Instance Variables
     */
    protected static $_data;
    protected static $restricted = array(
        'announcements',
        'dashboard',
        'errors',
        'includes',
        'templates',
        'users'
    );

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('America/Toronto');

        /**
         * Default load Pug HTML Template Engine
         */
        $this->load->library('pug/pug', array(
            'cache' => true,
            'prettyprint' => true
        ));

        /**
         * Exposing backend data to views
         */
        self::$_data = array(
            'baseUrl'   =>  base_url(),
            'env'       =>  (object) array(
                'dev'   =>  ENVIRONMENT == ENV_DEVELOPMENT,
                'prod'  =>  ENVIRONMENT == ENV_PRODUCTION,
                'test'  =>  ENVIRONMENT == ENV_TESTING
            ),
            'user'      =>  isset($this->session->user) ? $this->session->user : NULL,
            'year'      =>  date('Y')
        );
    }
    
    /**
     * Home page
     */
    public function index(){
        $data = self::$_data;
        $data['title'] = 'Home';
        $data['userLoggedIn'] = isset($this->session->user);

        /**
         * CI view loading
         */
        // $this->load->view('templates/header', $data);
        // $this->load->view('index');
        // $this->load->view('templates/footer');

        /**
         * Pug view loading
         */
        // $this->load->vars($data);
        // $this->pug->view('index');

        /**
         * Navigation class view loading
         */
        //self::load_view('', $data);
        redirect('login');
    }
    
    /**
     * Used mainly for redirection
     * @param  [type]  $page [description]
     * @param  boolean $auth [description]
     * @return [type]        [description]
     */
    public function load_page($page, $auth = FALSE){
        //Check that the page being accessed is not in the restricted list
        foreach (self::$restricted as $not_accessable) {
            if(stripos($page, $not_accessable) !== FALSE){
                show_404();
            }
        }

        if(isset($this->session->data)){
           $data = $this->session->data;
           unset($_SESSION['data']);
        }

        if(!isset($data['title']))
            $data['title'] = ucfirst($page);
        
        if(isset($this->session->auth_check_req)){
            $auth = $this->session->auth_check_req;
            unset($_SESSION['auth_check_req']);
        }

        self::load_view($page, $data, $auth);
    }
    
    /**
     * Used to load views
     * @param  [type]  $page [description]
     * @param  [type]  $data [description]
     * @param  boolean $auth [description]
     * @return [type]        [description]
     */
    protected function load_view($page, $data = NULL, $auth = FALSE){
        //$this->load->view('templates/header', $data);
        
        if($auth || $auth === 'TRUE'){
            $auth_val = isset($data['admin_access_only']) ? $data['admin_access_only'] : FALSE;
            self::auth_check($auth_val);
            // CI view loading
            //$this->load->view('templates/side_panel', $data);
        }
        
        // CI view loading
        /*$this->load->view('templates/container');
        $this->load->view($page.'/', $data);
        $this->load->view('templates/footer');*/

        // Pug view loading
        $this->load->vars(array_merge(self::$_data, $data));
        $this->pug->view($page.'/index');
    }

    protected function auth_check($admin_access_only = FALSE){
        if(!isset($this->session->authenticated) || !$this->session->authenticated) 
            redirect('login');

        if($admin_access_only && $this->session->user->type != ADMIN){
            $this->session->access_flag = 'You do not have permission to complete the requested operation.';
            $this->session->mark_as_flash('access_flag');

            redirect('dashboard');
        }
    }

    /**
     * Verfies the caller can access the called
     * @param  string   $caller  referrer
     * @param  mixed    $allowed referee(s)
     * @return boolean           access verification result
     */
    protected function check_access($caller = '', $allowed = ''){
        if(is_string($allowed)){
            $result = stripos($caller, $allowed) !== FALSE;
        }else if(is_array($allowed)){
            $i = 0;
            while((!isset($result) || $result === FALSE) && $i < count($allowed)){
                $result = stripos($caller, $allowed[$i++]);
            }
        }else if(is_bool($allowed)){
            $result = $class;
        }else{
            throw new Exception('Incorrect type for $allowed parameter.');
        }

        return $result;
    }

    /**
     * Verifies that the calling class and function can access the called object/class/function/method
     * @param  mixed    $class              class(s) that can access the called object/class/function/method
     * @param  mixed    $function           function(s) that can access the called object/class/function/method
     * @param  string   $path               location to be redirected to
     * @param  boolean  $manual_redirect    function redirection handling option
     * @return mixed
     */
    protected function allow_access_class($class = TRUE, $function = TRUE, $path = '', $manual_redirect = FALSE){
        $caller = debug_backtrace();

        $class_allowed = @self::check_access($caller[2]['class'], $class);
        $function_allowed = @self::check_access($caller[2]['function'], $function);

        if($class_allowed === FALSE || $function_allowed === FALSE){
            if($manual_redirect === FALSE) redirect($path);
            else return FALSE;
        }else return TRUE;
    }

    /**
     * Verifies that the referrer can access the requested content
     * @param  string   $referrer           request caller
     * @param  mixed    $referee            routes that can access the requested content
     * @param  string   $path               location to be redirected to
     * @param  boolean  $manual_redirect    function redirection handling option
     * @return mixed
     */
    protected function allow_access_route($referrer = '', $referee = '', $path = '', $manual_redirect = FALSE){
        if(self::check_access($this->agent->referrer(), $referee) === FALSE){
            if($manual_redirect === FALSE) redirect($path);
            else return FALSE;
        }else return TRUE;
    }

    //Used to redirect user to previous page they were on
    protected function redirect_to($ref = FALSE, $default = '', $routes = array()){
        if($ref != FALSE && is_array($routes) && count($routes) > 0){
            $route = substr($ref, -(strlen($ref) - strlen($this->config->item('base_url'))) + 1);
            if(in_array($route, $routes, TRUE)){
                redirect($this->agent->referrer());
            }
        }
        redirect(trim($default) == '' ? $routes[0] : $default);
    }

    protected function upload_error_check($err = UPLOAD_ERR_NO_FILE){
        //Error messages
        if($err != UPLOAD_ERR_OK && ENVIRONMENT == ENV_DEVELOPMENT){
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

        return isset($message) ? 'Upload error. '.$message : $err;
    }
    
    //Added in upload option type (automatic/manual)
    //TODO: Test that the automatic upload option works
    public function process_image_upload($img, $extra = ''){
        //Redirect user when trying to directly access method
        self::allow_access_route($this->agent->referrer(), array('announcement/create', 'announcement/update', 'settings'), 'dashboard');

        try{
            //Default values
            $field = '';
            $name = '';
            $tmp_upload = TRUE;
            $dir = '';
            $sess_var = '';

            //Passed in arguments
            if(!empty($extra)){
                $extra = explode(',', $extra);
                preg_match('/^(.+)*\[/', $extra[0], $extra[0]);
                if(array_key_exists('0', $extra)) $field = strval($extra[0][1]);
                if(array_key_exists('1', $extra)) $name = strval($extra[1]);
                if(array_key_exists('2', $extra)) $tmp_upload = boolval($extra[2]);
                if(array_key_exists('3', $extra)) $dir = strval($extra[3]);
                if(array_key_exists('4', $extra)) $sess_var = strval($extra[4]);
            }else{
                throw new Exception('Field name not provided.');
            }

            $err = array_key_exists($field, $_FILES) ? $_FILES[$field]['error']['image'] : UPLOAD_ERR_NO_FILE;
            $err = self::upload_error_check($err);

            if($err === UPLOAD_ERR_OK){
                if(strpos($_FILES[$field]['type']['image'], 'image') === 0){
                    $tmp_name = $_FILES[$field]['tmp_name']['image'];
                    $ext = pathinfo($_FILES[$field]['name']['image'], PATHINFO_EXTENSION);
                    $name = empty($name) ? basename($_FILES[$field]['name']['image']) : $name.'.'.$ext;

                    if($tmp_upload === TRUE){
                        if(move_uploaded_file($tmp_name, './'.UPLOAD_TMP.$name)){
                            //Update the temp files session variable for garbage collection later
                            $tmp = isset($this->session->temp_files) ? $this->session->temp_files : array();
                            $tmp[] = $name;
                            $this->session->temp_files = $tmp;

                            //Store in session variable for later use
                            if(trim($sess_var) == '') throw new Exception('Empty Session Variable Name for '.$field.'.');
                            $this->session->$sess_var = $name;
                        }else $upload_err = (ENVIRONMENT == ENV_DEVELOPMENT) ? 'Could not move uploaded file from '.$field.'.' : $err;
                    }else if($tmp_upload === FALSE){
                        if(move_uploaded_file($tmp_name, './'.$dir.$name)){
                            //Store in session variable for later use
                            if(trim($sess_var) == '') throw new Exception('Empty Session Variable Name for '.$field.'.');
                            $this->session->$sess_var = $name;
                        }else $upload_err = (ENVIRONMENT == ENV_DEVELOPMENT) ? 'Could not move uploaded file from '.$field.'.' : $err;
                    }
                }else $upload_err = 'Please upload a valid image file for'.$field.'.';
            }else $upload_err = $err === UPLOAD_ERR_NO_FILE ? NULL : $err;

            if(isset($upload_err)){
                $this->form_validation->set_message('process_image_upload', $upload_err);
                return FALSE;
            }else return TRUE;
        }catch(Exception $e){
            if(ENVIRONMENT != ENV_PRODUCTION) throw $e;
            log_message('debug', $e->getMessage());
        }
    }
}
?>

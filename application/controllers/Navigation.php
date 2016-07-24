<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once './application/config/app/constants.php';

class Navigation extends CI_Controller{    
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('America/Toronto');
    }
    
    public function index(){
        $data['title'] = 'Home';
        
        $this->load->view('templates/header', $data);
        $this->load->view('index');
        $this->load->view('templates/footer');
    }
    
    //Used mainly for redirection
    public function load_page($page, $auth = FALSE){
        if(isset($this->session->data)){
           $data = $this->session->data;
           unset($this->session->data);
        }
        if(!isset($data['title'])) $data['title'] = strtoupper(substr($page, 0, 1)).substr($page, 1);
        
        $this->load->view('templates/header', $data);
        if(isset($this->session->auth_check_req) || $auth){
            if($this->session->auth_check_req || $auth){
                $this->load->view('templates/auth_check', $data);
                $this->load->view('templates/side_panel', $data);
            }
        }
        $this->load->view('templates/container');
        $this->load->view($page.'/', $data);
        $this->load->view('templates/footer');
    }
    
    //Used to load views from within controller
    protected function load_view($page, $data = NULL, $auth = FALSE){
        $this->load->view('templates/header', $data);
        if($auth || $auth === 'TRUE'){
            $this->load->view('templates/auth_check', $data);
            $this->load->view('templates/side_panel', $data);
        }
        $this->load->view('templates/container');
        $this->load->view($page.'/', $data);
        $this->load->view('templates/footer');
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
            $name = '';
            $tmp_upload = TRUE;
            $dir = '';
            $sess_var = '';

            //Passed in arguments
            if(!empty($extra)){
                $extra = explode(',', $extra);
                if(array_key_exists('0', $extra)) $name = strval($extra[0]);
                if(array_key_exists('1', $extra)) $tmp_upload = boolval($extra[1]);
                if(array_key_exists('2', $extra)) $dir = strval($extra[2]);
                if(array_key_exists('3', $extra)) $sess_var = strval($extra[3]);
            }

            $err = array_key_exists('image', $_FILES) ? $_FILES['image']['error'] : UPLOAD_ERR_NO_FILE;
            $err = self::upload_error_check($err);

            if($err === UPLOAD_ERR_OK){
                if(strpos($_FILES['image']['type'], 'image') === 0){
                    $tmp_name = $_FILES['image']['tmp_name'];
                    $name = empty($name) ? basename($_FILES['image']['name']) : $name;

                    if($tmp_upload === TRUE){
                        if(move_uploaded_file($tmp_name, './'.UPLOAD_TMP.$name)){
                            //Update the temp files session variable for garbage collection later
                            $tmp = isset($this->session->temp_files) ? $this->session->temp_files : array();
                            $tmp[] = $name;
                            $this->session->temp_files = $tmp;

                            //Store in session variable for later use
                            if(trim($sess_var) == '') throw new Exception('Empty Session Variable Name');
                            $this->session->$sess_var = $name;
                        }else $upload_err = (ENVIRONMENT == ENV_DEVELOPMENT) ? 'Could not move uploaded file.' : $err;
                    }else if($tmp_upload === FALSE){
                        if(move_uploaded_file($tmp_name, './'.$dir.$name)){
                        }else $upload_err = (ENVIRONMENT == ENV_DEVELOPMENT) ? 'Could not move uploaded file.' : $err;
                    }
                }else $upload_err = 'Please upload a valid image file.';
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

    //Used to load assets from within views (scripts, stylesheets, images)
    /*public function load_asset($asset = FALSE){
        if(!empty($this->uri->segment(3))){
            $i = 2; $asset = $this->uri->segment($i++);
            do{
                $asset .= '/' . $this->uri->segment($i++);
            }while(!empty($this->uri->segment($i)));
        }

        $this->load->view($asset);
    }*/
}
?>

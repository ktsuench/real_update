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

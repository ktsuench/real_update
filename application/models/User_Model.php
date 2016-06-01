<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class User_Model extends CI_Model{
    const TABLE_NAME = 'real_update_users';
    
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    public function get_user($email = FALSE){
        $this->db->order_by('last_name ASC, first_name ASC');
        
        if($email == FALSE){
            $query = $this->db->get(self::TABLE_NAME);
            return $query->result_array();
        }
        
        $query = $this->db->get_where(self::TABLE_NAME, array('email' => $email));
        return $query->row_array();
    }
    
    public function set_user($email = FALSE){
        $data = array(
            'email'         =>  $this->input->post('email'),
            'type'          =>  $this->input->post('type'),
            'first_name'    =>  $this->input->post('first_name'),
            'last_name'     =>  $this->input->post('last_name'),
        );
        
        if($email == FALSE){
            $data['hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            
            return $this->db->insert(self::TABLE_NAME, $data);
        }else{
            if(!empty($this->input->post('password')) && !is_null($this->input->post('password')))
                $data['hash'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                
            return $this->db->update(self::TABLE_NAME, $data, array('email' => $email));
        }
    }
    
    public function set_user_batch($rows = FALSE){
        if($rows !== FALSE){
            $i = 0;
            foreach($rows as $record){
                //Check that email is not registered already
                if(empty(self::get_user($record['email']))){
                    foreach($record as $key => $val){
                        if($key == 'password')
                            $data[$i]['hash'] = password_hash($val, PASSWORD_DEFAULT);
                        else
                            $data[$i][$key] = $val;
                    }
                    $i++;
                }
            }
            
            return $this->db->insert_batch(self::TABLE_NAME, $data);
        }else{
            return 0;
        }
    }
    
    public function rem_user($email = FALSE){
        if($email != FALSE){
            return $this->db->delete(self::TABLE_NAME, array('email' => $email));
        }
        return 0;
    }
}
?>
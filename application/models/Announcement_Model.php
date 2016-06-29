<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Announcement_Model extends CI_Model{
    const TABLE_NAME = 'real_update_ann';
    
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    public function get_announcement($slug = FALSE){
        $this->db->order_by('title ASC, start_datetime ASC');
        
        if($slug == FALSE){
            $query = $this->db->get(self::TABLE_NAME);
            return $query->result_array();
        }
        
        $query = $this->db->get_where(self::TABLE_NAME, array('slug' => $slug));
        return $query->row_array();
    }
    
    public function set_announcement($slug = FALSE){
        $ann_title = url_title($this->session->ann_create['title'],'-',TRUE);
        $data = array(
            'title'             =>  $this->session->ann_create['title'],
            'content'           =>  $this->session->ann_create['content'],
            'type'              =>  $this->session->ann_create['type'],
            'author'            =>  $this->session->user->email,
            'start_datetime'    =>  $this->session->ann_create['schedule']['start']->format('Y-m-d\TH:i:s'),
            'end_datetime'      =>  $this->session->ann_create['schedule']['end']->format('Y-m-d\TH:i:s'),
            'slug'              =>  $ann_title.'-'.(new DateTime())->getTimestamp()
        );
        
        //Finalize image uploading process
        if(isset($this->session->ann_create['image'])){
            $img = $this->session->ann_create['image'];
            $ext = substr($img, strrpos($img, '.'));
            $img = $ann_title.'-'.(new DateTime())->getTimestamp().$ext;

            $data['image'] = $img;

            rename('./uploads/tmp/'.$this->session->ann_create['image'], './uploads/ann_content/'.$img);
        }else $data['image'] = NULL;

        //Remove the existing image file if there is one
        if($slug != FALSE && ($ann = self::get_announcement($slug)).image !== NULL){
            if(isset($this->session->ann_create['image']) && $this->session->ann_create['image'] != $ann['image']){
                unlink('./uploads/ann_content/'.$ann['image']);
            }else unlink('./uploads/ann_content/'.$ann['image']);
        }

        //Validate that the slug is unique
        if($slug == FALSE || strpos($slug, $data['slug']) === FALSE)
            $data['slug'] = self::unique_slug($data['slug']);
        //NOTE: need to figure out what the below code does
        /*else if(strpos($slug, $data['slug']) !== FALSE)
            $data['slug'] = $slug;*/
            
        if($slug == FALSE)
            return $this->db->insert(self::TABLE_NAME, $data);
        else
            return $this->db->update(self::TABLE_NAME, $data, array('slug' => $slug));
    }
    
    public function set_announcement_batch($rows = FALSE){
        if($rows !== FALSE){
            $i = 0;
            $taken_slug = [];
            foreach($rows as $record){
                foreach($record as $key => $val) $data[$i][$key] = $val;
                
                //Find a unique slug that isn't already in use
                $base = url_title($data[$i]['title'],'-',TRUE);
                $data[$i]['slug'] = $base.'-'.(new DateTime())->getTimestamp();
                $data[$i]['slug'] = self::unique_slug($data[$i]['slug'], $taken_slug, $base);

                //Update the used slug table
                $taken_slug[] = $data[$i]['slug'];
                
                $i++;
            }
            return $this->db->insert_batch(self::TABLE_NAME, $data);
        }else{
            return 0;
        }
    }
    
    public function rem_announcement($slug = FALSE){
        if($slug != FALSE){
            $ann = self::get_announcement($slug);
            unlink('./uploads/ann_content/'.$ann['image']);

            return $this->db->delete(self::TABLE_NAME, array('slug' => $slug));
        }
        return 0;
    }
    
    public function rem_announcement_all(){
        return $this->db->empty_table(self::TABLE_NAME);
    }
    
    //Used to prevent announcements with the same title to use the same 'unique' slug primary key
    protected function unique_slug($slug, $table = FALSE, $base = FALSE){
        if($base === FALSE) $base = $slug;
        $i = (new DateTime)->getTimestamp();
        if($table === FALSE){
            do{
                $query = $this->db->get_where(self::TABLE_NAME, array('slug' => $slug));
                $prev_slug = $slug;
                $slug = $base . '-' . $i++;
            }while(count($query->row_array()) != 0);
        }else{
            $prev_slug = $slug;
            while(in_array($slug, $table)){
                $slug = $base . '-' . $i++;
                $prev_slug = $slug;
            }
        }
        return $prev_slug;
    }
    
    public function get_announcement_display(){       
        $now = new DateTime();
        
        //Round to the nearest minute interval
        $rounded_min = (floor(intval($now->format('i')) / 15) + ((intval($now->format('i')) % 15 < 15 / 2) ? 0 : 1)) * 15;
        
        if($rounded_min == 60) $now->setTime(intval($now->format('H')) + 1, 0);
        else $now->setTime($now->format('H'), $rounded_min);
        
        $this->db->order_by('title ASC, start_datetime ASC');
        
        $where = '(start_datetime BETWEEN "1990-01-01 00:00" AND "'.$now->format('Y-m-d H:i').'")';

        //Set time to be 5 minutes ahead so that announcements end at correct times
        $now->setTime(intval($now->format('H')), intval($now->format('i')) + 5);

        $where .= ' AND (end_datetime BETWEEN "'.$now->format('Y-m-d H:i').'" AND "9999-12-31 11:59")';
        
        $query = $this->db->get_where(self::TABLE_NAME, $where);
        
        //For debugging and logging purposes
        if(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing'){
            $this->load->helper('path');
            
            $log_file = fopen(set_realpath('application/logs').'ann_disp/get_ann_'.$now->format('Y-m-d').'.log', 'a');
            
            //Set time back to curret time for logging purposes
            $now = new DateTime();

            $server = $_SERVER['REMOTE_ADDR'] === '::1' ? 'localhost' : $_SERVER['REMOTE_ADDR'];
            $content = 'Time: '.$now->format('H:i:s')."\nIP:".$server."\nAnnouncement Queue\n".print_r($query->result_array(), TRUE);

            fwrite($log_file, $content);
            
            fclose($log_file);
        }

        return $query->result_array();
    }
}
?>
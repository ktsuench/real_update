<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once './application/config/app/constants.php';

Class Announcement_Model extends CI_Model{
    const TABLE_NAME = 'real_update_ann';
    
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    protected function rem_file($path = FALSE){
        if($path != FALSE){
            if(file_exists($path)) unlink($path);
        }
    }
    
    public function get_announcement($slug = FALSE, $admin_mode = FALSE){
        $this->db->order_by('title ASC, start_datetime ASC');
        
        if($slug == FALSE){
            if($admin_mode == FALSE){
                $query = $this->db->get_where(self::TABLE_NAME, array('author' => $this->session->user->email));
            }else{
                $query = $this->db->get(self::TABLE_NAME);
            }
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
            'verified'          =>  $this->session->ann_create['verified'],
            'start_datetime'    =>  $this->session->ann_create['schedule']['start']->format('Y-m-d\TH:i:s'),
            'end_datetime'      =>  $this->session->ann_create['schedule']['end']->format('Y-m-d\TH:i:s'),
            'slug'              =>  $ann_title.'-'.(new DateTime())->getTimestamp()
        );
        
        if($slug != FALSE) $ann = self::get_announcement($slug);

        //Check if image info needs to be updated
        if($slug != FALSE && ($ann['image'] === NULL || ($ann['image'] !== NULL && $this->session->ann_create['image'] != $ann['image']))){
            $update_image = TRUE;
        }else $update_image = FALSE;

        //Check if existing image is to be removed
        if(isset($this->session->ann_create['remove_image']) && boolval($this->session->ann_create['remove_image']) == TRUE){
            $remove_image = TRUE;
        }else $remove_image = FALSE;

        //Finalize image uploading process
        if(isset($this->session->ann_create['image']) && $remove_image == FALSE){
            if($slug == FALSE || $update_image == TRUE){
                $img = $this->session->ann_create['image'];
                $ext = substr($img, strrpos($img, '.'));
                $img = $ann_title.'-'.(new DateTime())->getTimestamp().$ext;

                $data['image'] = $img;

                rename('./'.UPLOAD_TMP.$this->session->ann_create['image'], './'.UPLOAD_ANN.$img);

                //Remove image from temp file collection
                $tmp = $this->session->temp_files;
                unset($tmp[$this->session->ann_create['image']]);
                $this->session->temp_files = $tmp;
            }
        }else if($slug == FALSE || $remove_image == TRUE)
            $data['image'] = NULL; 

        //Remove the existing image file if there is one
        if($slug != FALSE && $ann['image'] !== NULL){
            if((isset($this->session->ann_create['image']) && $this->session->ann_create['image'] != $ann['image']) || $remove_image == TRUE){
                self::rem_file('./'.UPLOAD_ANN.$ann['image']);
            }
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
                
                $data[$i]['verified'] = intval($data[$i]['verified']);

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
    
    public function verify_announcement($slug = FALSE, $status = 0){
        if($slug != FALSE){
            $data = array(
                'verified' =>   $status ? 0 : 1
            );

            return $this->db->update(self::TABLE_NAME, $data, array('slug' => $slug));
        }
        return 0;
    }

    public function rem_announcement($slug = FALSE){
        if($slug != FALSE){
            $ann = self::get_announcement($slug);
            self::rem_file('./'.UPLOAD_ANN.$ann['image']);

            return $this->db->delete(self::TABLE_NAME, array('slug' => $slug));
        }
        return 0;
    }
    
    public function rem_announcement_all($author = FALSE){
        $where_img = '(image IS NOT NULL)';
        $where_del = '';

        if($author != FALSE){
            $where_del = '(author = "'.$author.'")';
            $where_img .= ' AND '.$where_del;
        }

        //Remove the images linked to announcements first
        $query = $this->db->get_where(self::TABLE_NAME, $where_img);
        foreach($query->result_array() as $row){
            self::rem_file('./'.UPLOAD_ANN.$row['image']);
        }

        //Delete the announcements
        if(empty($where_del)){
            return $this->db->empty_table(self::TABLE_NAME);
        }else{
            return $this->db->delete(self::TABLE_NAME, $where_del);
        }
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
        
        $where = '(verified = 1)';
        $where .= ' AND (start_datetime BETWEEN "1990-01-01 00:00" AND "'.$now->format('Y-m-d H:i').'")';

        //Set time to be 5 minutes ahead so that announcements end at correct times
        $now->setTime(intval($now->format('H')), intval($now->format('i')) + 5);

        $where .= ' AND (end_datetime BETWEEN "'.$now->format('Y-m-d H:i').'" AND "9999-12-31 11:59")';
        
        $query = $this->db->get_where(self::TABLE_NAME, $where);
        
        //For debugging and logging purposes
        if(ENVIRONMENT == ENV_DEVELOPMENT || ENVIRONMENT == ENV_TESTING){
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
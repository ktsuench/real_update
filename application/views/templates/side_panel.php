<?php
    $sp_link = base_url('announcement');
    $sp_title = 'View announcements list';
    echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
    
    $sp_link = base_url('announcement/create');
    $sp_title = 'Submit an announcement';
    echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
    
    if(ENVIRONMENT == 'development'){
        $sp_link = base_url('announcement/create/batch');
        $sp_title = 'Submit announcements (batch)';
        echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
    }

    $sp_link = base_url('announcement/display');
    $sp_title = 'Announcement display';
    echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'" target="_blank"').LINEBREAK;
    
    if($_SESSION['user']->type == ADMIN){
        $sp_link = base_url('user');
        $sp_title = 'View users';
        echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
        
        $sp_link = base_url('user/create');
        $sp_title = 'Add new user';
        echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
        
        $sp_link = base_url('user/create/batch');
        $sp_title = 'Add new user (batch)';
        echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
    }
    
    $sp_link = base_url('logout');
    $sp_title = 'Logout';
    echo anchor($sp_link, $sp_title, 'title="'.$sp_title.'"').LINEBREAK;
?>
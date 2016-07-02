<?php
    echo heading('Welcome to Real Update', 2);
    
    if(isset($_SESSION['access_flag'])){
        echo heading($_SESSION['access_flag'].LINEBREAK, 3);
        unset($_SESSION['access_flag']);   
    }
    
    //echo anchor(base_url(''), 'View ', 'title="View "').LINEBREAK;
    echo anchor(base_url('announcement/display'), 'View Announcements', 'title="View Announcements"').LINEBREAK;
    echo anchor(base_url('about'), 'About', 'title="About"').LINEBREAK;

    if(!isset($_SESSION['user'])){
        echo anchor(base_url('login'), 'Login', 'title="Login"').LINEBREAK;
    }else{
        echo anchor(base_url('dashboard'), 'Dashboard', 'title="Dashboard"').LINEBREAK;
        echo anchor(base_url('logout'), 'Logout', 'title="Logout"').LINEBREAK;
    }
?>
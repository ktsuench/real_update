<?php
    //TODO: Place constants in a config file
    const GUEST = 'guest';
    const ADMIN = 'admin';
    
    if(!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) redirect('login');
    if(isset($admin_access_only)){
        if($admin_access_only && $_SESSION['user']->type != ADMIN){
            $_SESSION['access_flag'] = 'You do not have permission to complete the requested operation.';
            redirect('');
        }
    }
?>
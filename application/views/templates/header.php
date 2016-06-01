<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Announcement displayment system.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

        <title><?php echo 'Real Update | '.$title; ?></title>

        <link rel="icon" type="image/png" href="<?php echo base_url(''); ?>/assets/images/favicon.png">
        <?php
            if(isset($stylesheet)){
                foreach($stylesheet as $s) echo "<link rel='stylesheet' type='text/css' href='".base_url('')."/assets/css/".$s."'>";
            }
        ?>
    </head>
    <body>
        <?php const LINEBREAK = '<br/>'; ?>
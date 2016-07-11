<h2><?php echo $title; ?></h2>

<h3><?php if(!empty($user_res)){echo $user_res;}?></h3>

<?php if(!empty($user_list)){?>
    <?php foreach ($user_list as $u){ ?>
	
        <?php
            $fn = $u['first_name'];
            $ln = $u['last_name'];
            $uname = $ln != '.' ? $ln.', '.$fn : $fn;
        ?>
        <h3><?php echo $uname; ?></h3>
        
        <div class="main">
            <?php echo 'Email: '.$u['email'];?><br/>
            <?php echo 'Type: '.($u['type'] == ADMIN ? 'Admin' : 'Guest'); ?>
        </div>

        <?php if(strcasecmp($fn, 'admin') !== 0 && strcasecmp($fn, 'guest') !== 0){ ?>
        <div>
            <span style='margin-right:10px;'>
                <a href="<?php echo base_url('user/update/'.rtrim(base64_encode($u['email']), '=')); ?>">Update user</a>
            </span>
            <?php if(strcasecmp($u['email'], $this->session->user->email) !== 0){ ?>
            <span style='margin-right:10px;'>
                <a href="<?php echo base_url('user/delete/'.rtrim(base64_encode($u['email']), '=')); ?>">Delete user</a>
            <span>
            <?php } ?>
        </div>
        <?php } ?>
    <?php } ?>
<?php }else{ ?>
    <h3>There are no users to be shown.</h3>
<?php } ?>
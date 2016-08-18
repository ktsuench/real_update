<h2><?php echo $title; ?></h2>

<?php if(!empty($announcement_res)){echo heading($announcement_res, 3);}?>

<?php
    function access_control($admin_mode = FALSE){
        if($admin_mode == FALSE){
            return '<a href="'.base_url('announcement/all').'">View All Announcements</a><br/>';
        }else{
            return '<a href="'.base_url('announcement/').'">View Own Announcements</a><br/>';
        }
    }
?>

<?php if(!empty($announcement)){?>
    <?php if($this->session->user->type == ADMIN){ ?>
        <a href="<?php echo base_url('announcement/delete/all'); ?>">Delete All</a><br/>
        <?php echo access_control($admin_mode); ?>
    <?php } ?>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Author</th>
                <th>Start Date &amp; Time</th>
                <th>End Date &amp; Time</th>
                <?php if($_SESSION['user']->type == ADMIN){ ?><th></th><?php } ?>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($announcement as $a){ ?>
                <tr <?php echo 'class="'.(boolval($a['verified']) ? 'validated' : 'invalidated').'"'; ?>>
                    <td><?php echo htmlentities($a['title'], ENT_HTML5, 'UTF-8'); ?></td>
                    <td><?php echo htmlentities($a['content'], ENT_HTML5, 'UTF-8'); ?></td>
                    <td><?php echo $a['author']; ?></td>
                    <td><?php echo $a['start_datetime']; ?></td>
                    <td><?php echo $a['end_datetime']; ?></td>
                    <?php if($_SESSION['user']->type == ADMIN){ ?>
                    <td>
                        <?php $word = $a['verified'] ? 'Invalidate' : 'Validate'; ?>
                        <a href="<?php echo base_url('announcement/verify/'.$a['slug'].'/'.$a['verified']); ?>"><?php echo $word; ?> announcement</a>
                    </td>
                    <?php } ?>
                    <td>
                        <a href="<?php echo base_url('announcement/update/'.$a['slug']); ?>">Update announcement</a>
                    </td>
                    <td>
                        <a href="<?php echo base_url('announcement/delete/'.$a['slug']); ?>">Delete announcement</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php }else{ ?>
    <?php if($this->session->user->type == ADMIN) echo access_control($admin_mode); ?>
    <?php if($admin_mode == FALSE){ ?>
        <h3>There are no announcements that you have created to be shown.</h3>
    <?php }else{ ?>
        <h3>There are no announcements in the system to be shown.</h3>
    <?php } ?>
<?php } ?>
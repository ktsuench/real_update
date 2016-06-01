<h2><?php echo $title; ?></h2>

<?php if(!empty($announcement_res)){echo heading($announcement_res, 3);}?>

<?php if(!empty($announcement)){?>
    <?php if($this->session->user->type == ADMIN){ ?>
        <a href="<?php echo base_url('announcement/delete/all'); ?>">Delete All</a>
    <?php } ?>
    <table>
        <thead>
            <tr>
              <th>Title</th>
              <th>Content</th>
              <th>Author</th>
              <th>Start Date &amp; Time</th>
              <th>End Date &amp; Time</th>
              <th></th>
              <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($announcement as $a){ ?>
            <tr>
                <td><?php echo $a['title']; ?></td>
                <td><?php echo $a['content']; ?></td>
                <td><?php echo $a['author']; ?></td>
                <td><?php echo $a['start_datetime']; ?></td>
                <td><?php echo $a['end_datetime']; ?></td>
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
    <h3>There are no announcements to be shown.</h3>
<?php } ?>
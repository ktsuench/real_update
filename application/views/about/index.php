<?php
    echo heading('About', 2);
?>

<p>This project is an improved version of <?php echo anchor('https://github.com/ktsuench/tv_announcement_system.git', 'tv_announcement_system', 'title="TV Announcement System Github Repo"'); ?>.</p>
<p>This project's code can be found at the <?php echo anchor('https://github.com/ktsuench/real_update.git', 'real_update', 'title="Real Update Github Repo"'); ?> Github Repo.</p>
<div>
    <?php echo heading('Resources this system uses:', 4); ?>
    <ul>
        <?php
            $resources = array(
                array(
                    'value' =>  'CodeIgniter PHP framework',
                    'href'  =>  'http://codeigniter.com/',
                    'title' =>  'CodeIgniter PHP framework',
                ),
                array(
                    'value' =>  'Weather Icons by Erik Flowers',
                    'href'  =>  'https://erikflowers.github.io/weather-icons/',
                    'title' =>  'Weather Icons',
                ),
                array(
                    'value' =>  'Open Weather Map Current Weather Data Api',
                    'href'  =>  'http://openweathermap.org/current',
                    'title' =>  'Open Weather Map Current Weather Data Api',
                )
            );

            foreach($resources as $r)
                echo '<li>'.anchor($r['href'], $r['value'], 'title="'.$r['title'].'"').'</li>';
        ?>
    </ul>
</div>
<?php
    $link_content = 'Home';
    echo anchor(base_url(''), $link_content, 'title="'.$link_content.'"');
?>
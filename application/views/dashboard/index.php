<?php
echo heading($title, 2);
if(isset($settings_res)) echo heading($settings_res, 3);
echo '<pre>';
print_r($this->config->item('ru_settings'));
echo '</pre>';
?>
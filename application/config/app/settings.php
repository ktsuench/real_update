<?php
defined('BASEPATH') OR exit('No direct script access allowed');

defined('BY_CITY')  OR define('BY_CITY', 10);
defined('BY_ZIP')   OR define('BY_ZIP', 11);
defined('BY_METRIC')   OR define('BY_METRIC', 'metric');
defined('BY_IMPERIAL')   OR define('BY_IMPERIAL', 'imperial');

$config_json = file_get_contents('./application/config/app/config.json', 'r');

$config['ru_settings'] = json_decode($config_json);
?>
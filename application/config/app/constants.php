<?php
defined('BASEPATH') OR exit('No direct script access allowed');

defined('ENV_DEVELOPMENT')   OR define('ENV_DEVELOPMENT' , 'development');
defined('ENV_TESTING')   OR define('ENV_TESTING' , 'testing');
defined('ENV_PRODUCTION')   OR define('ENV_PRODUCTION' , 'production');

defined('OP_CREATE')        OR define('OP_CREATE', 0);
defined('OP_UPDATE')        OR define('OP_UPDATE', 1);
defined('OP_VERIFY')        OR define('OP_VERIFY', 2);
defined('OP_DELETE')        OR define('OP_DELETE', 3);
defined('OP_CREATE_BATCH')  OR define('OP_CREATE_BATCH', 4);
defined('OP_DELETE_ALL')    OR define('OP_DELETE_ALL', 5);

defined('GUEST')            OR define('GUEST', 'guest');
defined('ADMIN')            OR define('ADMIN', 'admin');

defined('UPLOAD_TMP')       OR define('UPLOAD_TMP', 'uploads/tmp/');
defined('UPLOAD_ANN')       OR define('UPLOAD_ANN', 'uploads/ann_content/');

defined('SETTINGS_PATH')	OR define('SETTINGS_PATH', 'application/config/app/');
?>
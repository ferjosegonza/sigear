<?php

define("DEBUG", true);

define("BASE_URL", "");
define('RECURSOS_DIR', ROOT_DIR.'/resource/recursos');

define('APP_VERSION', 'v20221115');

//=============================================================
// CONEXION POSTGRESQL
//=============================================================
define('DB_HOST_POSTGRES', '');
define('DB_PORT_POSTGRES', '5432');
define('DB_CHAR_POSTGRES', 'utf8');
define('DB_NAME', '');

define('DB_USER', '');
define('DB_PASS', '');

define("DB_POSTGRES", array(
    'db' => POSTGRES,
    'db_host' => DB_HOST_POSTGRES,
    'db_port' => DB_PORT_POSTGRES,
    'db_user' => DB_USER,
    'db_pass' => DB_PASS,
    'db_name' => DB_NAME,
    'db_char' => DB_CHAR_POSTGRES,
));


?>
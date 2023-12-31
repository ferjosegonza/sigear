<?php
 
define("DEBUG", true);  

define("BASE_URL", "https://desa-gpa.sigap.com.ar");
define('RECURSOS_DIR', ROOT_DIR.'/uploads/recursos');

define('INFORMES_DIR', ROOT_DIR.'/uploads/informes');

define('APP_VERSION', 'v20221115'); 




//=============================================================
// CONEXION POSTGRESQL
//=============================================================
define('DB_HOST_POSTGRES', '200.58.123.214');
define('DB_PORT_POSTGRES', '5432');
define('DB_CHAR_POSTGRES', 'utf8');
//define('DB_NAME', 'gpa_desa');
define('DB_NAME', 'sigear_desa');

define('SCHEMA', 'sigear');
define('ENTORNO', 'desarrollo');

define('DB_USER', 'sigear_user');
define('DB_PASS', 'Us3r_S1g34R_432%');
//define('DB_PASS', 'us3r_gp4');

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
<?php
class Database extends PDO {
    private $motor;
    public function __construct($conn) {
        $this->motor = $conn['db'];

        switch ($this->motor) {
            case MYSQL:
                $dsn = 'mysql:host=' . $conn['db_host'] .';port='.$conn['db_port']. ';dbname=' . $conn['db_name'] . ';charset=' . $conn['db_char'];
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $conn['db_char']
                );
                break;

            case ORACLE:
                $tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . $conn['db_host'] . ")(PORT = " . $conn['db_port'] . ")))(CONNECT_DATA =(SERVICE_NAME = " . $conn['db_service'] . ")))";
                $dsn = "oci:dbname=" . $tns . ";charset=" . $conn['db_char'];
                $options = array();
                break;

            case SQLSERVER:
                $dsn = 'sqlsrv:Server='.$conn['db_host'].';Database=' .$conn['db_name'];
                $options = array();
                break;

            case POSTGRES:
                $dsn = 'pgsql:host='.$conn['db_host'].';dbname=' .$conn['db_name'];
                $options = array();
                break; 
        }

        parent::__construct($dsn, $conn['db_user'], $conn['db_pass'], $options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if($this->motor != SQLSERVER)
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    public function motor() {
        return $this->motor;
    }
}
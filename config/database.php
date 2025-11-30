<?php
class Database {
    private $host = "sql100.byethost18.com";
    private $db_name = "b18_40433717_casasolidaria";
    private $username = "b18_40433717";
    private $password = "CasaSolidaria2025";
    public $conn;

    public function getConnection(){
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception){
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

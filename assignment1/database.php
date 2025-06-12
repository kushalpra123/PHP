<?php
//create our database class
class database{
    private $host = '172.31.22.43';
    private $username = "Kushal200606978";
    private $password = '_V-b3wPLe1';
    private $database = 'Kushal200606978';
    //
    protected $connection;
    public function __construct(){
        if(!isset($this->connection)){
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->databse);
        }
    }

}
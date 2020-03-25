<?php

namespace AppBundle\Controller;
use \PDO;

class ConnectionPicdata
{
    public $serveur = '46.105.122.31';
    public $base = 'picdataovhd';
    public $login = 'picdataovhd';
    public $mp = 'picovh5d37';

    private $conn;

    public function __construct()
    {
        $this->conn = new PDO('mysql:host='.$this->serveur.';dbname='.$this->base.';charset=UTF8', $this->login, $this->mp, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
    }

    public function Conn()
    {
        return $this->conn;
    }
}
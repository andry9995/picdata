<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 08/05/2017
 * Time: 08:57
 */

namespace AppBundle\Functions;

/**
 * Connection simple PDO
 *
 * Class CustomPdoConnection
 * @package AppBundle\Functions
 */
class CustomPdoConnection
{
    private $host = '192.168.0.5';
//    private $host = '37.59.25.35';
    private $db = 'dbboost';
    private $user = 'dbboost';
    private $pass = 'Ricr1^42';
    private $charset = 'utf8';

    private $opt = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Créer un objet PDO
     * @return \PDO
     */
    public function connect()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $pdo = new \PDO($dsn, $this->user, $this->pass, $this->opt);

        return $pdo;
    }

    public function sirenConnect(){
        $dsn = "mysql:host=$this->host;dbname=siren;charset=$this->charset";
        $pdo = new \PDO($dsn, $this->user, $this->pass, $this->opt);

        return $pdo;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 11/01/2018
 * Time: 13:49
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;

class DeviseTauxRepository extends EntityRepository
{

    public function getTauxByDate($devise_id, $date_str)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "SELECT taux FROM devise_taux where taux!= '-' and taux!='' and devise_id =:devise ORDER BY ABS( DATEDIFF( date_devise, :date ) ) LIMIT 1";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            ':devise' => $devise_id,
            ':date' => $date_str
        ));

        return $prep->fetchAll();
    }

}
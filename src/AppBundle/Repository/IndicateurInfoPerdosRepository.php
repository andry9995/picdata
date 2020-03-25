<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/09/2019
 * Time: 11:44
 */

namespace AppBundle\Repository;


use AppBundle\Controller\DateExt;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\IndicateurInfoPerdos;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class IndicateurInfoPerdosRepository extends EntityRepository
{
    /**
     * @return IndicateurInfoPerdos[]
     */
    public function all()
    {
        return $this->createQueryBuilder('iip')
            ->orderBy('iip.header')
            ->getQuery()
            ->getResult();
    }

    public function getVal(Dossier $dossier, IndicateurInfoPerdos $indicateurInfoPerdos)
    {
        $tables = ['dossier'];

        if ($indicateurInfoPerdos->getTablesChild() && trim($indicateurInfoPerdos->getTablesChild()) != '')
        {
            $explodes = explode('#',$indicateurInfoPerdos->getTablesChild());
            foreach ($explodes as $explode) $tables[] = $explode;
        }

        $req = '' .
            'SELECT t_'.(count($tables) - 1). '.'. $indicateurInfoPerdos->getChamp() . ' AS val ' .
            'FROM dossier t_0 ';

        for ($i = 1; $i < count($tables); $i++)
        {
            $table = $tables[$i];
            $req .= 'LEFT JOIN '.$table.' t_'.$i.' ON (t_'.($i - 1).'.'.$table.'_id = t_'.$i.'.id) ';
        }

        $req .= 'WHERE t_0.id = :DOSSIER_ID LIMIT 1';

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $params = [
            'DOSSIER_ID' => $dossier->getId()
        ];

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $res = $prep->fetch();

        $val = $res->val;
        if (!$val) $val= '';
        else
        {
            //0: valeur, 1: date, 2 : age
            $type = intval($indicateurInfoPerdos->getType());
            if ($type == 1)
            {
                $date = \DateTime::createFromFormat('Y-m-d',$val);
                $val = $date->format('d/m/Y');
            }
            elseif ($type == 2)
            {
                $date = \DateTime::createFromFormat('Y-m-d',$val);
                $ages = DateExt::calculAge($date);

                $val = '';
                if (intval($ages[0]) != 0)
                    $val .= $ages[0] . 'ans ';
                if (intval($ages[1]) != 0)
                    $val .= (($val != '') ? ', ' : '') . $ages[1] . 'mois ';
                if (intval($ages[2]) != 0)
                    $val .= (($val != '') ? 'et ' : '') . $ages[2] . 'jour';
                //$val .= ' ('.$date->format('d/m/Y').') ';
            }
        }
        
        return $val;

        //return $res->val;
    }
}
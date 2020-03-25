<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 09/05/2018
 * Time: 10:47
 */

namespace One\AchatBundle\Service;


use AppBundle\Entity\OneDepenseAchat;
use Doctrine\ORM\EntityManager;

class DepenseService
{
    private $entity_manager;

    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }


    /**
     * @param $data
     * @return array
     */
    public function parseDepenseData($data) {
        $fields = [];
        $keyvalues = explode('&', $data);
        foreach ($keyvalues as $keyvalue) {
            $kv = explode('=', $keyvalue);
            $fields[$kv[0]] = urldecode($kv[1]);
            if ($kv[0] === 'price' || $kv[0] === 'remise')
                $fields[$kv[0]] = (float)$kv[1];
            if ($kv[0] === 'tva-id' || $kv[0] === 'pcc-id' || $kv[0] === 'id')
                $fields[$kv[0]] = (int)$kv[1];
        }
        return $fields;
    }

    /**
     * @param $parsedData
     * @return bool|int
     */
    public function saveDepenseAchat($parsedData) {

        //Ajout
        if ((int)$parsedData['id'] == 0 ) {
            try {
                $depenseAchat = new OneDepenseAchat();

                $achat = $this->entity_manager
                    ->getRepository('AppBundle:OneAchat')
                    ->find($parsedData['achat-id']);

                $tva = $this->entity_manager
                    ->getRepository('AppBundle:TvaTaux')
                    ->find($parsedData['tva-id']);

                $pcc = $this->entity_manager
                    ->getRepository('AppBundle:Pcc')
                    ->find($parsedData['pcc-id']);

                $depenseAchat->setAchat($achat);
                $depenseAchat->setPcc($pcc);
                $depenseAchat->setTvaTaux($tva);
                $depenseAchat->setRemise($parsedData['remise']);
                $depenseAchat->setPrix($parsedData['price']);

                $this->entity_manager->persist($depenseAchat);
                $this->entity_manager->flush();

                return $depenseAchat->getId();

            } catch (\Exception $ex) {
                return False;
            }
        }
        //Edition
        else {
            try {
                $depenseAchat = $this->entity_manager
                    ->getRepository('AppBundle:OneDepenseAchat')
                    ->find($parsedData['id']);

                $tva = $this->entity_manager
                    ->getRepository('AppBundle:TvaTaux')
                    ->find($parsedData['tva-id']);

                $pcc = $this->entity_manager
                    ->getRepository('AppBundle:Pcc')
                    ->find($parsedData['pcc-id']);

                $depenseAchat->setPcc($pcc);
                $depenseAchat->setTvaTaux($tva);
                $depenseAchat->setRemise($parsedData['remise']);
                $depenseAchat->setPrix($parsedData['price']);

                $this->entity_manager->flush();
                return $depenseAchat->getId();

            } catch (\Exception $ex) {
                return False;
            }
        }
    }

}
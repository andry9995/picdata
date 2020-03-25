<?php
    /**
     * Created by PhpStorm.
     * User: SITRAKA
     * Date: 07/02/2017
     * Time: 13:29
     */

    namespace AppBundle\Repository;

    use Doctrine\ORM\EntityRepository;

    class IndicateurGroupRepository extends EntityRepository
    {
        /**
         * @param bool $paramsGen
         * @param $client
         * @param $dossier
         * @param bool $chargerPack
         * @return array
         */
        public function getGroups($paramsGen = true,$client,$dossier,$chargerPack = true)
        {
            $paramsAreValids = false;
            $groups = array();
            $query = $this->createQueryBuilder('gr');
            if($paramsGen)
            {
                $query = $query->where('gr.client IS NULL')->andWhere('gr.dossier IS NULL');
                $paramsAreValids = true;
            }
            else
            {
                if($dossier != null)
                {
                    $query = $query->leftJoin('gr.dossier','d')
                        ->where('gr.dossier = :dossier OR gr.client = :client OR (gr.dossier IS NULL AND gr.client IS NULL)')
                        ->setParameter('dossier',$dossier)
                        ->setParameter('client',$dossier->getSite()->getClient());
                    $paramsAreValids = true;
                }
                else if($client != null)
                {
                    $query = $query->where('(gr.dossier IS NULL AND gr.client = :client) OR (gr.dossier IS NULL AND gr.client IS NULL)')->setParameter('client',$client);
                    $paramsAreValids = true;
                }
            }

            if($paramsAreValids)
            {
                $query = $query->orderBy('gr.rang')->getQuery()->getResult();
                foreach ($query as &$item)
                {
                    if($dossier != null) $item = $this->getEntityManager()->getRepository('AppBundle:IndicateurSpecGroup')->setEnabled($item,$dossier);

                    if($chargerPack)
                    {
                        $packs = $this->getEntityManager()->getRepository('AppBundle:IndicateurPack')->getPacksInGroups($item,$client,$dossier);
                        $item->setPacks($packs);
                    }
                    $groups[] = $item;
                }
            }
            return $groups;
        }

        /**
         * @param $id
         * @return mixed
         */
        public function getById($id)
        {
            return $this->createQueryBuilder('sp')
                ->where('sp.id = :id')
                ->setParameter('id',$id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        /**
         * @param $indicateurGroups
         */
        public function arrangeRang($indicateurGroups)
        {
            $em = $this->getEntityManager();
            foreach ($indicateurGroups as $key => $indicateurGroup) $indicateurGroup->setRang($key);
            $em->flush();
        }
    }
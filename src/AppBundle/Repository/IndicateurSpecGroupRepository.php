<?php
    /**
     * Created by PhpStorm.
     * User: SITRAKA
     * Date: 07/02/2017
     * Time: 13:31
     */

    namespace AppBundle\Repository;

    use AppBundle\Entity\Client;
    use AppBundle\Entity\Dossier;
    use AppBundle\Entity\IndicateurGroup;
    use AppBundle\Entity\IndicateurSpecGroup;
    use Doctrine\ORM\EntityRepository;

    /**
     * Class IndicateurSpecGroupRepository
     * @package AppBundle\Repository
     */
    class IndicateurSpecGroupRepository extends EntityRepository
    {
        /**
         * @param IndicateurGroup $indicateurGroup
         * @param Dossier $dossier
         * @return $this
         */
        public function setEnabled(IndicateurGroup $indicateurGroup,Dossier $dossier)
        {
            $enabledQuery = $this->createQueryBuilder('ig')
                ->where('ig.indicateurGroup = :indicateurGroup')
                ->setParameter('indicateurGroup',$indicateurGroup)
                ->andWhere('ig.dossier = :dossier')
                ->setParameter('dossier',$dossier);
            /*if($client != null) $enabledQuery = $enabledQuery->andWhere('ig.client = :client')->setParameter('client',$client);
            else
                $enabledQuery = $enabledQuery
                    ->andWhere('(ig.dossier = :dossier OR ig.client = :client)')
                    ->setParameter('dossier',$dossier)
                    ->setParameter('client',$dossier->getSite()->getClient());*/
            return $indicateurGroup->setEnabled($enabledQuery->getQuery()->getOneOrNullResult() != null);
        }

        /**
         * @param IndicateurGroup $indicateurGroup
         * @param Dossier $dossier
         * @param $oldStatus
         */
        public function changeEnabledTo(IndicateurGroup $indicateurGroup,Dossier $dossier,$oldStatus)
        {
            $em = $this->getEntityManager();
            $status = !$oldStatus;

            $indicateurSpecGroup = $this->createQueryBuilder('isp')
                ->where('isp.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->getQuery()
                ->getOneOrNullResult();

            if($indicateurSpecGroup != null)
            {
                $em->remove($indicateurSpecGroup);
                $em->flush();
            }

            if($status)
            {
                $indicateurSpecGroup = new IndicateurSpecGroup();
                $indicateurSpecGroup->setIndicateurGroup($indicateurGroup);
                $indicateurSpecGroup->setDossier($dossier);
                $em->persist($indicateurSpecGroup);
                $em->flush();
            }
        }

        /**
         * @param Dossier $dossier
         * @return mixed
         */
        public function getIndicateurGroup(Dossier $dossier)
        {
            $indicateurGroup = $this->createQueryBuilder('isg')
                ->where('isg.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->getQuery()
                ->getOneOrNullResult();
            return ($indicateurGroup != null) ? $indicateurGroup->getIndicateurGroup() : null;
        }

        /**
         * @param Client $client
         * @param $site
         * @param $dossier
         * @return array
         */
        public function getAllIndicateurSpecGroupDossiers(Client $client,$site,$dossier)
        {
            $result = $this->createQueryBuilder('ig')
                ->leftJoin('ig.dossier','d')
                ->leftJoin('d.site','s')
                ->leftJoin('s.client','c')
                ->where('c = :client')
                ->setParameter('client',$client);

            if ($dossier != null)
                $result = $result->andWhere('d = :dossier')->setParameter('dossier',$dossier);
            elseif ($site != null)
                $result = $result->andWhere('s = :site')->setParameter('site',$site);

            return $result->getQuery()->getResult();
        }
    }
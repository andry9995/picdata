<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 27/07/2016
 * Time: 09:08
 */

namespace AppBundle\Repository;

use AppBundle\Entity\CodeAnalytique;
use AppBundle\Entity\CodeAnalytiqueSection;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class CodeAnalytiqueRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return CodeAnalytique[]
     */
    public function getCodeAnalytique(Dossier $dossier)
    {
        return $this->createQueryBuilder('c')
            ->where('c.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('c.code')
            ->getQuery()
            ->getResult();
    }

    public function getCodeAnalytiqueGroupedObject(Dossier $dossier)
    {
        $results = [];
        $entities = $this->getCodeAnalytiqueGroupedInDossier($dossier);

        foreach ($entities as $entity)
        {
            /** @var CodeAnalytique[] $codeAnalytiques */
            $codeAnalytiques = $entity->cas;

            $cas = [];
            foreach ($codeAnalytiques as $codeAnalytique)
            {
                $cas[] = (object)
                [
                    'id' => $codeAnalytique->getId(),
                    'code' => trim($codeAnalytique->getCode()),
                    'libelle' => trim($codeAnalytique->getLibelle())
                ];
            }

            $results[] = (object)
            [
                's' => (object)
                [
                    'id' => $entity->s ? $entity->s->getId() : 0,
                    'code' => $entity->s ? trim($entity->s->getCode()) : '',
                    'libelle' => $entity->s ? trim($entity->s->getLibelle()) : ''
                ],
                'cas' => $cas
            ];
        }

        return $results;
    }

    /**
     * @param Dossier $dossier
     * @return array
     */
    public function getCodeAnalytiqueGroupedInDossier(Dossier $dossier)
    {
        $sections = $this->getEntityManager()->getRepository('AppBundle:CodeAnalytiqueSection')
            ->getAllForDossier($dossier);

        $results = [];
        foreach ($sections as $section)
        {
            $results[] = (object)
            [
                's' => $section,
                'cas' => $this->getCodeAnalytiqueInSection($dossier,$section)
            ];
        }

        $section = null;
        $results[] = (object)
        [
            's' => $section,
            'cas' => $this->getCodeAnalytiqueInSection($dossier,$section)
        ];

        return $results;
    }

    /**
     * @param CodeAnalytiqueSection|null $codeAnalytiqueSection
     * @param Dossier $dossier
     * @return array
     */
    public function getCodeAnalytiqueInSection(Dossier $dossier,CodeAnalytiqueSection $codeAnalytiqueSection = null)
    {
        if ($codeAnalytiqueSection)
            return $this->createQueryBuilder('ca')
                ->where('ca.codeAnalytiqueSection = :codeAnalytiqueSection')
                ->setParameter('codeAnalytiqueSection',$codeAnalytiqueSection)
                ->orderBy('ca.dossier')
                ->getQuery()
                ->getResult();

        return $this->createQueryBuilder('ca')
            ->where('ca.codeAnalytiqueSection IS NULL')
            ->andWhere('ca.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('ca.dossier')
            ->getQuery()
            ->getResult();
    }
}
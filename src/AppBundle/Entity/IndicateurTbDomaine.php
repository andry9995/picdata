<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTbDomaine
 *
 * @ORM\Table(name="indicateur_tb_domaine", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_indicateur_tb_domaine_libelle_affichage", columns={"nom", "affichage"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTbDomaineRepository")
 */
class IndicateurTbDomaine
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=false)
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="affichage", type="integer", nullable=false)
     */
    private $affichage = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return IndicateurTbDomaine
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set affichage
     *
     * @param integer $affichage
     *
     * @return IndicateurTbDomaine
     */
    public function setAffichage($affichage)
    {
        $this->affichage = $affichage;

        return $this;
    }

    /**
     * Get affichage
     *
     * @return integer
     */
    public function getAffichage()
    {
        return $this->affichage;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}

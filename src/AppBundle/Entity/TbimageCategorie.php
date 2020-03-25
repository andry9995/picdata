<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbimageCategorie
 *
 * @ORM\Table(name="tbimage_categorie", uniqueConstraints={@ORM\UniqueConstraint(name="dossier_id_UNIQUE", columns={"dossier_id"})}, indexes={@ORM\Index(name="fk_tbimage_categorie_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TbimageCategorieRepository")
 */
class TbimageCategorie
{
    /**
     * @var array
     *
     * @ORM\Column(name="categorie_list", type="simple_array", nullable=true)
     */
    private $categorieList;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * Un TbImageCategorie a un et un seul dossier
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Dossier", inversedBy="tbimageCategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TbimageCategorie
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }

    /**
     * Set categorieList
     *
     * @param array $categorieList
     *
     * @return TbimageCategorie
     */
    public function setCategorieList($categorieList)
    {
        $this->categorieList = $categorieList;

        return $this;
    }

    /**
     * Get categorieList
     *
     * @return array
     */
    public function getCategorieList()
    {
        sort($this->categorieList);
        return $this->categorieList;
    }

    //=====================================================================================================//
    /* Ajout manuel */

    public function __construct()
    {
        $this->categorieList = [];
    }

    /**
     * Tester si une categorie est activée pour un dossier
     *
     * @param Categorie $categorie
     * @return bool
     */
    public function isCategorieActive(Categorie $categorie)
    {
        return in_array($categorie->getId(), $this->categorieList);
    }

    /**
     * Activer ou désactiver une categorie pour un dossier
     *
     * @param Categorie $categorie
     * @param bool $active : activer ou desactiver
     * @return TbimageCategorie
     */
    public function toggleCategorie(Categorie $categorie, $active = true)
    {
        if ($active) {
            //Activer catégorie
            if (!$this->isCategorieActive($categorie)) {
                $this->categorieList[] = $categorie->getId();
            }
        } else {
            //Désactiver catégorie
            if ($this->isCategorieActive($categorie)) {
                $key = array_search($categorie->getId(), $this->categorieList);
                if ($key !== false) {
                    unset($this->categorieList[$key]);
                }
            }
        }
        $this->categorieList = array_values($this->categorieList);
        return $this;
    }
    /* Fin Ajout manuel */
    //====================================================================================================//
}

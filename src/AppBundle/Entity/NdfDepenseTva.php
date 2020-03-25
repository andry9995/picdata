<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfDepenseTva
 *
 * @ORM\Table(name="ndf_depense_tva", indexes={@ORM\Index(name="fk_ndf_depense_tva_ndf_depense1_idx", columns={"ndf_depense_id"}), @ORM\Index(name="fk_ndf_depense_tva_tva_taux_1_idx", columns={"tva_taux_id"})})
 * @ORM\Entity
 */
class NdfDepenseTva
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;

    /**
     * @var \AppBundle\Entity\NdfDepense
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfDepense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_depense_id", referencedColumnName="id")
     * })
     */
    private $ndfDepense;



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
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return NdfDepenseTva
     */
    public function setTvaTaux(\AppBundle\Entity\TvaTaux $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * Set ndfDepense
     *
     * @param \AppBundle\Entity\NdfDepense $ndfDepense
     *
     * @return NdfDepenseTva
     */
    public function setNdfDepense(\AppBundle\Entity\NdfDepense $ndfDepense = null)
    {
        $this->ndfDepense = $ndfDepense;

        return $this;
    }

    /**
     * Get ndfDepense
     *
     * @return \AppBundle\Entity\NdfDepense
     */
    public function getNdfDepense()
    {
        return $this->ndfDepense;
    }
}

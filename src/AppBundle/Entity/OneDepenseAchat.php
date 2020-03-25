<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneDepenseAchat
 *
 * @ORM\Table(name="one_depense_achat", indexes={@ORM\Index(name="fk_one_depense_achat_pcc1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_one_depense_achat_achat1_idx", columns={"achat_id"}), @ORM\Index(name="fk_one_depense_achat_tva_taux1_idx", columns={"tva_taux_id"})})
 * @ORM\Entity
 */
class OneDepenseAchat
{
    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=true)
     */
    private $prix;

    /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float", precision=10, scale=0, nullable=true)
     */
    private $remise;

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
     * @var \AppBundle\Entity\OneAchat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneAchat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="achat_id", referencedColumnName="id")
     * })
     */
    private $achat;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;



    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return OneDepenseAchat
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set remise
     *
     * @param float $remise
     *
     * @return OneDepenseAchat
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return float
     */
    public function getRemise()
    {
        return $this->remise;
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

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return OneDepenseAchat
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
     * Set achat
     *
     * @param \AppBundle\Entity\OneAchat $achat
     *
     * @return OneDepenseAchat
     */
    public function setAchat(\AppBundle\Entity\OneAchat $achat = null)
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * Get achat
     *
     * @return \AppBundle\Entity\OneAchat
     */
    public function getAchat()
    {
        return $this->achat;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return OneDepenseAchat
     */
    public function setPcc(\AppBundle\Entity\Pcc $pcc = null)
    {
        $this->pcc = $pcc;

        return $this;
    }

    /**
     * Get pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPcc()
    {
        return $this->pcc;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneArticleVente
 *
 * @ORM\Table(name="one_article_vente", indexes={@ORM\Index(name="fk_article_vente_one_article1_idx", columns={"one_article_id"}), @ORM\Index(name="fk_article_vente_tva1_idx", columns={"tva_taux_id"}), @ORM\Index(name="fk_article_vente_devis1_idx", columns={"devis_id"}), @ORM\Index(name="fk_article_vente_vente1_idx", columns={"vente_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneArticleVenteRepository")
 */
class OneArticleVente
{
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="quantite", type="float", precision=10, scale=0, nullable=false)
     */
    private $quantite = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=false)
     */
    private $prix = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float", precision=10, scale=0, nullable=true)
     */
    private $remise = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\OneDevis
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneDevis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devis_id", referencedColumnName="id")
     * })
     */
    private $devis;
    
    /**
     * @var \AppBundle\Entity\OneVente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vente_id", referencedColumnName="id")
     * })
     */
    private $vente;

    /**
     * @var \AppBundle\Entity\OneTva
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneTva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;

    /**
     * @var \AppBundle\Entity\OneArticle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneArticle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_article_id", referencedColumnName="id")
     * })
     */
    private $oneArticle;



    /**
     * Set description
     *
     * @param string $description
     *
     * @return OneArticleVente
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set quantite
     *
     * @param float $quantite
     *
     * @return OneArticleVente
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return float
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return OneArticleVente
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
     * @return OneArticleVente
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
     * Set devis
     *
     * @param \AppBundle\Entity\OneDevis $devis
     *
     * @return OneArticleVente
     */
    public function setDevis(\AppBundle\Entity\OneDevis $devis = null)
    {
        $this->devis = $devis;

        return $this;
    }

    /**
     * Get devis
     *
     * @return \AppBundle\Entity\OneDevis
     */
    public function getDevis()
    {
        return $this->devis;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\OneTva $tvaTaux
     *
     * @return OneArticleVente
     */
    public function setTvaTaux(\AppBundle\Entity\OneTva $tvaTaux = null)
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
     * Set oneArticle
     *
     * @param \AppBundle\Entity\OneArticle $oneArticle
     *
     * @return OneArticleVente
     */
    public function setOneArticle(\AppBundle\Entity\OneArticle $oneArticle = null)
    {
        $this->oneArticle = $oneArticle;

        return $this;
    }

    /**
     * Get oneArticle
     *
     * @return \AppBundle\Entity\OneArticle
     */
    public function getOneArticle()
    {
        return $this->oneArticle;
    }

    /**
     * Set vente
     *
     * @param \AppBundle\Entity\OneVente $vente
     *
     * @return OneArticleVente
     */
    public function setVente(\AppBundle\Entity\OneVente $vente = null)
    {
        $this->vente = $vente;
    
        return $this;
    }

    /**
     * Get vente
     *
     * @return \AppBundle\Entity\OneVente
     */
    public function getVente()
    {
        return $this->vente;
    }
}

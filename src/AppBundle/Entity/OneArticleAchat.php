<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneArticleAchat
 *
 * @ORM\Table(name="one_article_achat", indexes={@ORM\Index(name="fk_one_article_achat_achat1_idx", columns={"achat_id"}), @ORM\Index(name="fk_one_article_achat_article1_idx", columns={"one_article_id"}), @ORM\Index(name="fk_one_article_achat_tva_taux1_idx", columns={"tva_taux_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneArticleAchatRepository")
 */
class OneArticleAchat
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
     * @ORM\Column(name="quantite", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantite;

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
     * @var \AppBundle\Entity\OneArticle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneArticle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_article_id", referencedColumnName="id")
     * })
     */
    private $oneArticle;

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
     * Set description
     *
     * @param string $description
     *
     * @return OneArticleAchat
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
     * @return OneArticleAchat
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
     * @return OneArticleAchat
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
     * @return OneArticleAchat
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
     * Set oneArticle
     *
     * @param \AppBundle\Entity\OneArticle $oneArticle
     *
     * @return OneArticleAchat
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
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return OneArticleAchat
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
     * @return OneArticleAchat
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
}

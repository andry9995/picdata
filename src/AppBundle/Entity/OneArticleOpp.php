<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneArticleOpp
 *
 * @ORM\Table(name="one_article_opp", indexes={@ORM\Index(name="fk_article_opp_one_article1_idx", columns={"one_article_id"}), @ORM\Index(name="fk_one_article_opp_opportunite1_idx", columns={"opportunite_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneArticleOppRepository")
 */
class OneArticleOpp
{
    /**
     * @var float
     *
     * @ORM\Column(name="quantite", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantite = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=true)
     */
    private $prix = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\OneOpportunite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneOpportunite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opportunite_id", referencedColumnName="id")
     * })
     */
    private $opportunite;

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
     * Set quantite
     *
     * @param float $quantite
     *
     * @return OneArticleOpp
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
     * @return OneArticleOpp
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set opportunite
     *
     * @param \AppBundle\Entity\OneOpportunite $opportunite
     *
     * @return OneArticleOpp
     */
    public function setOpportunite(\AppBundle\Entity\OneOpportunite $opportunite = null)
    {
        $this->opportunite = $opportunite;

        return $this;
    }

    /**
     * Get opportunite
     *
     * @return \AppBundle\Entity\OneOpportunite
     */
    public function getOpportunite()
    {
        return $this->opportunite;
    }

    /**
     * Set oneArticle
     *
     * @param \AppBundle\Entity\OneArticle $oneArticle
     *
     * @return OneArticleOpp
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
}

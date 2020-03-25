<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneArticle
 *
 * @ORM\Table(name="one_article", indexes={@ORM\Index(name="fk_one_article_one_unite_article1_idx", columns={"one_unite_article_id"}), @ORM\Index(name="fk_one_article_one_famille_article1_idx", columns={"one_famille_article_id"}), @ORM\Index(name="fk_one_article_one_tva1_idx", columns={"tva_taux_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneArticleRepository")
 */
class OneArticle
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom;
    
    /**
     * @var float
     *
     * @ORM\Column(name="prix_achat", type="float", precision=10, scale=0, nullable=true)
     */
    private $prixAchat = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="prix_vente", type="float", precision=10, scale=0, nullable=true)
     */
    private $prixVente = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;
    
     /**
     *
     * @var datetime
     * 
     * @ORM\Column(name="cree_le", type="datetime", nullable=true)
     */
    private $creeLe;
    
    /**
     *
     * @var datetime
     * @ORM\Column(name="modifie_le", type="datetime", nullable=true)
     */
    private $modifieLe;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\OneFamilleArticle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneFamilleArticle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_famille_article_id", referencedColumnName="id")
     * })
     */
    private $oneFamilleArticle;

    /**
     * @var \AppBundle\Entity\OneUniteArticle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneUniteArticle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_unite_article_id", referencedColumnName="id")
     * })
     */
    private $oneUniteArticle;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;


    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @orm\JoinColumns({
            @ORM\JoinColumn(name="pcc_achat", referencedColumnName="id")
*     })
     */
    private $pccAchat;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="pcc_vente", referencedColumnName="id")
     * })
     */
    private $pccVente;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="tva_taux_achat", referencedColumnName="id")
     * })
     */
    private $tvaTauxAchat;


    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="tva_taux_vente", referencedColumnName="id")
     * })
     */
    private $tvaTauxVente;


    /**
     * Set prixAchat
     *
     * @param float $prixAchat
     *
     * @return OneArticle
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return float
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }

    /**
     * Set prixVente
     *
     * @param float $prixVente
     *
     * @return OneArticle
     */
    public function setPrixVente($prixVente)
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    /**
     * Get prixVente
     *
     * @return float
     */
    public function getPrixVente()
    {
        return $this->prixVente;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return OneArticle
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OneArticle
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set oneFamilleArticle
     *
     * @param \AppBundle\Entity\OneFamilleArticle $oneFamilleArticle
     *
     * @return OneArticle
     */
    public function setOneFamilleArticle(\AppBundle\Entity\OneFamilleArticle $oneFamilleArticle = null)
    {
        $this->oneFamilleArticle = $oneFamilleArticle;

        return $this;
    }

    /**
     * Get oneFamilleArticle
     *
     * @return \AppBundle\Entity\OneFamilleArticle
     */
    public function getOneFamilleArticle()
    {
        return $this->oneFamilleArticle;
    }

    /**
     * Set oneUniteArticle
     *
     * @param \AppBundle\Entity\OneUniteArticle $oneUniteArticle
     *
     * @return OneArticle
     */
    public function setOneUniteArticle(\AppBundle\Entity\OneUniteArticle $oneUniteArticle = null)
    {
        $this->oneUniteArticle = $oneUniteArticle;

        return $this;
    }

    /**
     * Get oneUniteArticle
     *
     * @return \AppBundle\Entity\OneUniteArticle
     */
    public function getOneUniteArticle()
    {
        return $this->oneUniteArticle;
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
     * Set nom
     *
     * @param string $nom
     *
     * @return OneArticle
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
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneArticle
     */
    public function setCreeLe($creeLe)
    {
        $this->creeLe = $creeLe;
    
        return $this;
    }

    /**
     * Get creeLe
     *
     * @return \DateTime
     */
    public function getCreeLe()
    {
        return $this->creeLe;
    }

    /**
     * Set modifieLe
     *
     * @param \DateTime $modifieLe
     *
     * @return OneArticle
     */
    public function setModifieLe($modifieLe)
    {
        $this->modifieLe = $modifieLe;
    
        return $this;
    }

    /**
     * Get modifieLe
     *
     * @return datetime
     */
    public function getModifieLe()
    {
        return $this->modifieLe;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\OneTva $tvaTaux
     *
     * @return OneArticle
     */
    public function setTvaTaux(\AppBundle\Entity\OneTva $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;
    
        return $this;
    }


    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return OneArticle
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\OneTva
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * @param Pcc $pccAchat
     * @return $this
     */
    public function setPccAchat(Pcc $pccAchat = null){
        $this->pccAchat = $pccAchat;
        return $this;
    }

    /**
     * @return Pcc
     */
    public function getPccAchat(){
        return $this->pccAchat;
    }


    /**
     * @param Pcc $pccVente
     * @return $this
     */
    public function setPccVente(Pcc $pccVente = null){
        $this->pccVente = $pccVente;
        return $this;
    }

    /**
     * @return Pcc
     */
    public function getPccVente(){
        return $this->pccVente;
    }


    /**
     * @param TvaTaux $tvaTauxVente
     * @return $this
     */
    public function setTvaTauxVente(TvaTaux $tvaTauxVente){
        $this->tvaTauxVente = $tvaTauxVente;
        return $this;
    }

    /**
     * @return TvaTaux
     */
    public function getTvaTauxVente(){
        return $this->tvaTauxVente;
    }


    /**
     * @param TvaTaux $tvaTauxAchat
     * @return $this
     */
    public function setTvaTauxAchat(TvaTaux $tvaTauxAchat){
        $this->tvaTauxAchat= $tvaTauxAchat;
        return $this;
    }

    /**
     * @return TvaTaux
     */
    public function getTvaTauxAchat(){
        return $this->tvaTauxAchat;
    }


}

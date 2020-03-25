<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTb
 *
 * @ORM\Table(name="indicateur_tb", uniqueConstraints={@ORM\UniqueConstraint(name="libelle_UNIQUE", columns={"libelle", "affichage"})}, indexes={@ORM\Index(name="fk_indicateur_tb_indicateur_tb_domaine_id1_idx", columns={"indicateur_tb_domaine_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTbRepository")
 */
class IndicateurTb
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="string", length=500, nullable=false)
     */
    private $formule = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=false)
     */
    private $rang = '99';

    /**
     * @var float
     *
     * @ORM\Column(name="ponderation", type="float", precision=10, scale=0, nullable=false)
     */
    private $ponderation = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description = '';

    /**
     * @var string
     *
     * @ORM\Column(name="norme", type="string", length=100, nullable=false)
     */
    private $norme = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="unite", type="integer", nullable=false)
     */
    private $unite = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_decimal", type="integer", nullable=false)
     */
    private $nbDecimal = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="affichage", type="integer", nullable=false)
     */
    private $affichage = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\IndicateurTbDomaine
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurTbDomaine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_tb_domaine_id", referencedColumnName="id")
     * })
     */
    private $indicateurTbDomaine;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return IndicateurTb
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return IndicateurTb
     */
    public function setFormule($formule)
    {
        $this->formule = $formule;

        return $this;
    }

    /**
     * Get formule
     *
     * @return string
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return IndicateurTb
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return IndicateurTb
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set ponderation
     *
     * @param float $ponderation
     *
     * @return IndicateurTb
     */
    public function setPonderation($ponderation)
    {
        $this->ponderation = $ponderation;

        return $this;
    }

    /**
     * Get ponderation
     *
     * @return float
     */
    public function getPonderation()
    {
        return $this->ponderation;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return IndicateurTb
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
     * Set norme
     *
     * @param string $norme
     *
     * @return IndicateurTb
     */
    public function setNorme($norme)
    {
        $this->norme = $norme;

        return $this;
    }

    /**
     * Get norme
     *
     * @return string
     */
    public function getNorme()
    {
        return $this->norme;
    }

    /**
     * Set unite
     *
     * @param integer $unite
     *
     * @return IndicateurTb
     */
    public function setUnite($unite)
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite
     *
     * @return integer
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set nbDecimal
     *
     * @param integer $nbDecimal
     *
     * @return IndicateurTb
     */
    public function setNbDecimal($nbDecimal)
    {
        $this->nbDecimal = $nbDecimal;

        return $this;
    }

    /**
     * Get nbDecimal
     *
     * @return integer
     */
    public function getNbDecimal()
    {
        return $this->nbDecimal;
    }

    /**
     * Set affichage
     *
     * @param integer $affichage
     *
     * @return IndicateurTb
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

    /**
     * Set indicateurTbDomaine
     *
     * @param \AppBundle\Entity\IndicateurTbDomaine $indicateurTbDomaine
     *
     * @return IndicateurTb
     */
    public function setIndicateurTbDomaine(\AppBundle\Entity\IndicateurTbDomaine $indicateurTbDomaine = null)
    {
        $this->indicateurTbDomaine = $indicateurTbDomaine;

        return $this;
    }

    /**
     * Get indicateurTbDomaine
     *
     * @return \AppBundle\Entity\IndicateurTbDomaine
     */
    public function getIndicateurTbDomaine()
    {
        return $this->indicateurTbDomaine;
    }
}

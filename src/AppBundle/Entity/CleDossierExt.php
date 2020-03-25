<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CleDossierExt
 *
 * @ORM\Table(name="cle_dossier_ext", indexes={@ORM\Index(name="fk_cle_dossier_ext_pcc_idx", columns={"pcc_id"}), @ORM\Index(name="fk_cle_dossier_ext_cle_dossier1_idx", columns={"cle_dossier_id"}), @ORM\Index(name="fk_cle_dossier_ext_tiers1_idx", columns={"tiers_id"}), @ORM\Index(name="fk_cle_dossier_ext_image_flague1_idx", columns={"image_flague_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CleDossierExtRepository")
 */
class CleDossierExt
{
    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="string", length=45, nullable=false)
     */
    private $formule = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="picdoc", type="integer", nullable=false)
     */
    private $picdoc = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="pcgs", type="string", length=500, nullable=false)
     */
    private $pcgs = '[]';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_compte", type="integer", nullable=false)
     */
    private $typeCompte = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="start", type="integer", nullable=false)
     */
    private $start = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="end", type="integer", nullable=false)
     */
    private $end = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="text_start", type="string", length=150, nullable=false)
     */
    private $textStart = '';

    /**
     * @var string
     *
     * @ORM\Column(name="text_end", type="string", length=150, nullable=false)
     */
    private $textEnd;

    /**
     * @var integer
     *
     * @ORM\Column(name="text_length", type="integer", nullable=false)
     */
    private $textLength = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="rechercher", type="integer", nullable=false)
     */
    private $rechercher = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="format", type="integer", nullable=false)
     */
    private $format = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;

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
     * @var \AppBundle\Entity\ImageFlague
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ImageFlague")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_flague_id", referencedColumnName="id")
     * })
     */
    private $imageFlague;

    /**
     * @var \AppBundle\Entity\CleDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CleDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_dossier_id", referencedColumnName="id")
     * })
     */
    private $cleDossier;



    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return CleDossierExt
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
     * Set picdoc
     *
     * @param integer $picdoc
     *
     * @return CleDossierExt
     */
    public function setPicdoc($picdoc)
    {
        $this->picdoc = $picdoc;

        return $this;
    }

    /**
     * Get picdoc
     *
     * @return integer
     */
    public function getPicdoc()
    {
        return $this->picdoc;
    }

    /**
     * Set pcgs
     *
     * @param string $pcgs
     *
     * @return CleDossierExt
     */
    public function setPcgs($pcgs)
    {
        $this->pcgs = $pcgs;

        return $this;
    }

    /**
     * Get pcgs
     *
     * @return string
     */
    public function getPcgs()
    {
        return $this->pcgs;
    }

    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     *
     * @return CleDossierExt
     */
    public function setTypeCompte($typeCompte)
    {
        $this->typeCompte = $typeCompte;

        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return integer
     */
    public function getTypeCompte()
    {
        return $this->typeCompte;
    }

    /**
     * Set start
     *
     * @param integer $start
     *
     * @return CleDossierExt
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param integer $end
     *
     * @return CleDossierExt
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return integer
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set textStart
     *
     * @param string $textStart
     *
     * @return CleDossierExt
     */
    public function setTextStart($textStart)
    {
        $this->textStart = $textStart;

        return $this;
    }

    /**
     * Get textStart
     *
     * @return string
     */
    public function getTextStart()
    {
        return $this->textStart;
    }

    /**
     * Set textEnd
     *
     * @param string $textEnd
     *
     * @return CleDossierExt
     */
    public function setTextEnd($textEnd)
    {
        $this->textEnd = $textEnd;

        return $this;
    }

    /**
     * Get textEnd
     *
     * @return string
     */
    public function getTextEnd()
    {
        return $this->textEnd;
    }

    /**
     * Set textLength
     *
     * @param integer $textLength
     *
     * @return CleDossierExt
     */
    public function setTextLength($textLength)
    {
        $this->textLength = $textLength;

        return $this;
    }

    /**
     * Get textLength
     *
     * @return integer
     */
    public function getTextLength()
    {
        return $this->textLength;
    }

    /**
     * Set rechercher
     *
     * @param integer $rechercher
     *
     * @return CleDossierExt
     */
    public function setRechercher($rechercher)
    {
        $this->rechercher = $rechercher;

        return $this;
    }

    /**
     * Get rechercher
     *
     * @return integer
     */
    public function getRechercher()
    {
        return $this->rechercher;
    }

    /**
     * Set format
     *
     * @param integer $format
     *
     * @return CleDossierExt
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return integer
     */
    public function getFormat()
    {
        return $this->format;
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
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return CleDossierExt
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return CleDossierExt
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

    /**
     * Set imageFlague
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague
     *
     * @return CleDossierExt
     */
    public function setImageFlague(\AppBundle\Entity\ImageFlague $imageFlague = null)
    {
        $this->imageFlague = $imageFlague;

        return $this;
    }

    /**
     * Get imageFlague
     *
     * @return \AppBundle\Entity\ImageFlague
     */
    public function getImageFlague()
    {
        return $this->imageFlague;
    }

    /**
     * Set cleDossier
     *
     * @param \AppBundle\Entity\CleDossier $cleDossier
     *
     * @return CleDossierExt
     */
    public function setCleDossier(\AppBundle\Entity\CleDossier $cleDossier = null)
    {
        $this->cleDossier = $cleDossier;

        return $this;
    }

    /**
     * Get cleDossier
     *
     * @return \AppBundle\Entity\CleDossier
     */
    public function getCleDossier()
    {
        return $this->cleDossier;
    }
}

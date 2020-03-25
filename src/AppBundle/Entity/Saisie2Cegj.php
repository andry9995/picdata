<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Saisie2Cegj
 *
 * @ORM\Table(name="saisie2_cegj", uniqueConstraints={@ORM\UniqueConstraint(name="image_id_UNIQUE", columns={"image_id"})})
 * @ORM\Entity
 */
class Saisie2Cegj
{
    /**
     * @var string
     *
     * @ORM\Column(name="entite_1", type="string", length=45, nullable=true)
     */
    private $entite1;

    /**
     * @var string
     *
     * @ORM\Column(name="entite_2", type="string", length=45, nullable=true)
     */
    private $entite2;

    /**
     * @var string
     *
     * @ORM\Column(name="entite_3", type="string", length=45, nullable=true)
     */
    private $entite3;

    /**
     * @var string
     *
     * @ORM\Column(name="entite_4", type="string", length=45, nullable=true)
     */
    private $entite4;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=true)
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="description2", type="string", length=45, nullable=true)
     */
    private $description2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_piece", type="datetime", nullable=true)
     */
    private $datePiece;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_debut", type="datetime", nullable=true)
     */
    private $periodeDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_fin", type="datetime", nullable=true)
     */
    private $periodeFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;



    /**
     * Set entite1
     *
     * @param string $entite1
     *
     * @return Saisie2Cegj
     */
    public function setEntite1($entite1)
    {
        $this->entite1 = $entite1;

        return $this;
    }

    /**
     * Get entite1
     *
     * @return string
     */
    public function getEntite1()
    {
        return $this->entite1;
    }

    /**
     * Set entite2
     *
     * @param string $entite2
     *
     * @return Saisie2Cegj
     */
    public function setEntite2($entite2)
    {
        $this->entite2 = $entite2;

        return $this;
    }

    /**
     * Get entite2
     *
     * @return string
     */
    public function getEntite2()
    {
        return $this->entite2;
    }

    /**
     * Set entite3
     *
     * @param string $entite3
     *
     * @return Saisie2Cegj
     */
    public function setEntite3($entite3)
    {
        $this->entite3 = $entite3;

        return $this;
    }

    /**
     * Get entite3
     *
     * @return string
     */
    public function getEntite3()
    {
        return $this->entite3;
    }

    /**
     * Set entite4
     *
     * @param string $entite4
     *
     * @return Saisie2Cegj
     */
    public function setEntite4($entite4)
    {
        $this->entite4 = $entite4;

        return $this;
    }

    /**
     * Get entite4
     *
     * @return string
     */
    public function getEntite4()
    {
        return $this->entite4;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Saisie2Cegj
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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Saisie2Cegj
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set description2
     *
     * @param string $description2
     *
     * @return Saisie2Cegj
     */
    public function setDescription2($description2)
    {
        $this->description2 = $description2;

        return $this;
    }

    /**
     * Get description2
     *
     * @return string
     */
    public function getDescription2()
    {
        return $this->description2;
    }

    /**
     * Set datePiece
     *
     * @param \DateTime $datePiece
     *
     * @return Saisie2Cegj
     */
    public function setDatePiece($datePiece)
    {
        $this->datePiece = $datePiece;

        return $this;
    }

    /**
     * Get datePiece
     *
     * @return \DateTime
     */
    public function getDatePiece()
    {
        return $this->datePiece;
    }

    /**
     * Set periodeDebut
     *
     * @param \DateTime $periodeDebut
     *
     * @return Saisie2Cegj
     */
    public function setPeriodeDebut($periodeDebut)
    {
        $this->periodeDebut = $periodeDebut;

        return $this;
    }

    /**
     * Get periodeDebut
     *
     * @return \DateTime
     */
    public function getPeriodeDebut()
    {
        return $this->periodeDebut;
    }

    /**
     * Set periodeFin
     *
     * @param \DateTime $periodeFin
     *
     * @return Saisie2Cegj
     */
    public function setPeriodeFin($periodeFin)
    {
        $this->periodeFin = $periodeFin;

        return $this;
    }

    /**
     * Get periodeFin
     *
     * @return \DateTime
     */
    public function getPeriodeFin()
    {
        return $this->periodeFin;
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Saisie2Cegj
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}

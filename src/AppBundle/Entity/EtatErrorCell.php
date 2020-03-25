<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatErrorCell
 *
 * @ORM\Table(name="etat_error_cell", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_error_column_col", columns={"row", "col", "etat_error_id"})}, indexes={@ORM\Index(name="fk_etat_error_column_etat_error1_idx", columns={"etat_error_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EtatErrorCellRepository")
 */
class EtatErrorCell
{
    /**
     * @var integer
     *
     * @ORM\Column(name="row", type="integer", nullable=false)
     */
    private $row;

    /**
     * @var integer
     *
     * @ORM\Column(name="col", type="integer", nullable=false)
     */
    private $col;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\EtatError
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EtatError")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_error_id", referencedColumnName="id")
     * })
     */
    private $etatError;



    /**
     * Set row
     *
     * @param integer $row
     *
     * @return EtatErrorCell
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get row
     *
     * @return integer
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set col
     *
     * @param integer $col
     *
     * @return EtatErrorCell
     */
    public function setCol($col)
    {
        $this->col = $col;

        return $this;
    }

    /**
     * Get col
     *
     * @return integer
     */
    public function getCol()
    {
        return $this->col;
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
     * Set etatError
     *
     * @param \AppBundle\Entity\EtatError $etatError
     *
     * @return EtatErrorCell
     */
    public function setEtatError(\AppBundle\Entity\EtatError $etatError = null)
    {
        $this->etatError = $etatError;

        return $this;
    }

    /**
     * Get etatError
     *
     * @return \AppBundle\Entity\EtatError
     */
    public function getEtatError()
    {
        return $this->etatError;
    }
}

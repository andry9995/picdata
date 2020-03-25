<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportHistorique
 *
 * @ORM\Table(name="import_historique", indexes={@ORM\Index(name="fk_import_historique_import_param_idx", columns={"import_param_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImportHistoriqueRepository")
 */
class ImportHistorique
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="date", nullable=true)
     */
    private $dateImport;

    /**
     * @var integer
     *
     * @ORM\Column(name="annuler", type="integer", nullable=false)
     */
    private $annuler = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ImportParam
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ImportParam")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="import_param_id", referencedColumnName="id")
     * })
     */
    private $importParam;



    /**
     * Set dateImport
     *
     * @param \DateTime $dateImport
     *
     * @return ImportHistorique
     */
    public function setDateImport($dateImport)
    {
        $this->dateImport = $dateImport;

        return $this;
    }

    /**
     * Get dateImport
     *
     * @return \DateTime
     */
    public function getDateImport()
    {
        return $this->dateImport;
    }

    /**
     * Set annuler
     *
     * @param integer $annuler
     *
     * @return ImportHistorique
     */
    public function setAnnuler($annuler)
    {
        $this->annuler = $annuler;

        return $this;
    }

    /**
     * Get annuler
     *
     * @return integer
     */
    public function getAnnuler()
    {
        return $this->annuler;
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
     * Set importParam
     *
     * @param \AppBundle\Entity\ImportParam $importParam
     *
     * @return ImportHistorique
     */
    public function setImportParam(\AppBundle\Entity\ImportParam $importParam = null)
    {
        $this->importParam = $importParam;

        return $this;
    }

    /**
     * Get importParam
     *
     * @return \AppBundle\Entity\ImportParam
     */
    public function getImportParam()
    {
        return $this->importParam;
    }
}

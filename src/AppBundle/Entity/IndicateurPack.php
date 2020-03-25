<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurPack
 *
 * @ORM\Table(name="indicateur_pack", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_indicateur_pack_libelle_group", columns={"libelle", "indicateur_group_id", "key_dupliquer"})}, indexes={@ORM\Index(name="fk_indicateur_pack_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_indicateur_pack_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_indicateur_pack_indicateur_group1_idx", columns={"indicateur_group_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurPackRepository")
 */
class IndicateurPack
{
    /***************************************************
     *              MODIF MANUEL
     **************************************************/
    /**
     * @var bool
     */
    private $enabled = true;
    /**
     * @var array
     */
    private $indicateurs = array();

    /**
     * @param $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param $indicateurs
     * @return $this
     */
    public function setIndicateurs($indicateurs)
    {
        $this->indicateurs = $indicateurs;
        return $this;
    }

    /**
     * @return array
     */
    public function getIndicateurs()
    {
        return $this->indicateurs;
    }
    /***************************************************
     *              FIN MODIF MANUEL
     **************************************************/
























    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=false)
     */
    private $rang = '10000';

    /**
     * @var string
     *
     * @ORM\Column(name="key_dupliquer", type="string", length=25, nullable=false)
     */
    private $keyDupliquer = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="valider", type="integer", nullable=false)
     */
    private $valider = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\IndicateurGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_group_id", referencedColumnName="id")
     * })
     */
    private $indicateurGroup;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return IndicateurPack
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
     * Set rang
     *
     * @param integer $rang
     *
     * @return IndicateurPack
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
     * Set keyDupliquer
     *
     * @param string $keyDupliquer
     *
     * @return IndicateurPack
     */
    public function setKeyDupliquer($keyDupliquer)
    {
        $this->keyDupliquer = $keyDupliquer;

        return $this;
    }

    /**
     * Get keyDupliquer
     *
     * @return string
     */
    public function getKeyDupliquer()
    {
        return $this->keyDupliquer;
    }

    /**
     * Set valider
     *
     * @param integer $valider
     *
     * @return IndicateurPack
     */
    public function setValider($valider)
    {
        $this->valider = $valider;

        return $this;
    }

    /**
     * Get valider
     *
     * @return integer
     */
    public function getValider()
    {
        return $this->valider;
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
     * Set indicateurGroup
     *
     * @param \AppBundle\Entity\IndicateurGroup $indicateurGroup
     *
     * @return IndicateurPack
     */
    public function setIndicateurGroup(\AppBundle\Entity\IndicateurGroup $indicateurGroup = null)
    {
        $this->indicateurGroup = $indicateurGroup;

        return $this;
    }

    /**
     * Get indicateurGroup
     *
     * @return \AppBundle\Entity\IndicateurGroup
     */
    public function getIndicateurGroup()
    {
        return $this->indicateurGroup;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return IndicateurPack
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return IndicateurPack
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}

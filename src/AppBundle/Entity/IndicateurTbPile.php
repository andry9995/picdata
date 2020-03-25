<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTbPile
 *
 * @ORM\Table(name="indicateur_tb_pile", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_indicateur_tb_pile", columns={"indicateur_tb", "historique_upload"})}, indexes={@ORM\Index(name="fk_indicateur_tb_pile_historique_upload_idx", columns={"historique_upload"}), @ORM\Index(name="IDX_CBFAADE5CC344614", columns={"indicateur_tb"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTbPileRepository")
 */
class IndicateurTbPile
{
    /**
     * @var string
     *
     * @ORM\Column(name="json", type="text", nullable=true)
     */
    private $json;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\HistoriqueUpload
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\HistoriqueUpload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="historique_upload", referencedColumnName="id")
     * })
     */
    private $historiqueUpload;

    /**
     * @var \AppBundle\Entity\IndicateurTb
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_tb", referencedColumnName="id")
     * })
     */
    private $indicateurTb;



    /**
     * Set json
     *
     * @param string $json
     *
     * @return IndicateurTbPile
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return string
     */
    public function getJson()
    {
        return $this->json;
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
     * Set historiqueUpload
     *
     * @param \AppBundle\Entity\HistoriqueUpload $historiqueUpload
     *
     * @return IndicateurTbPile
     */
    public function setHistoriqueUpload(\AppBundle\Entity\HistoriqueUpload $historiqueUpload = null)
    {
        $this->historiqueUpload = $historiqueUpload;

        return $this;
    }

    /**
     * Get historiqueUpload
     *
     * @return \AppBundle\Entity\HistoriqueUpload
     */
    public function getHistoriqueUpload()
    {
        return $this->historiqueUpload;
    }

    /**
     * Set indicateurTb
     *
     * @param \AppBundle\Entity\IndicateurTb $indicateurTb
     *
     * @return IndicateurTbPile
     */
    public function setIndicateurTb(\AppBundle\Entity\IndicateurTb $indicateurTb = null)
    {
        $this->indicateurTb = $indicateurTb;

        return $this;
    }

    /**
     * Get indicateurTb
     *
     * @return \AppBundle\Entity\IndicateurTb
     */
    public function getIndicateurTb()
    {
        return $this->indicateurTb;
    }
}

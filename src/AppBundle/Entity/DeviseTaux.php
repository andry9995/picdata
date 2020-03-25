<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeviseTaux
 *
 * @ORM\Table(name="devise_taux", indexes={@ORM\Index(name="fk_devisetaux_devis_id_idx", columns={"devise_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeviseTauxRepository")
 */
class DeviseTaux
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_devise", type="date", nullable=true)
     */
    private $dateDevise;

    /**
     * @var string
     *
     * @ORM\Column(name="taux", type="string", length=10, nullable=true)
     */
    private $taux;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Devise
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Devise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devise_id", referencedColumnName="id")
     * })
     */
    private $devise;



    /**
     * Set dateDevise
     *
     * @param \DateTime $dateDevise
     *
     * @return DeviseTaux
     */
    public function setDateDevise($dateDevise)
    {
        $this->dateDevise = $dateDevise;

        return $this;
    }

    /**
     * Get dateDevise
     *
     * @return \DateTime
     */
    public function getDateDevise()
    {
        return $this->dateDevise;
    }

    /**
     * Set taux
     *
     * @param string $taux
     *
     * @return DeviseTaux
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;

        return $this;
    }

    /**
     * Get taux
     *
     * @return string
     */
    public function getTaux()
    {
        return $this->taux;
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
     * Set devise
     *
     * @param \AppBundle\Entity\Devise $devise
     *
     * @return DeviseTaux
     */
    public function setDevise(\AppBundle\Entity\Devise $devise = null)
    {
        $this->devise = $devise;

        return $this;
    }

    /**
     * Get devise
     *
     * @return \AppBundle\Entity\Devise
     */
    public function getDevise()
    {
        return $this->devise;
    }
}

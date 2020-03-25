<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfDepenseContact
 *
 * @ORM\Table(name="ndf_depense_contact", indexes={@ORM\Index(name="ndf_depense_contact_ndf_depense1_idx", columns={"ndf_depense_id"}), @ORM\Index(name="ndf_depense_contact_ndf_contact1_idx", columns={"ndf_contact_id"})})
 * @ORM\Entity
 */
class NdfDepenseContact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfContact
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfContact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_contact_id", referencedColumnName="id")
     * })
     */
    private $ndfContact;

    /**
     * @var \AppBundle\Entity\NdfDepense
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfDepense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_depense_id", referencedColumnName="id")
     * })
     */
    private $ndfDepense;



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
     * Set ndfContact
     *
     * @param \AppBundle\Entity\NdfContact $ndfContact
     *
     * @return NdfDepenseContact
     */
    public function setNdfContact(\AppBundle\Entity\NdfContact $ndfContact = null)
    {
        $this->ndfContact = $ndfContact;

        return $this;
    }

    /**
     * Get ndfContact
     *
     * @return \AppBundle\Entity\NdfContact
     */
    public function getNdfContact()
    {
        return $this->ndfContact;
    }

    /**
     * Set ndfDepense
     *
     * @param \AppBundle\Entity\NdfDepense $ndfDepense
     *
     * @return NdfDepenseContact
     */
    public function setNdfDepense(\AppBundle\Entity\NdfDepense $ndfDepense = null)
    {
        $this->ndfDepense = $ndfDepense;

        return $this;
    }

    /**
     * Get ndfDepense
     *
     * @return \AppBundle\Entity\NdfDepense
     */
    public function getNdfDepense()
    {
        return $this->ndfDepense;
    }
}

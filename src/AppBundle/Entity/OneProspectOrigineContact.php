<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneProspectOrigineContact
 *
 * @ORM\Table(name="one_prospect_origine_contact", indexes={@ORM\Index(name="fk_one_prospect_origine_contact_client1_idx", columns={"one_client_prospect_id"})})
 * @ORM\Entity
 */
class OneProspectOrigineContact
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="societe", type="string", length=45, nullable=true)
     */
    private $societe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="premier_contact", type="date", nullable=true)
     */
    private $premierContact;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return OneProspectOrigineContact
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return OneProspectOrigineContact
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set societe
     *
     * @param string $societe
     *
     * @return OneProspectOrigineContact
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return string
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set premierContact
     *
     * @param \DateTime $premierContact
     *
     * @return OneProspectOrigineContact
     */
    public function setPremierContact($premierContact)
    {
        $this->premierContact = $premierContact;

        return $this;
    }

    /**
     * Get premierContact
     *
     * @return \DateTime
     */
    public function getPremierContact()
    {
        return $this->premierContact;
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
     * @param Tiers|null $tiers
     * @return $this
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers= null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * @return Tiers
     */
    public function getTiers()
    {
        return $this->tiers
            ;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneContactFournisseur
 *
 * @ORM\Table(name="one_contact_fournisseur", indexes={@ORM\Index(name="fk_one_contact_fournisseur_one_fournisseur1_idx", columns={"one_fournisseur_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneContactFournisseurRepository")
 */
class OneContactFournisseur
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
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\OneFournisseur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneFournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_fournisseur_id", referencedColumnName="id")
     * })
     */
    private $oneFournisseur;

    /**
     * @var Pays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pays")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="pays_id", referencedColumnName="id")})
     */
    private $pays;

    /**
     * @var string
     * @ORM\Column(name="code_postal", type="string", length=20, nullable=true)
     */
    private $codePostal;


    /**
     * @var string
     * @ORM\Column(name="ville", type="string", length=50, nullable=true)
     */
    private $ville;


    /**
     * @var string
     * @ORM\Column(name="adresse", type="string", nullable=true)
     */
    private $adresse;

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return OneContactFournisseur
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
     * @return OneContactFournisseur
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
     * Set email
     *
     * @param string $email
     *
     * @return OneContactFournisseur
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return OneContactFournisseur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
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
     * Set oneFournisseur
     *
     * @param \AppBundle\Entity\OneFournisseur $oneFournisseur
     *
     * @return OneContactFournisseur
     */
    public function setOneFournisseur(\AppBundle\Entity\OneFournisseur $oneFournisseur = null)
    {
        $this->oneFournisseur = $oneFournisseur;

        return $this;
    }

    /**
     * Get oneFournisseur
     *
     * @return \AppBundle\Entity\OneFournisseur
     */
    public function getOneFournisseur()
    {
        return $this->oneFournisseur;
    }


    /**
     * @param $ville
     * @return $this
     */
    public function setVille($ville){
        $this->ville = $ville;
        return $this;
    }

    /**
     * @return string
     */
    public function getVille(){
        return $this->ville;
    }


    /**
     * @param $codepostal
     * @return $this
     */
    public function setCodePostal($codepostal){
        $this->codePostal = $codepostal;
        return $this;
    }

    /**
     * @return string
     */

    public function getCodePostal(){
        return $this->codePostal;
    }


    /**
     * @param Pays $pays
     * @return $this
     */
    public function setPays(Pays $pays){
        $this->pays = $pays;
        return $this;
    }

    /**
     * @return Pays
     */
    public function getPays(){
        return $this->pays;
    }

    /**
     * @param $adresse
     * @return $this
     */
    public function setAdresse($adresse){
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdresse(){
        return $this->adresse;
    }


    public function getPrenomNom(){
        if($this->prenom !== null)
            return $this->prenom.' '.$this->nom;
        return $this->nom;
    }
}

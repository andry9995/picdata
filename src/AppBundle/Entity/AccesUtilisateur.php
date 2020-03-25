<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccesUtilisateur
 *
 * @ORM\Table(name="acces_utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="code_UNIQUE", columns={"code"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccesUtilisateurRepository")
 */
class AccesUtilisateur
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=false)
     */
    private $libelle = '';

    /**
     * @var integer
     * 0=a ne pas utiliser
     * 1=super admin
     * 2=scriptura
     * 3=client
     * 4=site
     * 5=dossier
     * 6=client final
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="groupe", type="string", length=50, nullable=true)
     */
    private $groupe;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set code
     *
     * @param string $code
     *
     * @return AccesUtilisateur
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set groupe
     *
     * @param string $groupe
     *
     * @return AccesUtilisateur
     */
    public function setGroup($groupe)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return string
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return AccesUtilisateur
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
     * Set type
     *
     * @param integer $type
     *
     * @return AccesUtilisateur
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
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
}
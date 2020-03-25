<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rubrique
 *
 * @ORM\Table(name="rubrique", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_rubrique_libelle_niveau", columns={"libelle", "type"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RubriqueRepository")
 */
class Rubrique
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="solde", type="integer", nullable=false)
     */
    private $solde = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_compte", type="integer", nullable=false)
     */
    private $typeCompte = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="string", length=250, nullable=false)
     */
    private $formule = '';

    /**
     * @var string
     *
     * @ORM\Column(name="rubriques_filles", type="string", length=250, nullable=false)
     */
    private $rubriquesFilles = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Rubrique
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
     * @return Rubrique
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
     * Set solde
     *
     * @param integer $solde
     *
     * @return Rubrique
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return integer
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     *
     * @return Rubrique
     */
    public function setTypeCompte($typeCompte)
    {
        $this->typeCompte = $typeCompte;

        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return integer
     */
    public function getTypeCompte()
    {
        return $this->typeCompte;
    }

    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return Rubrique
     */
    public function setFormule($formule)
    {
        $this->formule = $formule;

        return $this;
    }

    /**
     * Get formule
     *
     * @return string
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * Set rubriquesFilles
     *
     * @param string $rubriquesFilles
     *
     * @return Rubrique
     */
    public function setRubriquesFilles($rubriquesFilles)
    {
        $this->rubriquesFilles = $rubriquesFilles;

        return $this;
    }

    /**
     * Get rubriquesFilles
     *
     * @return string
     */
    public function getRubriquesFilles()
    {
        return $this->rubriquesFilles;
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

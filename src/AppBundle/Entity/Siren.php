<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Siren
 *
 * @ORM\Table(name="siren", uniqueConstraints={@ORM\UniqueConstraint(name="SIREN_UNIQUE", columns={"SIREN"})})
 * @ORM\Entity
 */
class Siren
{
    /**
     * @var string
     *
     * @ORM\Column(name="NIC", type="string", length=5, nullable=true)
     */
    private $nic;

    /**
     * @var string
     *
     * @ORM\Column(name="L1_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l1Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L2_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l2Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L3_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l3Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L4_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l4Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L5_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l5Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L6_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l6Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L7_NORMALISEE", type="string", length=38, nullable=true)
     */
    private $l7Normalisee;

    /**
     * @var string
     *
     * @ORM\Column(name="L1_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l1Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="L2_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l2Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="L3_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l3Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="L4_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l4Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="L5_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l5Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="L6_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l6Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="L7_DECLAREE", type="string", length=38, nullable=true)
     */
    private $l7Declaree;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMVOIE", type="string", length=4, nullable=true)
     */
    private $numvoie;

    /**
     * @var string
     *
     * @ORM\Column(name="INDREP", type="string", length=1, nullable=true)
     */
    private $indrep;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPVOIE", type="string", length=4, nullable=true)
     */
    private $typvoie;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBVOIE", type="string", length=32, nullable=true)
     */
    private $libvoie;

    /**
     * @var string
     *
     * @ORM\Column(name="CODPOS", type="string", length=5, nullable=true)
     */
    private $codpos;

    /**
     * @var string
     *
     * @ORM\Column(name="CEDEX", type="string", length=5, nullable=true)
     */
    private $cedex;

    /**
     * @var string
     *
     * @ORM\Column(name="RPET", type="string", length=2, nullable=true)
     */
    private $rpet;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBREG", type="string", length=70, nullable=true)
     */
    private $libreg;

    /**
     * @var string
     *
     * @ORM\Column(name="DEPET", type="string", length=2, nullable=true)
     */
    private $depet;

    /**
     * @var string
     *
     * @ORM\Column(name="ARRONET", type="string", length=2, nullable=true)
     */
    private $arronet;

    /**
     * @var string
     *
     * @ORM\Column(name="CTONET", type="string", length=3, nullable=true)
     */
    private $ctonet;

    /**
     * @var string
     *
     * @ORM\Column(name="COMET", type="string", length=3, nullable=true)
     */
    private $comet;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBCOM", type="string", length=32, nullable=true)
     */
    private $libcom;

    /**
     * @var string
     *
     * @ORM\Column(name="DU", type="string", length=2, nullable=true)
     */
    private $du;

    /**
     * @var string
     *
     * @ORM\Column(name="TU", type="string", length=1, nullable=true)
     */
    private $tu;

    /**
     * @var string
     *
     * @ORM\Column(name="UU", type="string", length=2, nullable=true)
     */
    private $uu;

    /**
     * @var string
     *
     * @ORM\Column(name="EPCI", type="string", length=9, nullable=true)
     */
    private $epci;

    /**
     * @var string
     *
     * @ORM\Column(name="TCD", type="string", length=2, nullable=true)
     */
    private $tcd;

    /**
     * @var string
     *
     * @ORM\Column(name="ZEMET", type="string", length=4, nullable=true)
     */
    private $zemet;

    /**
     * @var string
     *
     * @ORM\Column(name="SIEGE", type="string", length=1, nullable=true)
     */
    private $siege;

    /**
     * @var string
     *
     * @ORM\Column(name="ENSEIGNE", type="string", length=50, nullable=true)
     */
    private $enseigne;

    /**
     * @var string
     *
     * @ORM\Column(name="IND_PUBLIPO", type="string", length=1, nullable=true)
     */
    private $indPublipo;

    /**
     * @var string
     *
     * @ORM\Column(name="DIFFCOM", type="string", length=1, nullable=true)
     */
    private $diffcom;

    /**
     * @var string
     *
     * @ORM\Column(name="AMINTRET", type="string", length=6, nullable=true)
     */
    private $amintret;

    /**
     * @var string
     *
     * @ORM\Column(name="NATETAB", type="string", length=1, nullable=true)
     */
    private $natetab;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBNATETAB", type="string", length=30, nullable=true)
     */
    private $libnatetab;

    /**
     * @var string
     *
     * @ORM\Column(name="APET700", type="string", length=5, nullable=true)
     */
    private $apet700;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBAPET", type="string", length=65, nullable=true)
     */
    private $libapet;

    /**
     * @var string
     *
     * @ORM\Column(name="DAPET", type="string", length=4, nullable=true)
     */
    private $dapet;

    /**
     * @var string
     *
     * @ORM\Column(name="TEFET", type="string", length=2, nullable=true)
     */
    private $tefet;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBTEFET", type="string", length=23, nullable=true)
     */
    private $libtefet;

    /**
     * @var string
     *
     * @ORM\Column(name="EFETCENT", type="string", length=6, nullable=true)
     */
    private $efetcent;

    /**
     * @var string
     *
     * @ORM\Column(name="DEFET", type="string", length=4, nullable=true)
     */
    private $defet;

    /**
     * @var string
     *
     * @ORM\Column(name="ORIGINE", type="string", length=2, nullable=true)
     */
    private $origine;

    /**
     * @var string
     *
     * @ORM\Column(name="DCRET", type="string", length=8, nullable=true)
     */
    private $dcret;

    /**
     * @var string
     *
     * @ORM\Column(name="DDEBACT", type="string", length=8, nullable=true)
     */
    private $ddebact;

    /**
     * @var string
     *
     * @ORM\Column(name="ACTIVNAT", type="string", length=2, nullable=true)
     */
    private $activnat;

    /**
     * @var string
     *
     * @ORM\Column(name="LIEUACT", type="string", length=2, nullable=true)
     */
    private $lieuact;

    /**
     * @var string
     *
     * @ORM\Column(name="ACTISURF", type="string", length=2, nullable=true)
     */
    private $actisurf;

    /**
     * @var string
     *
     * @ORM\Column(name="SAISONAT", type="string", length=2, nullable=true)
     */
    private $saisonat;

    /**
     * @var string
     *
     * @ORM\Column(name="MODET", type="string", length=1, nullable=true)
     */
    private $modet;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODET", type="string", length=1, nullable=true)
     */
    private $prodet;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODPART", type="string", length=1, nullable=true)
     */
    private $prodpart;

    /**
     * @var string
     *
     * @ORM\Column(name="AUXILT", type="string", length=1, nullable=true)
     */
    private $auxilt;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMEN_LONG", type="string", length=131, nullable=true)
     */
    private $nomenLong;

    /**
     * @var string
     *
     * @ORM\Column(name="SIGLE", type="string", length=20, nullable=true)
     */
    private $sigle;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM", type="string", length=100, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="PRENOM", type="string", length=30, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="CIVILITE", type="string", length=1, nullable=true)
     */
    private $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="RNA", type="string", length=10, nullable=true)
     */
    private $rna;

    /**
     * @var string
     *
     * @ORM\Column(name="NICSIEGE", type="string", length=5, nullable=true)
     */
    private $nicsiege;

    /**
     * @var string
     *
     * @ORM\Column(name="RPEN", type="string", length=2, nullable=true)
     */
    private $rpen;

    /**
     * @var string
     *
     * @ORM\Column(name="DEPCOMEN", type="string", length=5, nullable=true)
     */
    private $depcomen;

    /**
     * @var string
     *
     * @ORM\Column(name="ADR_MAIL", type="string", length=80, nullable=true)
     */
    private $adrMail;

    /**
     * @var string
     *
     * @ORM\Column(name="NJ", type="string", length=4, nullable=true)
     */
    private $nj;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBNJ", type="string", length=100, nullable=true)
     */
    private $libnj;

    /**
     * @var string
     *
     * @ORM\Column(name="APEN700", type="string", length=5, nullable=true)
     */
    private $apen700;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBAPEN", type="string", length=65, nullable=true)
     */
    private $libapen;

    /**
     * @var string
     *
     * @ORM\Column(name="DAPEN", type="string", length=4, nullable=true)
     */
    private $dapen;

    /**
     * @var string
     *
     * @ORM\Column(name="APRM", type="string", length=6, nullable=true)
     */
    private $aprm;

    /**
     * @var string
     *
     * @ORM\Column(name="ESS", type="string", length=1, nullable=true)
     */
    private $ess;

    /**
     * @var string
     *
     * @ORM\Column(name="DATEESS", type="string", length=8, nullable=true)
     */
    private $dateess;

    /**
     * @var string
     *
     * @ORM\Column(name="TEFEN", type="string", length=2, nullable=true)
     */
    private $tefen;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBTEFEN", type="string", length=23, nullable=true)
     */
    private $libtefen;

    /**
     * @var string
     *
     * @ORM\Column(name="EFENCENT", type="string", length=6, nullable=true)
     */
    private $efencent;

    /**
     * @var string
     *
     * @ORM\Column(name="DEFEN", type="string", length=4, nullable=true)
     */
    private $defen;

    /**
     * @var string
     *
     * @ORM\Column(name="CATEGORIE", type="string", length=5, nullable=true)
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="DCREN", type="string", length=8, nullable=true)
     */
    private $dcren;

    /**
     * @var string
     *
     * @ORM\Column(name="AMINTREN", type="string", length=6, nullable=true)
     */
    private $amintren;

    /**
     * @var string
     *
     * @ORM\Column(name="MONOACT", type="string", length=1, nullable=true)
     */
    private $monoact;

    /**
     * @var string
     *
     * @ORM\Column(name="MODEN", type="string", length=1, nullable=true)
     */
    private $moden;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODEN", type="string", length=1, nullable=true)
     */
    private $proden;

    /**
     * @var string
     *
     * @ORM\Column(name="ESAANN", type="string", length=4, nullable=true)
     */
    private $esaann;

    /**
     * @var string
     *
     * @ORM\Column(name="TCA", type="string", length=1, nullable=true)
     */
    private $tca;

    /**
     * @var string
     *
     * @ORM\Column(name="ESAAPEN", type="string", length=5, nullable=true)
     */
    private $esaapen;

    /**
     * @var string
     *
     * @ORM\Column(name="ESASEC1N", type="string", length=5, nullable=true)
     */
    private $esasec1n;

    /**
     * @var string
     *
     * @ORM\Column(name="ESASEC2N", type="string", length=5, nullable=true)
     */
    private $esasec2n;

    /**
     * @var string
     *
     * @ORM\Column(name="ESASEC3N", type="string", length=5, nullable=true)
     */
    private $esasec3n;

    /**
     * @var string
     *
     * @ORM\Column(name="ESASEC4N", type="string", length=5, nullable=true)
     */
    private $esasec4n;

    /**
     * @var string
     *
     * @ORM\Column(name="VMAJ", type="string", length=1, nullable=true)
     */
    private $vmaj;

    /**
     * @var string
     *
     * @ORM\Column(name="VMAJ1", type="string", length=1, nullable=true)
     */
    private $vmaj1;

    /**
     * @var string
     *
     * @ORM\Column(name="VMAJ2", type="string", length=1, nullable=true)
     */
    private $vmaj2;

    /**
     * @var string
     *
     * @ORM\Column(name="VMAJ3", type="string", length=1, nullable=true)
     */
    private $vmaj3;

    /**
     * @var string
     *
     * @ORM\Column(name="SIREN", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $siren;



    /**
     * Set nic
     *
     * @param string $nic
     *
     * @return Siren
     */
    public function setNic($nic)
    {
        $this->nic = $nic;

        return $this;
    }

    /**
     * Get nic
     *
     * @return string
     */
    public function getNic()
    {
        return $this->nic;
    }

    /**
     * Set l1Normalisee
     *
     * @param string $l1Normalisee
     *
     * @return Siren
     */
    public function setL1Normalisee($l1Normalisee)
    {
        $this->l1Normalisee = $l1Normalisee;

        return $this;
    }

    /**
     * Get l1Normalisee
     *
     * @return string
     */
    public function getL1Normalisee()
    {
        return $this->l1Normalisee;
    }

    /**
     * Set l2Normalisee
     *
     * @param string $l2Normalisee
     *
     * @return Siren
     */
    public function setL2Normalisee($l2Normalisee)
    {
        $this->l2Normalisee = $l2Normalisee;

        return $this;
    }

    /**
     * Get l2Normalisee
     *
     * @return string
     */
    public function getL2Normalisee()
    {
        return $this->l2Normalisee;
    }

    /**
     * Set l3Normalisee
     *
     * @param string $l3Normalisee
     *
     * @return Siren
     */
    public function setL3Normalisee($l3Normalisee)
    {
        $this->l3Normalisee = $l3Normalisee;

        return $this;
    }

    /**
     * Get l3Normalisee
     *
     * @return string
     */
    public function getL3Normalisee()
    {
        return $this->l3Normalisee;
    }

    /**
     * Set l4Normalisee
     *
     * @param string $l4Normalisee
     *
     * @return Siren
     */
    public function setL4Normalisee($l4Normalisee)
    {
        $this->l4Normalisee = $l4Normalisee;

        return $this;
    }

    /**
     * Get l4Normalisee
     *
     * @return string
     */
    public function getL4Normalisee()
    {
        return $this->l4Normalisee;
    }

    /**
     * Set l5Normalisee
     *
     * @param string $l5Normalisee
     *
     * @return Siren
     */
    public function setL5Normalisee($l5Normalisee)
    {
        $this->l5Normalisee = $l5Normalisee;

        return $this;
    }

    /**
     * Get l5Normalisee
     *
     * @return string
     */
    public function getL5Normalisee()
    {
        return $this->l5Normalisee;
    }

    /**
     * Set l6Normalisee
     *
     * @param string $l6Normalisee
     *
     * @return Siren
     */
    public function setL6Normalisee($l6Normalisee)
    {
        $this->l6Normalisee = $l6Normalisee;

        return $this;
    }

    /**
     * Get l6Normalisee
     *
     * @return string
     */
    public function getL6Normalisee()
    {
        return $this->l6Normalisee;
    }

    /**
     * Set l7Normalisee
     *
     * @param string $l7Normalisee
     *
     * @return Siren
     */
    public function setL7Normalisee($l7Normalisee)
    {
        $this->l7Normalisee = $l7Normalisee;

        return $this;
    }

    /**
     * Get l7Normalisee
     *
     * @return string
     */
    public function getL7Normalisee()
    {
        return $this->l7Normalisee;
    }

    /**
     * Set l1Declaree
     *
     * @param string $l1Declaree
     *
     * @return Siren
     */
    public function setL1Declaree($l1Declaree)
    {
        $this->l1Declaree = $l1Declaree;

        return $this;
    }

    /**
     * Get l1Declaree
     *
     * @return string
     */
    public function getL1Declaree()
    {
        return $this->l1Declaree;
    }

    /**
     * Set l2Declaree
     *
     * @param string $l2Declaree
     *
     * @return Siren
     */
    public function setL2Declaree($l2Declaree)
    {
        $this->l2Declaree = $l2Declaree;

        return $this;
    }

    /**
     * Get l2Declaree
     *
     * @return string
     */
    public function getL2Declaree()
    {
        return $this->l2Declaree;
    }

    /**
     * Set l3Declaree
     *
     * @param string $l3Declaree
     *
     * @return Siren
     */
    public function setL3Declaree($l3Declaree)
    {
        $this->l3Declaree = $l3Declaree;

        return $this;
    }

    /**
     * Get l3Declaree
     *
     * @return string
     */
    public function getL3Declaree()
    {
        return $this->l3Declaree;
    }

    /**
     * Set l4Declaree
     *
     * @param string $l4Declaree
     *
     * @return Siren
     */
    public function setL4Declaree($l4Declaree)
    {
        $this->l4Declaree = $l4Declaree;

        return $this;
    }

    /**
     * Get l4Declaree
     *
     * @return string
     */
    public function getL4Declaree()
    {
        return $this->l4Declaree;
    }

    /**
     * Set l5Declaree
     *
     * @param string $l5Declaree
     *
     * @return Siren
     */
    public function setL5Declaree($l5Declaree)
    {
        $this->l5Declaree = $l5Declaree;

        return $this;
    }

    /**
     * Get l5Declaree
     *
     * @return string
     */
    public function getL5Declaree()
    {
        return $this->l5Declaree;
    }

    /**
     * Set l6Declaree
     *
     * @param string $l6Declaree
     *
     * @return Siren
     */
    public function setL6Declaree($l6Declaree)
    {
        $this->l6Declaree = $l6Declaree;

        return $this;
    }

    /**
     * Get l6Declaree
     *
     * @return string
     */
    public function getL6Declaree()
    {
        return $this->l6Declaree;
    }

    /**
     * Set l7Declaree
     *
     * @param string $l7Declaree
     *
     * @return Siren
     */
    public function setL7Declaree($l7Declaree)
    {
        $this->l7Declaree = $l7Declaree;

        return $this;
    }

    /**
     * Get l7Declaree
     *
     * @return string
     */
    public function getL7Declaree()
    {
        return $this->l7Declaree;
    }

    /**
     * Set numvoie
     *
     * @param string $numvoie
     *
     * @return Siren
     */
    public function setNumvoie($numvoie)
    {
        $this->numvoie = $numvoie;

        return $this;
    }

    /**
     * Get numvoie
     *
     * @return string
     */
    public function getNumvoie()
    {
        return $this->numvoie;
    }

    /**
     * Set indrep
     *
     * @param string $indrep
     *
     * @return Siren
     */
    public function setIndrep($indrep)
    {
        $this->indrep = $indrep;

        return $this;
    }

    /**
     * Get indrep
     *
     * @return string
     */
    public function getIndrep()
    {
        return $this->indrep;
    }

    /**
     * Set typvoie
     *
     * @param string $typvoie
     *
     * @return Siren
     */
    public function setTypvoie($typvoie)
    {
        $this->typvoie = $typvoie;

        return $this;
    }

    /**
     * Get typvoie
     *
     * @return string
     */
    public function getTypvoie()
    {
        return $this->typvoie;
    }

    /**
     * Set libvoie
     *
     * @param string $libvoie
     *
     * @return Siren
     */
    public function setLibvoie($libvoie)
    {
        $this->libvoie = $libvoie;

        return $this;
    }

    /**
     * Get libvoie
     *
     * @return string
     */
    public function getLibvoie()
    {
        return $this->libvoie;
    }

    /**
     * Set codpos
     *
     * @param string $codpos
     *
     * @return Siren
     */
    public function setCodpos($codpos)
    {
        $this->codpos = $codpos;

        return $this;
    }

    /**
     * Get codpos
     *
     * @return string
     */
    public function getCodpos()
    {
        return $this->codpos;
    }

    /**
     * Set cedex
     *
     * @param string $cedex
     *
     * @return Siren
     */
    public function setCedex($cedex)
    {
        $this->cedex = $cedex;

        return $this;
    }

    /**
     * Get cedex
     *
     * @return string
     */
    public function getCedex()
    {
        return $this->cedex;
    }

    /**
     * Set rpet
     *
     * @param string $rpet
     *
     * @return Siren
     */
    public function setRpet($rpet)
    {
        $this->rpet = $rpet;

        return $this;
    }

    /**
     * Get rpet
     *
     * @return string
     */
    public function getRpet()
    {
        return $this->rpet;
    }

    /**
     * Set libreg
     *
     * @param string $libreg
     *
     * @return Siren
     */
    public function setLibreg($libreg)
    {
        $this->libreg = $libreg;

        return $this;
    }

    /**
     * Get libreg
     *
     * @return string
     */
    public function getLibreg()
    {
        return $this->libreg;
    }

    /**
     * Set depet
     *
     * @param string $depet
     *
     * @return Siren
     */
    public function setDepet($depet)
    {
        $this->depet = $depet;

        return $this;
    }

    /**
     * Get depet
     *
     * @return string
     */
    public function getDepet()
    {
        return $this->depet;
    }

    /**
     * Set arronet
     *
     * @param string $arronet
     *
     * @return Siren
     */
    public function setArronet($arronet)
    {
        $this->arronet = $arronet;

        return $this;
    }

    /**
     * Get arronet
     *
     * @return string
     */
    public function getArronet()
    {
        return $this->arronet;
    }

    /**
     * Set ctonet
     *
     * @param string $ctonet
     *
     * @return Siren
     */
    public function setCtonet($ctonet)
    {
        $this->ctonet = $ctonet;

        return $this;
    }

    /**
     * Get ctonet
     *
     * @return string
     */
    public function getCtonet()
    {
        return $this->ctonet;
    }

    /**
     * Set comet
     *
     * @param string $comet
     *
     * @return Siren
     */
    public function setComet($comet)
    {
        $this->comet = $comet;

        return $this;
    }

    /**
     * Get comet
     *
     * @return string
     */
    public function getComet()
    {
        return $this->comet;
    }

    /**
     * Set libcom
     *
     * @param string $libcom
     *
     * @return Siren
     */
    public function setLibcom($libcom)
    {
        $this->libcom = $libcom;

        return $this;
    }

    /**
     * Get libcom
     *
     * @return string
     */
    public function getLibcom()
    {
        return $this->libcom;
    }

    /**
     * Set du
     *
     * @param string $du
     *
     * @return Siren
     */
    public function setDu($du)
    {
        $this->du = $du;

        return $this;
    }

    /**
     * Get du
     *
     * @return string
     */
    public function getDu()
    {
        return $this->du;
    }

    /**
     * Set tu
     *
     * @param string $tu
     *
     * @return Siren
     */
    public function setTu($tu)
    {
        $this->tu = $tu;

        return $this;
    }

    /**
     * Get tu
     *
     * @return string
     */
    public function getTu()
    {
        return $this->tu;
    }

    /**
     * Set uu
     *
     * @param string $uu
     *
     * @return Siren
     */
    public function setUu($uu)
    {
        $this->uu = $uu;

        return $this;
    }

    /**
     * Get uu
     *
     * @return string
     */
    public function getUu()
    {
        return $this->uu;
    }

    /**
     * Set epci
     *
     * @param string $epci
     *
     * @return Siren
     */
    public function setEpci($epci)
    {
        $this->epci = $epci;

        return $this;
    }

    /**
     * Get epci
     *
     * @return string
     */
    public function getEpci()
    {
        return $this->epci;
    }

    /**
     * Set tcd
     *
     * @param string $tcd
     *
     * @return Siren
     */
    public function setTcd($tcd)
    {
        $this->tcd = $tcd;

        return $this;
    }

    /**
     * Get tcd
     *
     * @return string
     */
    public function getTcd()
    {
        return $this->tcd;
    }

    /**
     * Set zemet
     *
     * @param string $zemet
     *
     * @return Siren
     */
    public function setZemet($zemet)
    {
        $this->zemet = $zemet;

        return $this;
    }

    /**
     * Get zemet
     *
     * @return string
     */
    public function getZemet()
    {
        return $this->zemet;
    }

    /**
     * Set siege
     *
     * @param string $siege
     *
     * @return Siren
     */
    public function setSiege($siege)
    {
        $this->siege = $siege;

        return $this;
    }

    /**
     * Get siege
     *
     * @return string
     */
    public function getSiege()
    {
        return $this->siege;
    }

    /**
     * Set enseigne
     *
     * @param string $enseigne
     *
     * @return Siren
     */
    public function setEnseigne($enseigne)
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    /**
     * Get enseigne
     *
     * @return string
     */
    public function getEnseigne()
    {
        return $this->enseigne;
    }

    /**
     * Set indPublipo
     *
     * @param string $indPublipo
     *
     * @return Siren
     */
    public function setIndPublipo($indPublipo)
    {
        $this->indPublipo = $indPublipo;

        return $this;
    }

    /**
     * Get indPublipo
     *
     * @return string
     */
    public function getIndPublipo()
    {
        return $this->indPublipo;
    }

    /**
     * Set diffcom
     *
     * @param string $diffcom
     *
     * @return Siren
     */
    public function setDiffcom($diffcom)
    {
        $this->diffcom = $diffcom;

        return $this;
    }

    /**
     * Get diffcom
     *
     * @return string
     */
    public function getDiffcom()
    {
        return $this->diffcom;
    }

    /**
     * Set amintret
     *
     * @param string $amintret
     *
     * @return Siren
     */
    public function setAmintret($amintret)
    {
        $this->amintret = $amintret;

        return $this;
    }

    /**
     * Get amintret
     *
     * @return string
     */
    public function getAmintret()
    {
        return $this->amintret;
    }

    /**
     * Set natetab
     *
     * @param string $natetab
     *
     * @return Siren
     */
    public function setNatetab($natetab)
    {
        $this->natetab = $natetab;

        return $this;
    }

    /**
     * Get natetab
     *
     * @return string
     */
    public function getNatetab()
    {
        return $this->natetab;
    }

    /**
     * Set libnatetab
     *
     * @param string $libnatetab
     *
     * @return Siren
     */
    public function setLibnatetab($libnatetab)
    {
        $this->libnatetab = $libnatetab;

        return $this;
    }

    /**
     * Get libnatetab
     *
     * @return string
     */
    public function getLibnatetab()
    {
        return $this->libnatetab;
    }

    /**
     * Set apet700
     *
     * @param string $apet700
     *
     * @return Siren
     */
    public function setApet700($apet700)
    {
        $this->apet700 = $apet700;

        return $this;
    }

    /**
     * Get apet700
     *
     * @return string
     */
    public function getApet700()
    {
        return $this->apet700;
    }

    /**
     * Set libapet
     *
     * @param string $libapet
     *
     * @return Siren
     */
    public function setLibapet($libapet)
    {
        $this->libapet = $libapet;

        return $this;
    }

    /**
     * Get libapet
     *
     * @return string
     */
    public function getLibapet()
    {
        return $this->libapet;
    }

    /**
     * Set dapet
     *
     * @param string $dapet
     *
     * @return Siren
     */
    public function setDapet($dapet)
    {
        $this->dapet = $dapet;

        return $this;
    }

    /**
     * Get dapet
     *
     * @return string
     */
    public function getDapet()
    {
        return $this->dapet;
    }

    /**
     * Set tefet
     *
     * @param string $tefet
     *
     * @return Siren
     */
    public function setTefet($tefet)
    {
        $this->tefet = $tefet;

        return $this;
    }

    /**
     * Get tefet
     *
     * @return string
     */
    public function getTefet()
    {
        return $this->tefet;
    }

    /**
     * Set libtefet
     *
     * @param string $libtefet
     *
     * @return Siren
     */
    public function setLibtefet($libtefet)
    {
        $this->libtefet = $libtefet;

        return $this;
    }

    /**
     * Get libtefet
     *
     * @return string
     */
    public function getLibtefet()
    {
        return $this->libtefet;
    }

    /**
     * Set efetcent
     *
     * @param string $efetcent
     *
     * @return Siren
     */
    public function setEfetcent($efetcent)
    {
        $this->efetcent = $efetcent;

        return $this;
    }

    /**
     * Get efetcent
     *
     * @return string
     */
    public function getEfetcent()
    {
        return $this->efetcent;
    }

    /**
     * Set defet
     *
     * @param string $defet
     *
     * @return Siren
     */
    public function setDefet($defet)
    {
        $this->defet = $defet;

        return $this;
    }

    /**
     * Get defet
     *
     * @return string
     */
    public function getDefet()
    {
        return $this->defet;
    }

    /**
     * Set origine
     *
     * @param string $origine
     *
     * @return Siren
     */
    public function setOrigine($origine)
    {
        $this->origine = $origine;

        return $this;
    }

    /**
     * Get origine
     *
     * @return string
     */
    public function getOrigine()
    {
        return $this->origine;
    }

    /**
     * Set dcret
     *
     * @param string $dcret
     *
     * @return Siren
     */
    public function setDcret($dcret)
    {
        $this->dcret = $dcret;

        return $this;
    }

    /**
     * Get dcret
     *
     * @return string
     */
    public function getDcret()
    {
        return $this->dcret;
    }

    /**
     * Set ddebact
     *
     * @param string $ddebact
     *
     * @return Siren
     */
    public function setDdebact($ddebact)
    {
        $this->ddebact = $ddebact;

        return $this;
    }

    /**
     * Get ddebact
     *
     * @return string
     */
    public function getDdebact()
    {
        return $this->ddebact;
    }

    /**
     * Set activnat
     *
     * @param string $activnat
     *
     * @return Siren
     */
    public function setActivnat($activnat)
    {
        $this->activnat = $activnat;

        return $this;
    }

    /**
     * Get activnat
     *
     * @return string
     */
    public function getActivnat()
    {
        return $this->activnat;
    }

    /**
     * Set lieuact
     *
     * @param string $lieuact
     *
     * @return Siren
     */
    public function setLieuact($lieuact)
    {
        $this->lieuact = $lieuact;

        return $this;
    }

    /**
     * Get lieuact
     *
     * @return string
     */
    public function getLieuact()
    {
        return $this->lieuact;
    }

    /**
     * Set actisurf
     *
     * @param string $actisurf
     *
     * @return Siren
     */
    public function setActisurf($actisurf)
    {
        $this->actisurf = $actisurf;

        return $this;
    }

    /**
     * Get actisurf
     *
     * @return string
     */
    public function getActisurf()
    {
        return $this->actisurf;
    }

    /**
     * Set saisonat
     *
     * @param string $saisonat
     *
     * @return Siren
     */
    public function setSaisonat($saisonat)
    {
        $this->saisonat = $saisonat;

        return $this;
    }

    /**
     * Get saisonat
     *
     * @return string
     */
    public function getSaisonat()
    {
        return $this->saisonat;
    }

    /**
     * Set modet
     *
     * @param string $modet
     *
     * @return Siren
     */
    public function setModet($modet)
    {
        $this->modet = $modet;

        return $this;
    }

    /**
     * Get modet
     *
     * @return string
     */
    public function getModet()
    {
        return $this->modet;
    }

    /**
     * Set prodet
     *
     * @param string $prodet
     *
     * @return Siren
     */
    public function setProdet($prodet)
    {
        $this->prodet = $prodet;

        return $this;
    }

    /**
     * Get prodet
     *
     * @return string
     */
    public function getProdet()
    {
        return $this->prodet;
    }

    /**
     * Set prodpart
     *
     * @param string $prodpart
     *
     * @return Siren
     */
    public function setProdpart($prodpart)
    {
        $this->prodpart = $prodpart;

        return $this;
    }

    /**
     * Get prodpart
     *
     * @return string
     */
    public function getProdpart()
    {
        return $this->prodpart;
    }

    /**
     * Set auxilt
     *
     * @param string $auxilt
     *
     * @return Siren
     */
    public function setAuxilt($auxilt)
    {
        $this->auxilt = $auxilt;

        return $this;
    }

    /**
     * Get auxilt
     *
     * @return string
     */
    public function getAuxilt()
    {
        return $this->auxilt;
    }

    /**
     * Set nomenLong
     *
     * @param string $nomenLong
     *
     * @return Siren
     */
    public function setNomenLong($nomenLong)
    {
        $this->nomenLong = $nomenLong;

        return $this;
    }

    /**
     * Get nomenLong
     *
     * @return string
     */
    public function getNomenLong()
    {
        return $this->nomenLong;
    }

    /**
     * Set sigle
     *
     * @param string $sigle
     *
     * @return Siren
     */
    public function setSigle($sigle)
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * Get sigle
     *
     * @return string
     */
    public function getSigle()
    {
        return $this->sigle;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Siren
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
     * @return Siren
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
     * Set civilite
     *
     * @param string $civilite
     *
     * @return Siren
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set rna
     *
     * @param string $rna
     *
     * @return Siren
     */
    public function setRna($rna)
    {
        $this->rna = $rna;

        return $this;
    }

    /**
     * Get rna
     *
     * @return string
     */
    public function getRna()
    {
        return $this->rna;
    }

    /**
     * Set nicsiege
     *
     * @param string $nicsiege
     *
     * @return Siren
     */
    public function setNicsiege($nicsiege)
    {
        $this->nicsiege = $nicsiege;

        return $this;
    }

    /**
     * Get nicsiege
     *
     * @return string
     */
    public function getNicsiege()
    {
        return $this->nicsiege;
    }

    /**
     * Set rpen
     *
     * @param string $rpen
     *
     * @return Siren
     */
    public function setRpen($rpen)
    {
        $this->rpen = $rpen;

        return $this;
    }

    /**
     * Get rpen
     *
     * @return string
     */
    public function getRpen()
    {
        return $this->rpen;
    }

    /**
     * Set depcomen
     *
     * @param string $depcomen
     *
     * @return Siren
     */
    public function setDepcomen($depcomen)
    {
        $this->depcomen = $depcomen;

        return $this;
    }

    /**
     * Get depcomen
     *
     * @return string
     */
    public function getDepcomen()
    {
        return $this->depcomen;
    }

    /**
     * Set adrMail
     *
     * @param string $adrMail
     *
     * @return Siren
     */
    public function setAdrMail($adrMail)
    {
        $this->adrMail = $adrMail;

        return $this;
    }

    /**
     * Get adrMail
     *
     * @return string
     */
    public function getAdrMail()
    {
        return $this->adrMail;
    }

    /**
     * Set nj
     *
     * @param string $nj
     *
     * @return Siren
     */
    public function setNj($nj)
    {
        $this->nj = $nj;

        return $this;
    }

    /**
     * Get nj
     *
     * @return string
     */
    public function getNj()
    {
        return $this->nj;
    }

    /**
     * Set libnj
     *
     * @param string $libnj
     *
     * @return Siren
     */
    public function setLibnj($libnj)
    {
        $this->libnj = $libnj;

        return $this;
    }

    /**
     * Get libnj
     *
     * @return string
     */
    public function getLibnj()
    {
        return $this->libnj;
    }

    /**
     * Set apen700
     *
     * @param string $apen700
     *
     * @return Siren
     */
    public function setApen700($apen700)
    {
        $this->apen700 = $apen700;

        return $this;
    }

    /**
     * Get apen700
     *
     * @return string
     */
    public function getApen700()
    {
        return $this->apen700;
    }

    /**
     * Set libapen
     *
     * @param string $libapen
     *
     * @return Siren
     */
    public function setLibapen($libapen)
    {
        $this->libapen = $libapen;

        return $this;
    }

    /**
     * Get libapen
     *
     * @return string
     */
    public function getLibapen()
    {
        return $this->libapen;
    }

    /**
     * Set dapen
     *
     * @param string $dapen
     *
     * @return Siren
     */
    public function setDapen($dapen)
    {
        $this->dapen = $dapen;

        return $this;
    }

    /**
     * Get dapen
     *
     * @return string
     */
    public function getDapen()
    {
        return $this->dapen;
    }

    /**
     * Set aprm
     *
     * @param string $aprm
     *
     * @return Siren
     */
    public function setAprm($aprm)
    {
        $this->aprm = $aprm;

        return $this;
    }

    /**
     * Get aprm
     *
     * @return string
     */
    public function getAprm()
    {
        return $this->aprm;
    }

    /**
     * Set ess
     *
     * @param string $ess
     *
     * @return Siren
     */
    public function setEss($ess)
    {
        $this->ess = $ess;

        return $this;
    }

    /**
     * Get ess
     *
     * @return string
     */
    public function getEss()
    {
        return $this->ess;
    }

    /**
     * Set dateess
     *
     * @param string $dateess
     *
     * @return Siren
     */
    public function setDateess($dateess)
    {
        $this->dateess = $dateess;

        return $this;
    }

    /**
     * Get dateess
     *
     * @return string
     */
    public function getDateess()
    {
        return $this->dateess;
    }

    /**
     * Set tefen
     *
     * @param string $tefen
     *
     * @return Siren
     */
    public function setTefen($tefen)
    {
        $this->tefen = $tefen;

        return $this;
    }

    /**
     * Get tefen
     *
     * @return string
     */
    public function getTefen()
    {
        return $this->tefen;
    }

    /**
     * Set libtefen
     *
     * @param string $libtefen
     *
     * @return Siren
     */
    public function setLibtefen($libtefen)
    {
        $this->libtefen = $libtefen;

        return $this;
    }

    /**
     * Get libtefen
     *
     * @return string
     */
    public function getLibtefen()
    {
        return $this->libtefen;
    }

    /**
     * Set efencent
     *
     * @param string $efencent
     *
     * @return Siren
     */
    public function setEfencent($efencent)
    {
        $this->efencent = $efencent;

        return $this;
    }

    /**
     * Get efencent
     *
     * @return string
     */
    public function getEfencent()
    {
        return $this->efencent;
    }

    /**
     * Set defen
     *
     * @param string $defen
     *
     * @return Siren
     */
    public function setDefen($defen)
    {
        $this->defen = $defen;

        return $this;
    }

    /**
     * Get defen
     *
     * @return string
     */
    public function getDefen()
    {
        return $this->defen;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Siren
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set dcren
     *
     * @param string $dcren
     *
     * @return Siren
     */
    public function setDcren($dcren)
    {
        $this->dcren = $dcren;

        return $this;
    }

    /**
     * Get dcren
     *
     * @return string
     */
    public function getDcren()
    {
        return $this->dcren;
    }

    /**
     * Set amintren
     *
     * @param string $amintren
     *
     * @return Siren
     */
    public function setAmintren($amintren)
    {
        $this->amintren = $amintren;

        return $this;
    }

    /**
     * Get amintren
     *
     * @return string
     */
    public function getAmintren()
    {
        return $this->amintren;
    }

    /**
     * Set monoact
     *
     * @param string $monoact
     *
     * @return Siren
     */
    public function setMonoact($monoact)
    {
        $this->monoact = $monoact;

        return $this;
    }

    /**
     * Get monoact
     *
     * @return string
     */
    public function getMonoact()
    {
        return $this->monoact;
    }

    /**
     * Set moden
     *
     * @param string $moden
     *
     * @return Siren
     */
    public function setModen($moden)
    {
        $this->moden = $moden;

        return $this;
    }

    /**
     * Get moden
     *
     * @return string
     */
    public function getModen()
    {
        return $this->moden;
    }

    /**
     * Set proden
     *
     * @param string $proden
     *
     * @return Siren
     */
    public function setProden($proden)
    {
        $this->proden = $proden;

        return $this;
    }

    /**
     * Get proden
     *
     * @return string
     */
    public function getProden()
    {
        return $this->proden;
    }

    /**
     * Set esaann
     *
     * @param string $esaann
     *
     * @return Siren
     */
    public function setEsaann($esaann)
    {
        $this->esaann = $esaann;

        return $this;
    }

    /**
     * Get esaann
     *
     * @return string
     */
    public function getEsaann()
    {
        return $this->esaann;
    }

    /**
     * Set tca
     *
     * @param string $tca
     *
     * @return Siren
     */
    public function setTca($tca)
    {
        $this->tca = $tca;

        return $this;
    }

    /**
     * Get tca
     *
     * @return string
     */
    public function getTca()
    {
        return $this->tca;
    }

    /**
     * Set esaapen
     *
     * @param string $esaapen
     *
     * @return Siren
     */
    public function setEsaapen($esaapen)
    {
        $this->esaapen = $esaapen;

        return $this;
    }

    /**
     * Get esaapen
     *
     * @return string
     */
    public function getEsaapen()
    {
        return $this->esaapen;
    }

    /**
     * Set esasec1n
     *
     * @param string $esasec1n
     *
     * @return Siren
     */
    public function setEsasec1n($esasec1n)
    {
        $this->esasec1n = $esasec1n;

        return $this;
    }

    /**
     * Get esasec1n
     *
     * @return string
     */
    public function getEsasec1n()
    {
        return $this->esasec1n;
    }

    /**
     * Set esasec2n
     *
     * @param string $esasec2n
     *
     * @return Siren
     */
    public function setEsasec2n($esasec2n)
    {
        $this->esasec2n = $esasec2n;

        return $this;
    }

    /**
     * Get esasec2n
     *
     * @return string
     */
    public function getEsasec2n()
    {
        return $this->esasec2n;
    }

    /**
     * Set esasec3n
     *
     * @param string $esasec3n
     *
     * @return Siren
     */
    public function setEsasec3n($esasec3n)
    {
        $this->esasec3n = $esasec3n;

        return $this;
    }

    /**
     * Get esasec3n
     *
     * @return string
     */
    public function getEsasec3n()
    {
        return $this->esasec3n;
    }

    /**
     * Set esasec4n
     *
     * @param string $esasec4n
     *
     * @return Siren
     */
    public function setEsasec4n($esasec4n)
    {
        $this->esasec4n = $esasec4n;

        return $this;
    }

    /**
     * Get esasec4n
     *
     * @return string
     */
    public function getEsasec4n()
    {
        return $this->esasec4n;
    }

    /**
     * Set vmaj
     *
     * @param string $vmaj
     *
     * @return Siren
     */
    public function setVmaj($vmaj)
    {
        $this->vmaj = $vmaj;

        return $this;
    }

    /**
     * Get vmaj
     *
     * @return string
     */
    public function getVmaj()
    {
        return $this->vmaj;
    }

    /**
     * Set vmaj1
     *
     * @param string $vmaj1
     *
     * @return Siren
     */
    public function setVmaj1($vmaj1)
    {
        $this->vmaj1 = $vmaj1;

        return $this;
    }

    /**
     * Get vmaj1
     *
     * @return string
     */
    public function getVmaj1()
    {
        return $this->vmaj1;
    }

    /**
     * Set vmaj2
     *
     * @param string $vmaj2
     *
     * @return Siren
     */
    public function setVmaj2($vmaj2)
    {
        $this->vmaj2 = $vmaj2;

        return $this;
    }

    /**
     * Get vmaj2
     *
     * @return string
     */
    public function getVmaj2()
    {
        return $this->vmaj2;
    }

    /**
     * Set vmaj3
     *
     * @param string $vmaj3
     *
     * @return Siren
     */
    public function setVmaj3($vmaj3)
    {
        $this->vmaj3 = $vmaj3;

        return $this;
    }

    /**
     * Get vmaj3
     *
     * @return string
     */
    public function getVmaj3()
    {
        return $this->vmaj3;
    }

    /**
     * Get siren
     *
     * @return string
     */
    public function getSiren()
    {
        return $this->siren;
    }
}

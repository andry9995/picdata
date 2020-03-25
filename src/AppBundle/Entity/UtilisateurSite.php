<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateurSite
 *
 * @ORM\Table(name="utilisateur_site", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_site_utilisateur", columns={"utilisateur_id", "site_id"})}, indexes={@ORM\Index(name="fk_utilisateur_has_site_site1_idx", columns={"site_id"}), @ORM\Index(name="fk_utilisateur_has_site_utilisateur1_idx", columns={"utilisateur_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UtilisateurSiteRepository")
 */
class UtilisateurSite
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
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur", inversedBy="sites")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

    /**
     * @var \AppBundle\Entity\Site
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;



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
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return UtilisateurSite
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return UtilisateurSite
     */
    public function setSite(\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }
}

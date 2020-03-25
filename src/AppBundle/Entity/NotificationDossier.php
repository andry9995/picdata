<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationDossier
 *
 * @ORM\Table(name="notification_dossier", indexes={@ORM\Index(name="fk_notifdosiier_dossier_id_idx", columns={"dossier_id"}), @ORM\Index(name="fk_notifdossier_notif_id_idx", columns={"notification_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationDossierRepository")
 */
class NotificationDossier
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
     * @var \AppBundle\Entity\Notification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Notification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     * })
     */
    private $notification;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set notification
     *
     * @param \AppBundle\Entity\Notification $notification
     *
     * @return NotificationDossier
     */
    public function setNotification(\AppBundle\Entity\Notification $notification = null)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return \AppBundle\Entity\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NotificationDossier
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
}

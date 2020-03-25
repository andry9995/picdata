<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailSender
 *
 * @ORM\Table(name="mail_sender")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MailSenderRepository")
 */
class MailSender
{
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=45, nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="adresses", type="text", nullable=false)
     */
    private $adresses;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return MailSender
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return MailSender
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set adresses
     *
     * @param string $adresses
     *
     * @return MailSender
     */
    public function setAdresses($adresses)
    {
        $this->adresses = $adresses;

        return $this;
    }

    /**
     * Get adresses
     *
     * @return string
     */
    public function getAdresses()
    {
        return $this->adresses;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return MailSender
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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

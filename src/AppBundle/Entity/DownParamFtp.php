<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DownParamFtp
 *
 * @ORM\Table(name="down_param_ftp")
 * @ORM\Entity
 */
class DownParamFtp
{
    /**
     * @var string
     *
     * @ORM\Column(name="serveurip", type="string", length=60, nullable=true)
     */
    private $serveurip;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=45, nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=45, nullable=true)
     */
    private $mdp;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set serveurip
     *
     * @param string $serveurip
     *
     * @return DownParamFtp
     */
    public function setServeurip($serveurip)
    {
        $this->serveurip = $serveurip;

        return $this;
    }

    /**
     * Get serveurip
     *
     * @return string
     */
    public function getServeurip()
    {
        return $this->serveurip;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return DownParamFtp
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set mdp
     *
     * @param string $mdp
     *
     * @return DownParamFtp
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;

        return $this;
    }

    /**
     * Get mdp
     *
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return DownParamFtp
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

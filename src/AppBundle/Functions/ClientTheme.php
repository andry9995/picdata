<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/06/2017
 * Time: 11:52
 */

namespace AppBundle\Functions;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Theme du client
 *
 * Class ClientTheme
 * @package AppBundle\Functions
 */
class ClientTheme
{
    /** @var TokenStorage */
    private $tokenStorage;

    /** @var  AuthorizationChecker */
    private $authChecker;

    /** @var  EntityManager */
    private $em;

    private $packages;
    private $root_dir;

    public function __construct(TokenStorage $storage, AuthorizationChecker $checker, EntityManager $em, Packages $packages, $root_dir)
    {
        $this->tokenStorage = $storage;
        $this->authChecker = $checker;
        $this->em = $em;
        $this->packages = $packages;
        $this->root_dir = $root_dir;
    }

    /**
     * Get Css Theme Client (.css)
     *
     * @return null|string
     */
    public function getCssTheme()
    {
        $user = $this->tokenStorage
            ->getToken()
            ->getUser();
        if ($user && $user instanceof \AppBundle\Entity\Utilisateur) {
            $client = $user->getClient();
            if ($client && $client instanceof \AppBundle\Entity\Client) {
                $theme = $this->em
                    ->getRepository('AppBundle:ClientTheme')
                    ->getCssTheme($client);
                if ($theme && trim($theme) != '') {
                    if (is_file($this->root_dir . '/../web/css/themes/' . $theme)) {
                        return $theme;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get Logo Client (Image)
     *
     * @return null|string
     */
    public function getLogo()
    {
        $user = $this->tokenStorage
            ->getToken()
            ->getUser();
        if ($user && $user instanceof \AppBundle\Entity\Utilisateur) {
            $client = $user->getClient();
            if ($client && $client instanceof \AppBundle\Entity\Client) {
                $logo = $client->getLogo();
                if ($logo && trim($logo) != '') {
                    if (is_file($this->root_dir . '/../web/img/logo/' . $logo)) {
                        return $logo;
                    }
                }
            }
        }
        return '626.png';
    }

    /**
     * Get Logo Par client Pour Email
     *
     * @param $client_id
     * @return string
     */
    public function getLogoForEmail($client_id)
    {
        $client = $this->em->getRepository('AppBundle:Client')
            ->find($client_id);
        if (!$client) {
            return '';
        }
        if ($client && $client instanceof \AppBundle\Entity\Client) {
            $logo = $client->getLogo();
            if ($logo && trim($logo) != '') {
                if (is_file($this->root_dir . '/../web/img/email/' . $logo)) {
                    return 'img/email/' . $logo;
                }
            }
        }
        return '';
    }
}
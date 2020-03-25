<?php

/**
 * Created by Netbeans
 * Created on : 7 juil. 2017, 17:23:33
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Service;

use Doctrine\ORM\EntityManager;

class OpportuniteService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
}
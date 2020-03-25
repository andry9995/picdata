<?php

/**
 * Created by Netbeans
 * Created on : 13 juil. 2017, 22:01:33
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Service;

class UploadService {
    private $maxFileSize = 10; //En Mo
    private $validExtensions = array('png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx');
    
    public function getMaxFileSize() {
        return $this->maxFileSize;
    }
    
    public function getValidExtensions() {
        return $this->validExtensions;
    }
}
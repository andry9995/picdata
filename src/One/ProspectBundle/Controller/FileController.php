<?php

/**
 * Created by Netbeans
 * Created on : 13 juil. 2017, 21:56:35
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileController extends Controller
{
    /**
     * Upload d'un fichier
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request) {
        if($request->isMethod('POST')) {
            $uploadUrl = $this->getParameter('one_upload_url');
            $validExtensions = $this->getParameter('one_valid_extensions');
            $maxFileSize = $this->getParameter('one_max_filesize');
            $file = $request->files->get('file-to-upload');
            if ($file->getClientSize()<= $maxFileSize && in_array($file->guessExtension(), $validExtensions)) {
                $name = $file->getClientOriginalName();
                $ext = $file->guessExtension();
                $uniqid = md5(uniqid());
                $filename = $uniqid.'.'.$ext;
                $file->move($this->getParameter('one_upload_dir'), $filename);
                
                if ($ext==='png' || $ext==='jpg' || $ext==='jpeg' || $ext='gif')
                    $response = array('type'=>'success', 'filename'=>$filename, 'path'=>$uploadUrl, 'toshow'=>$uploadUrl.$filename, 'name'=>$name, 'uniqid'=>$uniqid);
                else
                    $response = array('type'=>'success', 'filename'=>$filename, 'path'=>$uploadUrl, 'toshow'=>'/bundles/oneprospect/img/default-file.png', 'name'=>$name, 'uniqid'=>$uniqid);
                return new JsonResponse($response);
            } else {
                $response = array('type'=>'error', 'filename'=>$filename, 'message'=>'Fichier volumineux ou non autorisé');
                return new JsonResponse($response);
            }
        } else {
            $response = array('type'=>'error', 'message'=>'Aucun fichier uploadé');
            return new JsonResponse($response);
        }
    }
}
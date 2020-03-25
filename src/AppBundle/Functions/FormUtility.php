<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 17/02/2017
 * Time: 10:57
 */

namespace AppBundle\Functions;


class FormUtility
{
    public static function getErrorMessages(\Symfony\Component\Form\Form $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = FormUtility::getErrorMessages($child);
            }
        }
        return $errors;
    }
}
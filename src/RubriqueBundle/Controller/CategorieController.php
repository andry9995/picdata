<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/10/2019
 * Time: 09:18
 */

namespace RubriqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\TresoCategorie;
use AppBundle\Entity\TresoCategoriePcg;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategorieController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categorieContainersAction(Request $request)
    {
        return $this->render('RubriqueBundle:Categorie:index.html.twig');
    }

    public function categoriesAction(Request $request)
    {
        $type = intval($request->request->get('type'));
        $tresorerieCategories = $this->getDoctrine()->getRepository('AppBundle:TresoCategorie')
            ->getAll($type);

        $results = [];

        foreach ($tresorerieCategories as $tresorerieCategorie)
        {
            $tresoCategoriePcgs = $this->getDoctrine()->getRepository('AppBundle:TresoCategoriePcg')
                ->getForTresoCategories($tresorerieCategorie);

            $res = [
                'id' => Boost::boost($tresorerieCategorie->getId()),
                'libelle' => $tresorerieCategorie->getLibelle(),
            ];

            foreach ($tresoCategoriePcgs as $key => $tresoCategoriePcg)
            {
                $caractere = '';

                $caracteres = $this->getDoctrine()->getRepository('AppBundle:PcgsRubrique')->getCaracteres();
                if($tresoCategoriePcg->getSolde() != 0) $caractere .= array_search($tresoCategoriePcg->getSolde(), $caracteres);
                if($tresoCategoriePcg->getTypeCompte() != 0) $caractere .= array_search($tresoCategoriePcg->getTypeCompte() + 4, $caracteres);

                $res['_'.$key] = $tresoCategoriePcg->getPcg()->getCompte() . $caractere;
            }

            $results[] = (object)$res;
        }

        return new JsonResponse($results);

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $results
        ]);
    }

    public function categorieSaveAction(Request $request)
    {
        $libelle = trim($request->request->get('libelle'));
        $action = intval($request->request->get('action'));
        $tresorerieCategorie = Boost::deboost($request->request->get('tresorerie_categorie'),$this);
        if(is_bool($tresorerieCategorie)) return new Response('security');
        $tresorerieCategorie = $this->getDoctrine()->getRepository('AppBundle:TresoCategorie')
            ->find($tresorerieCategorie);
        $em = $this->getDoctrine()->getManager();

        if ($action == 0)
        {
            $type = intval($request->request->get('type'));
            $tresorerieCategorie = new TresoCategorie();

            $tresorerieCategorie
                ->setLibelle($libelle)
                ->setType($type);
            $em->persist($tresorerieCategorie);
        }

        $em->flush();

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $tresorerieCategorie
        ]);

        /*libelle: libelle,
        type: type,
        action: 0*/
    }

    public function categoriePcgSaveAction(Request $request)
    {
        $type = intval($request->request->get('type'));
        $tresoCategorie = Boost::deboost($request->request->get('cat'),$this);
        if(is_bool($tresoCategorie)) return new Response('security');
        /** @var TresoCategorie $tresoCategorie */
        $tresoCategorie = $this->getDoctrine()->getRepository('AppBundle:TresoCategorie')
            ->find($tresoCategorie);
        $new = trim($request->request->get('new'));
        $em = $this->getDoctrine()->getManager();

        if ($type == 1)
        {
            $tresoCategorie
                ->setLibelle($new);
        }
        else
        {
            $oldVal = trim($request->request->get('old'));
            $pcgOld = abs(intval($oldVal));
            $pcgOld = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getByCompte($pcgOld);
            $pcgNew = abs(intval($new));
            $pcgNew = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getByCompte($pcgNew);

            $negation = (intval($new) < 0) ? 1 : 0;
            $caracteres = $this->getDoctrine()->getRepository('AppBundle:PcgsRubrique')->getCaracteres();
            $solde = 0;
            $typeCompte = 0;
            $chars = str_split($new);
            foreach ($chars as $char)
            {
                if(array_key_exists($char,$caracteres))
                {
                    $carVal = $caracteres[$char];
                    if($carVal < 5) $solde = $carVal;
                    else $typeCompte = $carVal - 4;
                }
            }

            //modifie ancien
            if($pcgOld)
            {
                if($pcgNew == null && $new != '') return new Response(2);
                $tresoCategoriePcgOld = $this->getDoctrine()->getRepository('AppBundle:TresoCategoriePcg')
                    ->getTresoCategoriePcg($tresoCategorie,$pcgOld);

                if($new == '') $em->remove($tresoCategoriePcgOld);
                else
                {
                    $tresoCategoriePcgOld
                        ->setPcg($pcgNew)
                        ->setNegation($negation)
                        ->setSolde($solde)
                        ->setTypeCompte($typeCompte);
                }
            }
            //ajout nouveau
            else
            {
                if($pcgNew == null) return new Response(2);
                $tresoCategoriePcgOld = new TresoCategoriePcg();
                $tresoCategoriePcgOld
                    ->setTresoCategorie($tresoCategorie)
                    ->setPcg($pcgNew)
                    ->setNegation($negation)
                    ->setSolde($solde)
                    ->setTypeCompte($typeCompte);
                $em->persist($tresoCategoriePcgOld);
            }
        }

        try
        {
            $em->flush();
            return new Response(1);
        }
        catch (UniqueConstraintViolationException $ex)
        {
            return new Response(0);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function deleteCategorieAction(Request $request)
    {
        $tresoCategorie = Boost::deboost($request->request->get('cat'),$this);
        if(is_bool($tresoCategorie)) return new Response('security');
        $tresoCategorie = $this->getDoctrine()->getRepository('AppBundle:TresoCategorie')
            ->find($tresoCategorie);

        $em = $this->getDoctrine()->getManager();
        $em->remove($tresoCategorie);
        $em->flush();

        return new Response(1);
    }
}
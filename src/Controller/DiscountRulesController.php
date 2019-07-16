<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\DiscountRules;
use App\Entity\Products;
use App\Form\DiscountRulesType;

use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Validator\Constraint;


class DiscountRulesController extends AbstractController
{
    /**
     * @Route("/", name="discountedrules")
     */
    public function index()
    {
     	$discountedRules = $this->getDoctrine()->getRepository(DiscountRules::class)->findAll();
    	if(empty($discountedRules))
    	{
    		$this->addFlash(
    			'danger',
    			'Il n\'a pas de régles définies pour le moment'
    		);
    	}
        return $this->render('discounted_rules/index.html.twig', [
            'controller_name' => 'Les régles de réduction',
            'rules' => $discountedRules
        ]);
    }


    /**
    * @Route("/new", name="new_discountedrules")
    */
    public function new(Request $request)
    {
    	// On créé le formulaire pour la création d'une nouvelle règle
    	$discountedRule = new DiscountRules();
    	$form = $this->createForm(DiscountRulesType::class, $discountedRule);

    	$form->handleRequest($request);

    	//Si le formulaire est valide et correctement rempli, on enregistre la nouvele règle en base de données
    	if($form->isSubmitted() && $form->isValid()){
    		$em = $this->getDoctrine()->getManager();
			$em->persist($discountedRule);
			$em->flush();
    	 	$this->addFlash('success', 'La nouvelle règle a bien été enregistrée');
    	}
    	else{
    		$this->addFlash('danger', 'La nouvelle règle n\'est pas correcte');
    	}

    	return $this->render('discounted_rules/new.html.twig', [
            'form' => $form->createView()
            ]
        );
    }

}

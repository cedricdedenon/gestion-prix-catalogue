<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\DiscountRules;
use App\Entity\Products;
use App\Form\DiscountRulesType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
    	if($form->isSubmitted() && $form->isValid()){

    		$rule = $form->getData()->getRuleExpression();
    		$percent = $form->getData()->getDiscountPercent();
			
			$expressionLanguage = new ExpressionLanguage();

			// *** TEST DE L'EXPRESSION DE LA REGLE ***
			/* L'expression doit être de type (espace inclus)
				product.type = 'TYPE' 
				ou 
				product.type = 'TYPE' and product.price OPERATOR PRICE
				avec
					TYPE = une des catégories disponibles dans le catalogue (Electro-ménager, Hi-fi ...)
					OPERATOR = l'opérateur de comparaison pour le prix (>, >=, <, >= ou =)
					PRICE = le seuil du prix du produit pour la réduction
			*/
			$explodeRule = explode(' ', $rule); 		// On éclate notre chaine en les séparant par un espace

			// On compte le nombre d'élément de notre tableau
				// Si explodeRuleCount = 3, on a entré une régle de type [product.type = 'TYPE']
				// Si explodeRuleCount = 7, on a entré une régle de type [product.type = 'TYPE' and product.price OPERATOR PRICE] 
				// Dans tous les autres cas, l'expression de la règle est erronée 
			$explodeRuleCount = count($explodeRule);

			// On a besoin d'aller chercher tous les types (ou les catégories) disponibles dans le catalogue
			$products = $this->getDoctrine()->getRepository(Products::class)->findAll();
			$typeAllow = [];
			foreach ($products as $product) {
				$typeAllow[] = $product->getType(); 
			};

			// On évalue chacune des expressions
			$successRule = true;
			$successPercent = true;
			if($explodeRuleCount != 3 && $explodeRuleCount != 7){
				$successRule = false;
			} else{
				if(!$expressionLanguage->evaluate('typeCol == "product.type"', ['typeCol' => $explodeRule[0]])) $successRule = false;
				if(!$expressionLanguage->evaluate('operator1 == "="', ['operator1' => $explodeRule[1]])) $successRule = false;
				if(!in_array(trim($explodeRule[2], "'"), $typeAllow)) $successRule = false;
				if ($explodeRuleCount === 7) {
					if(!$expressionLanguage->evaluate('operator2 == "and"', ['operator2' => $explodeRule[3]])) $successRule = false;
					if(!$expressionLanguage->evaluate('priceCol == "product.price"', ['priceCol' => $explodeRule[4]])) $successRule = false;
					if(!$expressionLanguage->evaluate('operator3 in ["<", "<=", ">", ">=", "="]', ['operator3' => $explodeRule[5]])) $successRule = false;
					if(!is_numeric($explodeRule[6])) $successRule = false;
				}
			}

			//S'il y a des erreurs dans l'expression, on affiche une erreur avec les types disponilbles dans le catalogue
			$type =implode(" , ",  array_unique($typeAllow));
			if(!$successRule) $this->addFlash('danger', "Erreur d'écriture de la règle, la régle doit être écrite comme ci-dessous: " . 
								"<br><br>" . "1) product.type = 'Type' <br>2) product.type = 'Type' and product.price >= 100"
								. "<br> avec Type = $type" );

			// *** TEST DU POURCENTAGE DE REDUCTION --> le prix du produits (doit être un entier compris entre 1 et 50 inclus) ***
			if(!$expressionLanguage->evaluate(
			    'discount_percent in 1..50',
			    [
			        'discount_percent' => $percent,
			    ]
			)){
				$successPercent = false;
				$this->addFlash('danger', "Le pourcentage de réduction doit être compris entre 1 et 50 (entier uniquement)");
			}

			// Si PAS D'ERREUR, ON INSERE LA NOUVELLE REGLE DANS LA BASE DE DONNEES
			if($successRule && $successPercent){
	    		$em = $this->getDoctrine()->getManager();
    			$em->persist($discountedRule);
    			$em->flush();

    			$this->addFlash('success', "La règle a bien été ajoutée !");
    			return $this->redirectToRoute('discountedrules');
			}
    	}

    	return $this->render('discounted_rules/new.html.twig', [
            'form' => $form->createView()
            ]
        );
    }

}

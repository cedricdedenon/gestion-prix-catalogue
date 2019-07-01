<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\DiscountRules;

class DiscountedRulesController extends AbstractController
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

}

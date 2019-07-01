<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Products;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index()
    {
        $products = $this->getDoctrine()->getRepository(Products::class)->findAll();

        return $this->render('products/index.html.twig', [
            'controller_name' => 'Les produits disponibles en catalogue',
            'products' => $products
        ]);
    }
}

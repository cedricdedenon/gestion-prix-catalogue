<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Products;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

    	$name = ['Cafetière', 'Enceinte Bluetooth', 'TV LED 150cm', 'Aspirateur', 'Câble HDMI', 'Smartphone', 'Casserole inox', 'Jeu PS4', 'Liquide de refroidissement été', 'Machine à laver'];
    	$price = [15.50, 100, 399, 149, 8.50, 1100, 12.80, 70, 3.50, 290];
        $type = ['Electro-ménager', 'Hi-fi', 'Multimédia', 'Electro-ménager', 'Connectique', 'Multimédia', 'Cuisine', 'Multimédia', 'Automobile', 'Electro-ménager'];

        $nb_products = count($name);

        for($i = 0; $i < $nb_products; $i++){
        	$product = new Products();
        	$product->setName($name[$i])
        				->setPrice($price[$i])
        				->setType($type[$i]);

        	$manager->persist($product);
        }

        $manager->flush();
    }
}

<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Products;
use App\Entity\DiscountRules;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ProductsRepository;


class SendEmailCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:send-email';

    protected $service;
    protected $em;

    
    public function __construct(EntityManagerInterface $em){
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
        // Une courte description lorsque que l'on éxécute la commande "php bin/console list"
        ->setDescription('Envoyer un email avec les réductions')

        // Description entière de la commande en rajoutant l'option --help
        ->setHelp('Cette commande permet de remettre à jour les prix réduits de la totalité du catalogue de produit. Permet d\'envoyer un email récapitulatif')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // 1.   ON MET A JOUR TOUS LES PRIX EN REDUCTION
        // On récupère toutes les règles existantes
        $rules = $this->em->getRepository(DiscountRules::class)->findAll();
        if(empty($rules))
        {
            $output->writeln('Il n\'y a aucune régle définie, l\'email ne sera pas envoyé');
            $products = $this->em->getRepository(Products::class)->findAll();
            foreach ($products as $product) {
                $null = NULL;
                $product->setDiscountedPrice($null);
            }
            $this->em->flush();
        }
        else{

            // Pour chacune de règles, on va appliquer la réduction aux produits soldés 
            foreach ($rules as $rule) {
                // On récupére l'expression de la règle et le pourcentage de réduction
                $ruleExpression = $rule->getRuleExpression();
                $rule_discount_percent = $rule->getDiscountPercent();

                // On éclate l'expression de la règle pour en extraire le type, le prix et l'opérateur de comparaison
                $ruleExpressionExplode = explode(" ", $ruleExpression);
                $rule_type = trim($ruleExpressionExplode[2], "'");

                // On teste si la régle contient une condition sur le prix (count = 7) ou non (count = 3)
                $count = count($ruleExpressionExplode);

                // Si count = 7 alors il s'agit d'une requête de type [product.type = 'TYPE' and product.price OPERATOR PRICE] 
                if($count === 7){
                    $rule_price = $ruleExpressionExplode[6];
                    $rule_operator = $ruleExpressionExplode[5]; 
                    switch ($rule_operator) {
                        case '=':
                            $select_command = "findPriceEgalAt";
                            break;
                        case '>':
                            $select_command = "findPriceHigherThan";
                            break;
                        case '>=':
                            $select_command = "findPriceHigherOrEgalThan";
                            break;
                        case '<':
                            $select_command = "findPriceLowerThan";
                            break;
                         case '<=':
                            $select_command = "findPriceLowerOrEgalThan";
                            break;
                        
                        default: break;
                    }

                // On récupère tous les produits du catalogue qui correspond aux critères présents dans la règle de réduction
                // Exemple de requête possible: SELECT * FROM Products WHERE type = 'Electro-ménager' AND prics >= 100
                $products = $this->em->getRepository(Products::class)->$select_command($rule_type,$rule_price);

                } elseif($count === 3){
                    // Si count = 3 alors il s'agit d'une requête de type [product.type = 'TYPE'] 
                    $products = $this->em->getRepository(Products::class)->findBy(['type' => $rule_type]);
                } else{
                    // Sinon une erreur aura lieue (CAS IMPOSSIBLE puisque que l'effectue un contrôle sur l'expression d'une nouvelle règle au préalable)
                    $output->writeln('Une erreur est survenue !');
                }

                // On calcule le nouveau prix avec la réduction pour tous les produits de la règle en cours (arrondi à 2 chiffres après la virgule)
                foreach ($products as $product) {
                    $product_price = $product->getPrice();
                    $new_product_price = round($product_price - (($product_price * $rule_discount_percent) / 100), 2);
                    $product->setDiscountedPrice($new_product_price);
                }

                // On enregistre en base de données les nouveaux prix
                $this->em->flush();
            }

            $output->writeln('La base de données à bien été modifiée !');

             // 2.   ON ENVERRA UN EMAIL PAR LA SUITE
        }
    }
}
<?php

namespace App\Service;

use App\Repository\ProductsRepository;

class EmailService{
    private $mailer;
    private $productRepository;

    public function __construct(\Swift_Mailer $mailer, ProductsRepository $productRepository){
        $this->productRepository = $productRepository;
        $this->mailer = $mailer;

    }

    public function sendEmail()
    {
        $products = $this->productRepository->findProducts();

        $body =  '<html>' .
                    ' <body>' .
                        '<h1>Produits en promotion actuellement</h1>' .
                        '<table style="border-collapse: collapse; text-align: center;">' .
                            '<thead>' .
                                '<tr>' .
                                    '<th style="border-collapse: collapse; border: 1px solid black; padding: 5px; background-color: #CCC">nom</th>' .
                                    '<th style="border-collapse: collapse; border: 1px solid black; padding: 5px;background-color: #CCC">Type</th>' .
                                    '<th style="border-collapse: collapse; border: 1px solid black; padding: 5px; background-color: #CCC">prix</th>' .
                                    '<th style="border-collapse: collapse; border: 1px solid black; padding: 5px; background-color: #CCC">prix réduit</th>' .
                                '</tr>' .
                            '</thead>' .
                            '<tbody>';
        
        foreach ($products as $key => $product) {
            $body .= '<tr>' .
                            '<td style="border-collapse: collapse; border: 1px solid black; padding: 5px;">' . $product->getName() . '</td>' .
                            '<td style="border-collapse: collapse; border: 1px solid black; padding: 5px;">' . $product->getType() . '</td>' .
                            '<td style="border-collapse: collapse; border: 1px solid black; padding: 5px;"><del>' . $product->getPrice() . ' € </del></td>' .
                            '<td style="border-collapse: collapse; border: 1px solid black; padding: 5px;">' . $product->getDiscountedPrice() . ' € </td>' .
                        '</tr>';
         }     

        $body .=  '</tbody>' .
                        '</table>' .
                    ' </body>' .
                '</html>'; 


        $message = (new \Swift_Message('Nos promotions'))
            ->setFrom('contact@gmail.com')
            ->setTo('testdevwebphp@gmail.com')
            ->setBody($body,'text/html')
            ;

        $this->mailer->send($message);
    }
}
?>
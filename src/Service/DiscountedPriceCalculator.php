<?php

namespace App\Service;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use App\Repository\DiscountRulesRepository;
use App\Repository\ProductsRepository;


class DiscountedPriceCalculator{

	/**
    * @var DiscountRuleRepository
    */
    private $ruleRepository;

    /**
    * @var ProductRepository
    */
    private $productRepository;

	/**
    * @var ExpressionLanguage
    */
	private $expressionLanguage;

    /**
    * DiscountedPriceCalculator constructor.
    *
    * @param DiscountRulesRepository $ruleRepository
    * @param ProductsRepository $productRepository
    * @param ExpressionLanguage $expressionLanguage
    */
    public function __construct(DiscountRulesRepository $ruleRepository, ProductsRepository $productRepository)
    {
        $this->ruleRepository = $ruleRepository;
        $this->productRepository = $productRepository;
        $this->expressionLanguage = new ExpressionLanguage();
    }

	public function calculateDiscountedPriceForProduct(){
		$rules = $this->ruleRepository->findAll();
 
        foreach ($this->productRepository->findAll() as $product) {
            $product->setDiscountedPrice(null);
        
            foreach ($rules as $rule) {
                $valuesObject = new \stdClass();
                $valuesObject->type = $product->getType();
                $valuesObject->price = $product->getPrice();

                $ruleMatch = $this->expressionLanguage->evaluate(
                    $rule->getRuleExpression(),
                    [
                        'product' => $valuesObject
                    ]
                );
        
                if (true === $ruleMatch) {
                    $discountedPrice = $product->getPrice() - ($product->getPrice() * $rule->getDiscountPercent() / 100);
                    $product->setDiscountedPrice($discountedPrice);
                }
            }
        
            $this->productRepository->save($product);
        }
	}
}
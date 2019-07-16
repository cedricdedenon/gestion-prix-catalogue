<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Validator\Constraints as AcmeAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiscountRulesRepository")
 */
class DiscountRules
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @AcmeAssert\RulesExpression
     */
    private $rule_expression;

    /**
    * @ORM\Column(type="integer")
    * @Assert\Expression(
    *     "this.getDiscountPercent() in 1..50",
    *     message="Le pourcentage de réduction doit être compris entre 1 et 50 (entier uniquement)"
    * )
    */
    private $discount_percent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuleExpression(): ?string
    {
        return $this->rule_expression;
    }

    public function setRuleExpression(string $rule_expression): self
    {
        $this->rule_expression = $rule_expression;

        return $this;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discount_percent;
    }

    public function setDiscountPercent(int $discount_percent): self
    {
        $this->discount_percent = $discount_percent;

        return $this;
    }
}

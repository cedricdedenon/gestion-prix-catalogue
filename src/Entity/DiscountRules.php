<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    private $rule_expression;

    /**
     * @ORM\Column(type="integer")
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

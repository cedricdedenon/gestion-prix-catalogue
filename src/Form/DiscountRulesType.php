<?php

namespace App\Form;

use App\Entity\DiscountRules;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DiscountRulesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rule_expression', TextType::class, [
                'attr' => [
                    'placeholder' => 'Expression de la règle',
                    'class' => "form-control"
                ]
            ])
            ->add('discount_percent', IntegerType::class, [
                 'attr' => [
                    'placeholder' => 'Pourcentage de réduction',
                    'class' => "form-control"
                ]
            ])
            ->add('enregistrer', SubmitType::class, [
                'attr' => [
                    'class' => "btn btn-success"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DiscountRules::class,
        ]);
    }
}

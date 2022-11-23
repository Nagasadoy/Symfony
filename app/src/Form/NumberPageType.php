<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberPageType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'Первая страница' => 1,
                'Десятая страница' => 10,
                'Сотая страница' => 100,
            ]
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
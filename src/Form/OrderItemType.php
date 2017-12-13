<?php

namespace App\Form;

use App\Entity\OrderItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'label' => 'Produto',
                'placeholder' => 'Selecione',
                'class' => 'App:Product',
                'required' => true,
            ])
            ->add('quant', NumberType::class, [
                'label' => 'Quantidade',
                'required' => true,
            ])
            ->add('discount', NumberType::class, [
                'label' => 'Desconto',
                'scale' => 2,
                'required' => true,
            ])
            ->add('price', HiddenType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }
}
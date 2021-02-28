<?php

namespace App\Form;

use App\Model\Calculation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 *
 * @codeCoverageIgnore
 */
class CalculationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    Calculation::TYPE_MULTIPLY,
                    Calculation::TYPE_DIVIDE,
                ],
                'choice_label' => function ($choices, $key, $value) {
                    return ucfirst($value);
                },
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('var1', TextType::class, [
                'label' => 'Value 1',
                'required' => true,
            ])
            ->add('var2', TextType::class, [
                'label' => 'Value 2',
                'required' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calculation::class,
        ]);
    }
}

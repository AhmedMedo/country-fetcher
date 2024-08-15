<?php

namespace App\Form;

use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CountryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('region', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('subRegion', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('demonym', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('population', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('independent', CheckboxType::class, [
                'constraints' => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('flag', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('currency', CurrencyType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
            'csrf_protection' => false,  // Disable CSRF protection for API
        ]);
    }
}

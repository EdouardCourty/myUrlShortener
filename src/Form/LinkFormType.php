<?php

namespace App\Form;

use App\Entity\Link;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class LinkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', UrlType::class, [
                'required' => true,
                'label' => 'URL to shorten'
            ])
            ->add('customShortcode', TextType::class, [
                'required' => false,
                'label' => 'Custom shortcode',
                'constraints' => [
                    new Length(
                        min: 5,
                        max: 255,
                        minMessage: 'The custom shortcode must be at least 5 characters long.',
                        maxMessage: 'The custom shortcode must be at most 255 characters long.'
                    )
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Shorten'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Link::class,
                'translation_domain' => false
            ]);
    }
}

<?php

namespace App\Form\Type;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, [
                'required' => false,
                'widget' => 'select2',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    $space = '';
                    for ($i = 0; $i < $category->getLevel(); ++$i) {
                        $space .= '--';
                    }

                    return $space.$category->getName();
                },
                'placeholder' => 'Select parent',
            ])
            ->add('name', TextType::class, [
                'constraints' => new NotBlank(),
            ])
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('categorySlug', TextType::class, [
                'constraints' => new NotBlank(),
            ]);
    }
}

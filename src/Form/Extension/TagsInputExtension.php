<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsInputExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [TextType::class];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tags' => false,
            'delimiter' => ';',
        ]);
        $resolver->setAllowedTypes('tags', 'bool');
        $resolver->setAllowedTypes('delimiter', 'string');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['tags']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($keywords) use ($options) {
                    return implode($options['delimiter'], $keywords ?: []);
                },
                function ($value) use ($options) {
                    return explode($options['delimiter'], $value);
                }
            ));
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['tags']) {
            $view->vars['attr'] = array_merge([
                'class' => 'tags-input',
                'data-delimiter' => $options['delimiter'],
            ], $view->vars['attr']);
        }
    }
}

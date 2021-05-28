<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ChoiceType::class];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'selectpicker',
            'search-box' => true,
        ]);
        $resolver->setAllowedValues('widget', ['select2', 'selectpicker', 'tags-select']);
        $resolver->setAllowedTypes('search-box', 'bool');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = [];
        switch ($options['widget']) {
            case 'select2':
                $attr = [
                    'class' => 'select2',
                    'data-dropdown-auto-width' => true,
                    'data-width' => '100%',
                ];
                break;

            case 'selectpicker':
                $attr = [
                    'class' => 'selectpicker',
                    'data-live-search' => true,
                    'data-live-search-style' => 'startsWith',
                ];
                break;

            case 'tags-select':
                $attr = [
                    'class' => 'tags-select',
                    'data-dropdown-auto-width' => true,
                    'data-width' => '100%',
                ];
                break;
        }

        $view->vars['attr'] = array_merge($attr, $view->vars['attr']);
    }
}

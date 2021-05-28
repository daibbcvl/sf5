<?php

namespace App\Form\Extension;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [EntityType::class];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'ajax' => null,
            'choice_value' => 'id',
            'choice_label' => 'name',
        ]);
        $resolver->addAllowedTypes('ajax', ['null', 'string', 'array']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ('select2' === $options['widget'] && isset($options['ajax'])) {
            $view->vars['attr'] = array_merge([
                'data-ajax' => json_encode($this->normalizeAjaxOptions($options)),
                'data-allow-clear' => !$options['required'],
                'data-placeholder' => $options['placeholder'],
            ], $view->vars['attr']);
            $view->vars['placeholder'] = null;
        }
    }

    private function normalizeAjaxOptions($options): array
    {
        $ajaxOptions = \is_string($options['ajax']) ? ['url' => $options['ajax']] : $options['ajax'];

        return array_merge([
            'searchOn' => 'name',
            'searchLimit' => 10,
            'delay' => 250,
            'valueField' => $options['choice_value'],
            'displayField' => $options['choice_label'],
        ], $ajaxOptions);
    }
}

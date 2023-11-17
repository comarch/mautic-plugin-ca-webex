<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @template T of object
 *
 * @extends AbstractType<T>
 */
class SubmitActionWebexRegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'meeting',
            MeetingsWithRegistrationListType::class,
            [
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'cawebex.form.label.meetings_with_registration_list.tooltip',
                ],
                'constraints' => [
                    new NotBlank(
                        ['message' => 'mautic.core.value.required']
                    ),
                ],
            ],
        );
    }
}

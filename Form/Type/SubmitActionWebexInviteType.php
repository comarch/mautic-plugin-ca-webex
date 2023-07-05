<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

use Mautic\FormBundle\Form\Type\FormFieldTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @template T of object
 *
 * @extends AbstractType<T>
 */
class SubmitActionWebexInviteType extends AbstractType
{
    use FormFieldTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'meeting',
            MeetingsListType::class,
            [
                'attr'        => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(
                        ['message' => 'mautic.core.value.required']
                    ),
                ],
            ],
        );
    }
}

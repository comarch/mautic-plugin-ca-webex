<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template T of object
 *
 * @extends AbstractType<T>
 */
class MeetingsListType extends AbstractType
{
    private GetMeetingsQuery $getMeetingsQuery;

    public function __construct(GetMeetingsQuery $getMeetingsQuery)
    {
        $this->getMeetingsQuery = $getMeetingsQuery;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $from     = date('Y-m-d');
                $to       = date('Y-m-d', strtotime('+1 year'));
                $meetings = $this->getMeetingsQuery->execute($from, $to);
                $choices  = [];
                foreach ($meetings as $meeting) {
                    $choices[$meeting['title']] = $meeting['id'];
                }

                return $choices;
            },
            'label'             => 'cawebex.form.label.meetings_list',
            'label_attr'        => ['class' => 'control-label'],
            'multiple'          => false,
            'required'          => false,
            'return_entity'     => true,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'meeting';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}

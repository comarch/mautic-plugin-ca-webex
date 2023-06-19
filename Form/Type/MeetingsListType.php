<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

use MauticPlugin\CaWebexBundle\Api\Query\GetFutureMeetingsQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingsListType extends AbstractType
{
    private GetFutureMeetingsQuery $getFutureMeetingsQuery;

    public function __construct(GetFutureMeetingsQuery $getFutureMeetingsQuery)
    {
        $this->getFutureMeetingsQuery = $getFutureMeetingsQuery;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $meetings = $this->getFutureMeetingsQuery->execute();
                $choices = [];
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
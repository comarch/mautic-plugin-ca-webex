<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingQuery;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template T of object
 *
 * @extends AbstractType<T>
 */
class MeetingsListType extends AbstractType
{
    public function __construct(
        private GetMeetingsQuery $getMeetingsQuery,
        private GetMeetingQuery $getMeetingQuery,
        private WebexIntegrationHelper $webexIntegrationHelper
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $scheduledTypeFilter = $this->webexIntegrationHelper->getScheduledTypeSetting();

        $resolver->setDefaults([
            'choices' => function (Options $options) use ($scheduledTypeFilter) {
                $from     = date('Y-m-d');
                $to       = date('Y-m-d', strtotime('+1 year'));
                $meetings = $this->getMeetingsQuery->execute($from, $to, null, $scheduledTypeFilter);
                $choices  = [];
                foreach ($meetings as $meeting) {
                    $choices[$meeting->getTitle()] = $meeting->getId();
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

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (empty($view->vars['data'])) {
            return;
        }

        // if the selected meeting was not found in future meetings query try to get this meeting by id
        if (!in_array($view->vars['data'], $options['choices'], true)) {
            $meetingId = $view->vars['data'];
            try {
                $meeting                 = $this->getMeetingQuery->execute($meetingId);
                $view->vars['choices'][] = new ChoiceView($meetingId, $meetingId, $meeting->getTitle());
                $view->vars['value']     = $view->vars['data'];
            } catch (\Exception $e) {
                // do nothing if the meeting doesn't exist
            }
        }
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

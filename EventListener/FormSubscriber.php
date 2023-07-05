<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\SubmissionEvent;
use Mautic\FormBundle\FormEvents;
use MauticPlugin\CaWebexBundle\Api\Command\CreateInviteeCommand;
use MauticPlugin\CaWebexBundle\Form\Type\SubmitActionWebexInviteType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    private CreateInviteeCommand $createInviteeCommand;

    public function __construct(CreateInviteeCommand $createInviteeCommand)
    {
        $this->createInviteeCommand = $createInviteeCommand;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::FORM_ON_BUILD                 => ['onFormBuilder', 0],
            FormEvents::ON_EXECUTE_SUBMIT_ACTION      => ['onFormSubmitActionInvite', 0],
        ];
    }

    public function onFormBuilder(FormBuilderEvent $event): void
    {
        $event->addSubmitAction('cawebex.invite', [
            'group'              => 'mautic.form.actions',
            'label'              => 'cawebex.form.action.webex_invite',
            'description'        => 'cawebex.form.action.webex_invite.desc',
            'formType'           => SubmitActionWebexInviteType::class,
            'eventName'          => FormEvents::ON_EXECUTE_SUBMIT_ACTION,
            'allowCampaignForm'  => true,
        ]);
    }

    public function onFormSubmitActionInvite(SubmissionEvent $event): void
    {
        if (!$event->checkContext('cawebex.invite')) {
            return;
        }

        $config        = $event->getActionConfig();
        $lead          = $event->getSubmission()->getLead();

        $meetingId   = $config['meeting'] ?? null;
        $leadEmail   = $lead->getEmail();
        $displayName = $lead->getName();

        $this->createInviteeCommand->execute($meetingId, $leadEmail, $displayName);
    }
}

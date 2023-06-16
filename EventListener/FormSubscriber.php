<?php

namespace MauticPlugin\CaWebexBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\SubmissionEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Exception\ConfigurationException;
use MauticPlugin\CaWebexBundle\Form\Type\SubmitActionWebexInviteType;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{

    private IntegrationHelper $integrationHelper;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::FORM_ON_BUILD            => ['onFormBuilder', 0],
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

        $meetingId = $config['meeting'] ?? null;
        $leadEmail = $lead->getEmail();
        $displayName = $lead->getName();

        /** @var WebexIntegration $integration */
        $integration = $this->integrationHelper->getIntegrationObject('Webex');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished() || !$meetingId || !$leadEmail) {
            throw new ConfigurationException();
        }

        $api = $integration->getApi();
        $api->createMeetingInvitee($meetingId, $leadEmail, $displayName);
    }

}
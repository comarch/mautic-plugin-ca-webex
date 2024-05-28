<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\SubmissionEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\Api\Command\CreateInviteeCommand;
use MauticPlugin\CaWebexBundle\Api\Command\CreateRegistrantCommand;
use MauticPlugin\CaWebexBundle\Exception\UserIsAlreadyInvitedException;
use MauticPlugin\CaWebexBundle\Exception\UserIsAlreadyRegisteredException;
use MauticPlugin\CaWebexBundle\Form\Type\SubmitActionWebexInviteType;
use MauticPlugin\CaWebexBundle\Form\Type\SubmitActionWebexRegisterType;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CreateInviteeCommand $createInviteeCommand,
        private CreateRegistrantCommand $createRegistrantCommand,
        private LeadModel $leadModel,
        private LoggerInterface $logger
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::FORM_ON_BUILD                 => ['onFormBuilder', 0],
            FormEvents::ON_EXECUTE_SUBMIT_ACTION      => [
                ['onFormSubmitActionInvite', 0],
                ['onFormSubmitActionRegister', 0],
            ],
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

        $event->addSubmitAction('cawebex.register', [
            'group'              => 'mautic.form.actions',
            'label'              => 'cawebex.form.action.webex_register',
            'description'        => 'cawebex.form.action.webex_register.desc',
            'formType'           => SubmitActionWebexRegisterType::class,
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

        try {
            $this->createInviteeCommand->execute($meetingId, $leadEmail, $displayName);
            $this->leadModel->modifyTags($lead, ["webex-{$meetingId}-invited"]);
        } catch (UserIsAlreadyInvitedException $e) {
            // just log the warning if user is already invited
            $this->logger->warning($e->getMessage());
        } catch (ApiErrorException $e) {
            $this->handleApiError($e);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    public function onFormSubmitActionRegister(SubmissionEvent $event): void
    {
        if (!$event->checkContext('cawebex.register')) {
            return;
        }

        $config        = $event->getActionConfig();
        $lead          = $event->getSubmission()->getLead();
        $meetingId     = $config['meeting'] ?? null;

        try {
            $this->createRegistrantCommand->execute($meetingId, $lead);
            $this->leadModel->modifyTags($lead, ["webex-{$meetingId}-registered"]);
        } catch (UserIsAlreadyRegisteredException $e) {
            // just log the warning if user is already registered
            $this->logger->warning($e->getMessage());
        } catch (ApiErrorException $e) {
            $this->handleApiError($e);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function handleApiError(ApiErrorException $e): void
    {
        $this->logger->error($e->getMessage());
        $response = json_decode($e->getMessage(), true);
        $message  = 'CaWebex: ';
        if ($response) {
            $message .= $response['errors'][0]['description'] ?? $response['message'] ?? 'Unknown';
        }
        throw new BadRequestHttpException($message);
    }

    private function handleException(\Exception $e): void
    {
        $this->logger->error($e->getMessage());
        throw $e;
    }
}

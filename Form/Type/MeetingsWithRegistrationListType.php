<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

/**
 * @template T of object
 *
 * @extends MeetingsListType<T>
 */
class MeetingsWithRegistrationListType extends MeetingsListType
{
    /**
     * @return array<string, mixed>
     */
    protected function getChoices(): array
    {
        $scheduledTypeFilter = $this->webexIntegrationHelper->getScheduledTypeSetting();
        $from                = date('Y-m-d');
        $to                  = date('Y-m-d', strtotime('+1 year'));
        $meetings            = $this->getMeetingsQuery->execute($from, $to, null, $scheduledTypeFilter);

        // pull meetings list for other accounts from the organization
        $extraHosts = $this->webexIntegrationHelper->getExtraHostsSetting();
        foreach ($extraHosts as $extraHost) {
            $meetings = array_merge($meetings, $this->getMeetingsQuery->execute(
                from: $from,
                to: $to,
                scheduledType: $scheduledTypeFilter,
                hostEmail: $extraHost
            ));
        }

        $choices             = [];
        foreach ($meetings as $meeting) {
            if ($meeting->hasRegistration()) {
                $choices[$meeting->getTitle()] = $meeting->getId();
            }
        }

        return $choices;
    }
}

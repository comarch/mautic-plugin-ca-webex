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
        $choices             = [];
        foreach ($meetings as $meeting) {
            if ($meeting->hasRegistration()) {
                $choices[$meeting->getTitle()] = $meeting->getId();
            }
        }

        return $choices;
    }
}

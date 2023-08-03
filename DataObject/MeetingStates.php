<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\DataObject;

class MeetingStates
{
    public const ACTIVE      = 'active';
    public const SCHEDULED   = 'scheduled';
    public const READY       = 'ready';
    public const LOBBY       = 'lobby';
    public const IN_PROGRESS = 'inProgress';
    public const ENDED       = 'ended';
    public const MISSED      = 'missed';
    public const EXPIRED     = 'expired';
}

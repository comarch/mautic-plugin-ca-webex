<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\DataObject;

use DateTime;

class MeetingStates
{
    const ACTIVE = 'active';
    const SCHEDULED = 'scheduled';
    const READY = 'ready';
    const LOBBY = 'lobby';
    const IN_PROGRESS = 'inProgress';
    const ENDED = 'ended';
    const MISSED = 'missed';
    const EXPIRED = 'expired';

}
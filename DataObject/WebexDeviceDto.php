<?php

namespace MauticPlugin\CaWebexBundle\DataObject;

use DateTime;

class WebexDeviceDto
{
    private string $correlationId;
    private string $deviceType;
    private DateTime $joinedTime;
    private DateTime $leftTime;
    private int $durationSecond;

    public function __construct(array $data) {
        $this->correlationId = $data['correlationId'];
        $this->deviceType = $data['deviceType'];
        $this->joinedTime = new DateTime($data['joinedTime']);
        $this->leftTime = new DateTime($data['leftTime']);
        $this->durationSecond = (int) $data['durationSecond'];
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function getDeviceType(): string
    {
        return $this->deviceType;
    }

    public function getJoinedTime(): DateTime
    {
        return $this->joinedTime;
    }

    public function getLeftTime(): DateTime
    {
        return $this->leftTime;
    }

    public function getDurationSecond(): int
    {
        return $this->durationSecond;
    }

}
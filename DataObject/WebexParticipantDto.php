<?php

namespace MauticPlugin\CaWebexBundle\DataObject;

use DateTime;

class WebexParticipantDto
{
    private string $id;
    private bool $host;
    private bool $coHost;
    private bool $spaceModerator;
    private string $email;
    private string $displayName;
    private bool $invitee;
    private bool $muted;
    private string $state;
    private DateTime $joinedTime;
    private DateTime $leftTime;
    private string $siteUrl;
    private string $meetingId;
    private string $hostEmail;
    private DateTime $meetingStartTime;
    private array $devices;

    /**
     * @throws \Exception
     */
    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->host = (bool) $data['host'];
        $this->coHost = (bool) $data['coHost'];
        $this->spaceModerator = (bool) $data['spaceModerator'];
        $this->email = $data['email'];
        $this->displayName = $data['displayName'];
        $this->invitee = (bool) $data['invitee'];
        $this->muted = (bool) $data['muted'];
        $this->state = $data['state'];
        $this->joinedTime = new DateTime($data['joinedTime']);
        $this->leftTime = new DateTime($data['leftTime']);
        $this->siteUrl = $data['siteUrl'];
        $this->meetingId = $data['meetingId'];
        $this->hostEmail = $data['hostEmail'];
        $this->meetingStartTime = new DateTime($data['meetingStartTime']);

        $this->devices = [];
        foreach ($data['devices'] as $deviceData) {
            $device = new WebexDeviceDto($deviceData);
            $this->devices[] = $device;
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isHost(): bool
    {
        return $this->host;
    }

    public function isCoHost(): bool
    {
        return $this->coHost;
    }

    public function isSpaceModerator(): bool
    {
        return $this->spaceModerator;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function isInvitee(): bool
    {
        return $this->invitee;
    }

    public function isMuted(): bool
    {
        return $this->muted;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getJoinedTime(): DateTime
    {
        return $this->joinedTime;
    }

    public function getLeftTime(): DateTime
    {
        return $this->leftTime;
    }

    public function getSiteUrl(): string
    {
        return $this->siteUrl;
    }

    public function getMeetingId(): string
    {
        return $this->meetingId;
    }

    public function getHostEmail(): string
    {
        return $this->hostEmail;
    }

    public function getMeetingStartTime(): DateTime
    {
        return $this->meetingStartTime;
    }

    /**
     * @return array<int, WebexDeviceDto>
     */
    public function getDevices(): array
    {
        return $this->devices;
    }

}
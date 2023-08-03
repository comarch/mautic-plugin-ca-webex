<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\DataObject;

class ParticipantDto
{
    private string $id;
    private bool $host;
    private bool $coHost;
    private string $email;
    private string $displayName;
    private bool $invitee;
    private bool $muted;
    private string $state;
    private \DateTime $joinedTime;
    private \DateTime $leftTime;
    private string $meetingId;
    private \DateTime $meetingStartTime;

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->id               = $data['id'];
        $this->host             = (bool) $data['host'];
        $this->coHost           = (bool) $data['coHost'];
        $this->email            = $data['email'];
        $this->displayName      = $data['displayName'];
        $this->invitee          = (bool) $data['invitee'];
        $this->muted            = (bool) $data['muted'];
        $this->state            = $data['state'];
        $this->joinedTime       = new \DateTime($data['joinedTime']);
        $this->leftTime         = new \DateTime($data['leftTime']);
        $this->meetingId        = $data['meetingId'];
        $this->meetingStartTime = new \DateTime($data['meetingStartTime']);
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

    public function getJoinedTime(): \DateTime
    {
        return $this->joinedTime;
    }

    public function getLeftTime(): \DateTime
    {
        return $this->leftTime;
    }

    public function getMeetingId(): string
    {
        return $this->meetingId;
    }

    public function getMeetingStartTime(): \DateTime
    {
        return $this->meetingStartTime;
    }

    public function isGuest(): bool
    {
        return str_ends_with($this->email, '@guest.webex.localhost');
    }
}

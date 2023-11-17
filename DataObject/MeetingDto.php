<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\DataObject;

class MeetingDto
{
    private string $id;
    private ?string $meetingSeriesId;
    private ?string $meetingNumber;
    private string $title;
    private string $meetingType;
    private string $state;
    private string $timezone;
    private \DateTime $start;
    private \DateTime $end;
    private string $scheduledType;
    /**
     * @var array<string, mixed>|null
     */
    private ?array $registration;

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->id              = $data['id'];
        $this->meetingSeriesId = $data['meetingSeriesId'] ?? null;
        $this->meetingNumber   = $data['meetingNumber'] ?? null;
        $this->title           = $data['title'];
        $this->meetingType     = $data['meetingType'];
        $this->state           = $data['state'];
        $this->timezone        = $data['timezone'];
        $this->start           = new \DateTime($data['start']);
        $this->end             = new \DateTime($data['end']);
        $this->scheduledType   = $data['scheduledType'];
        $this->registration    = $data['registration'] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMeetingSeriesId(): ?string
    {
        return $this->meetingSeriesId;
    }

    public function getMeetingNumber(): ?string
    {
        return $this->meetingNumber;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMeetingType(): string
    {
        return $this->meetingType;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getStart(): \DateTime
    {
        return $this->start;
    }

    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    public function getScheduledType(): string
    {
        return $this->scheduledType;
    }

    public function hasEnded(): bool
    {
        return in_array($this->state, ['ended', 'expired']);
    }

    public function hasRegistration(): bool
    {
        return is_array($this->registration);
    }
}

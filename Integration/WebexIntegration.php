<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\DataObject\ScheduledTypes;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;

/** @phpstan-ignore-next-line */
class WebexIntegration extends AbstractIntegration
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'Webex';
    }

    public function getDisplayName()
    {
        return 'Webex';
    }

    public function getIcon(): string
    {
        return 'plugins/CaWebexBundle/Assets/img/icon.png';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    public function getRequiredKeyFields()
    {
        return [
            'client_id'     => 'mautic.integration.keyfield.clientid',
            'client_secret' => 'mautic.integration.keyfield.clientsecret',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int, string>
     */
    public function getSupportedFeatures()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'oauth2';
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return 'https://webexapis.com/v1';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAccessTokenUrl()
    {
        return $this->getApiUrl().'/access_token';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationUrl()
    {
        return $this->getApiUrl().'/authorize';
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        $keys = $this->getKeys();

        return $keys[$this->getAuthTokenKey()];
    }

    /**
     * @param bool $inAuthorization
     *
     * @return string|null
     */
    public function getBearerToken($inAuthorization = false)
    {
        if (!$inAuthorization && isset($this->keys[$this->getAuthTokenKey()])) {
            return $this->keys[$this->getAuthTokenKey()];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int, string>
     */
    public function getRefreshTokenKeys()
    {
        return ['refresh_token', 'expires'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthScope()
    {
        return 'spark:kms meeting:admin_schedule_write meeting:schedules_read meeting:participants_read meeting:admin_participants_read meeting:participants_write meeting:admin_schedule_read meeting:schedules_write';
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $data
     */
    public function prepareResponseForExtraction($data)
    {
        if (is_array($data) && isset($data['expires_in'])) {
            $data['expires'] = $data['expires_in'] + time();
        }

        return $data;
    }

    public function getApi(): WebexApi
    {
        return new WebexApi($this);
    }

    /**
     * @param Form|FormBuilder $builder
     * @param array<string, mixed>            $data
     * @param string           $formArea
     */
    public function appendToForm(&$builder, $data, $formArea): void
    {
        if ('features' == $formArea) {
            $builder->add(
                'scheduled_type',
                ChoiceType::class,
                [
                    'choices' => [
                        'cawebex.form.features.scheduled_type.meeting'  => ScheduledTypes::MEETING,
                        'cawebex.form.features.scheduled_type.webinar' => ScheduledTypes::WEBINAR,
                    ],
                    'label'             => 'cawebex.form.features.scheduled_type.label',
                    'label_attr'        => ['class' => 'control-label'],
                    'required'          => false,
                    'multiple'          => false,
                    'attr'              => [
                        'class' => 'form-control frequency',
                    ],
                ]
            );
        }
    }
}

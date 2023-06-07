<?php

namespace MauticPlugin\CaWebexBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\CaWebexBundle\Api\WebexApi;


class WebexIntegration extends AbstractIntegration
{
    protected $auth;

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
     * @return array
     */
    public function getRequiredKeyFields()
    {
        return [
            'client_id'     => 'mautic.integration.keyfield.clientid',
            'client_secret' => 'mautic.integration.keyfield.clientsecret',
        ];
    }

    /**
     * @return array
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
     * Get the keys for the refresh token and expiry.
     *
     * @return array
     */
    public function getRefreshTokenKeys()
    {
        return ['refresh_token', 'expires'];
    }

    /**
     * @return string
     */
    public function getAuthScope()
    {
        return 'spark:kms meeting:admin_schedule_write meeting:schedules_read meeting:participants_read meeting:admin_participants_read meeting:participants_write meeting:admin_schedule_read meeting:schedules_write';
    }

    /**
     * {@inheritdoc}
     *
     * @param $data
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
}

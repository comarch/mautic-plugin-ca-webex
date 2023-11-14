<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Helper;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\Exception\ConfigurationException;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;

class WebexIntegrationHelper
{
    private IntegrationHelper $integrationHelper;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function getIntegration(): WebexIntegration
    {
        /** @var WebexIntegration|false $integration */
        $integration = $this->integrationHelper->getIntegrationObject('Webex');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new ConfigurationException();
        }

        return $integration;
    }

    public function getApi(): WebexApi
    {
        return $this->getIntegration()->getApi();
    }

    public function getScheduledTypeSetting(): ?string
    {
        $settings = $this->getIntegration()->getIntegrationSettings()->getFeatureSettings();

        return $settings['scheduled_type'] ?? null;
    }

}

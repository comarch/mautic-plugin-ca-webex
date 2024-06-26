<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Helper;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\Exception\ConfigurationException;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;

class WebexIntegrationHelper
{
    public function __construct(private IntegrationHelper $integrationHelper)
    {
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

    /**
     * @return array<int, string>
     */
    public function getExtraHostsSetting(): array
    {
        $settings   = $this->getIntegration()->getIntegrationSettings()->getFeatureSettings();
        $extraHosts = [];
        if (!empty($settings)) {
            foreach (explode("\r\n", $settings['extra_hosts'] ?? '') as $host) {
                $host = trim($host);
                if (!empty($host) && filter_var($host, FILTER_VALIDATE_EMAIL)) {
                    $extraHosts[] = trim($host);
                }
            }
        }

        return $extraHosts;
    }
}

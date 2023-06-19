<?php

namespace MauticPlugin\CaWebexBundle\Helper;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\Exception\ConfigurationException;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;

class WebexApiHelper
{
    private IntegrationHelper $integrationHelper;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function getApi(): WebexApi
    {
        /** @var WebexIntegration $integration */
        $integration = $this->integrationHelper->getIntegrationObject('Webex');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new ConfigurationException();
        }

        return $integration->getApi();
    }

}
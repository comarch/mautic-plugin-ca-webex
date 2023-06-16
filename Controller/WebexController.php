<?php

namespace MauticPlugin\CaWebexBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Exception\ConfigurationException;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;

class WebexController extends FormController
{

    public function indexAction(IntegrationHelper $integrationHelper)
    {
        /** @var WebexIntegration $integration */
        $integration = $integrationHelper->getIntegrationObject('Webex');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new ConfigurationException();
        }

        $api = $integration->getApi();
        $response = $api->getFutureMeetings();

        return $this->delegateView([
            'viewParameters'  => [
                'items' => $response['items']
            ],
            'contentTemplate' => '@CaWebex/Webex/index.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_webex_index',
                'route'         => $this->generateUrl('mautic_webex_index'),
                'mauticContent' => 'tags',
            ],
        ]);
    }

}

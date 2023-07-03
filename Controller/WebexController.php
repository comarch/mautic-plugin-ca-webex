<?php

namespace MauticPlugin\CaWebexBundle\Controller;

use DateTime;
use Mautic\CoreBundle\Controller\FormController;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;
use Twilio\Exceptions\ConfigurationException;

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

        $date = new DateTime();
        $date->modify('-1 month');
        $from = $date->format('Y-m-d');
        $date->modify('+6 months');
        $to = $date->format('Y-m-d');

        $response = $api->getMeetings($from, $to);

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

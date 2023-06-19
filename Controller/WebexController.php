<?php

namespace MauticPlugin\CaWebexBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use MauticPlugin\CaWebexBundle\Api\Query\GetFutureMeetingsQuery;

class WebexController extends FormController
{

    public function indexAction(GetFutureMeetingsQuery $getFutureMeetingsQuery)
    {

        $response = $getFutureMeetingsQuery->execute();

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

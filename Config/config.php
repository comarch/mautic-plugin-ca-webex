<?php

return [
    'name'        => 'CA Webex',
    'description' => 'Comarch Webex',
    'author'      => 'Comarch',
    'version'     => '1.0.0',
    'services'    => [
        'integrations' => [
            'mautic.integration.webex' => [
                'class'       => \MauticPlugin\CaWebexBundle\Integration\WebexIntegration::class,
                'arguments'   => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
    ],
];

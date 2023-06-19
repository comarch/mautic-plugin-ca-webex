<?php

namespace MauticPlugin\CaWebexBundle\Api\Command;

use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;

abstract class AbstractCommand
{
    protected WebexApiHelper $apiHelper;

    public function __construct(WebexApiHelper $webexApiHelper)
    {
        $this->apiHelper = $webexApiHelper;
    }
}
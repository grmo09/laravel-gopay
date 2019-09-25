<?php

namespace grmo09\LaravelGoPay;

use GoPay\Http\Log\Logger as GoPayLogger;
use GoPay\Http\Request;
use GoPay\Http\Response;

/**
 * Class Logger
 * @package grmo09\LaravelGoPay
 */
class Logger implements GoPayLogger
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function logHttpCommunication(Request $request, Response $response)
    {
        \GoPay::logHttpCommunication($request, $response);
    }
}
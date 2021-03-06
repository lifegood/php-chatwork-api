<?php

namespace Polidog\Chatwork;

use GuzzleHttp\Exception\GuzzleException;
use Polidog\Chatwork\Exception\NoSupportApiException;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * @param $method
     *
     * @return Api\Me
     *
     * @throws NoSupportApiException
     */
    public function api($method);

    /**
     * @param       $method
     * @param       $path
     * @param array $options
     *
     * @return mixed
     * @return array
     *
     * @throws GuzzleException
     */
    public function request($method, $path, array $options = []);
}

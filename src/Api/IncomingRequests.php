<?php


namespace Polidog\Chatwork\Api;


use Polidog\Chatwork\ClientInterface;
use Polidog\Chatwork\Entity\Factory\FactoryInterface;
use Polidog\Chatwork\Entity\Factory\IncomingRequestsFactory;

class IncomingRequests extends AbstractApi
{
    public function __construct(ClientInterface $client, FactoryInterface $factory = null)
    {
        assert($factory instanceof IncomingRequestsFactory);
        parent::__construct($client, $factory);
    }


    public function show()
    {
        return $this->factory->collection(
            $this->client->request('GET','incoming_requests')
        );
    }

    public function accept($requestId)
    {
        return $this->factory->entity(
            $this->client->request('PUT', "incoming_requests/{$requestId}")
        );
    }

    public function reject($requestId)
    {
        $this->client->request('DELETE', "incoming_requests/{$requestId}");
    }
}

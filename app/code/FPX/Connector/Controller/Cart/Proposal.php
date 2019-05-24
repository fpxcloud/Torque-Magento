<?php
/**
 * FPX Connector Cart Proposal controller
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */
namespace FPX\Connector\Controller\Cart;

use Magento\Framework\App\Action\Action;

class Proposal extends Action
{
    /**
     * @var \FPX\Connector\Model\Api\Client
     */
    private $client;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \FPX\Connector\Model\Api\Client $client
    ) {
        parent::__construct($context);

        $this->client = $client;
    }

    /**
     * Proposal action
     *
     * @return void
     */
    public function execute()
    {
        $response = $this->client->getProposal();

        $this->getResponse()
            ->setHttpResponseCode($response->getStatusCode())
            ->setHeaders($response->getHeaders())
            ->setContent($response->getContent())
            ->send();
    }
}

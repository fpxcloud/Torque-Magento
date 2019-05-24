<?php

/**
 * FPX Connector API wrapper
 */

namespace FPX\Connector\Model\Api;

class Client
{
    const URI_GET_CONFIGURATION_DATA = '/rs/%s/anonymous/configurationData';
    const URI_GET_PROPOSAL_QUOTE_SUMMARY = '/rs/%s/cpq/proposal/Quote_Summary/printable';
    const URI_MANIPULATE_OBJECT = '/rs/%s/cpq/%s';

    /**
     * @var \Zend\Http\ClientFactory
     */
    private $clientFactory;

    /**
     * @var \Zend\Http\Client
     */
    private $client;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \FPX\Connector\Helper\Config
     */
    private $config;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $rfst;

    /**
     * @var array
     */
    private $response = [];

    /**
     * Client constructor.
     * @param \Magento\Customer\Model\Session\Proxy $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \FPX\Connector\Helper\Config $config
     * @param \Zend\Http\ClientFactory $clientFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session\Proxy $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \FPX\Connector\Helper\Config $config,
        \Zend\Http\ClientFactory $clientFactory
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->config = $config;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param $productId
     * @return array
     */
    public function getConfigurationData($productId)
    {
        $this->login();

        $url = sprintf(self::URI_GET_CONFIGURATION_DATA, $this->config->getFpxApiVer());
        $this->get($url, ['uid' => $productId]);

        return $this->response;
    }

    /**
     * @return \Zend\Http\Response
     */
    public function getProposal()
    {
        $this->login();

        $url = sprintf(self::URI_GET_PROPOSAL_QUOTE_SUMMARY, $this->config->getFpxApiVer());
        $this->get($url, ['associatedId' => $this->customerSession->getFpxQuoteId()]);

        return $this->getClient()->getResponse();
    }

    /**
     * @param string|int $objectId
     * @param array $data
     */
    public function updateObject($objectId, array $data)
    {
        $this->login();
        $url = sprintf(self::URI_MANIPULATE_OBJECT, $this->config->getFpxApiVer(), $objectId);

        $this->put($url, $data);
    }

    /**
     * @param string|int $objectId
     */
    public function deleteObject($objectId)
    {
        $this->login();

        $url = sprintf(self::URI_MANIPULATE_OBJECT, $this->config->getFpxApiVer(), $objectId);

        $this->delete($url);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return bool
     */
    public function get($uri, $params = [])
    {
        $this->getClient()->setUri($this->getUri($uri));
        $this->getClient()->setMethod('GET');
        $this->setQueryParams($params);

        return $this->makeRequest();
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $params
     * @return bool
     */
    public function post($uri, $data = [], $params = [])
    {
        $this->getClient()->setUri($this->getUri($uri));
        $this->getClient()->setMethod('POST');
        $this->getClient()->getRequest()->setContent(json_encode($data));
        $this->setQueryParams($params);

        return $this->makeRequest();
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $params
     * @return bool
     */
    public function put($uri, $data = [], $params = [])
    {
        $this->getClient()->setUri($this->getUri($uri));
        $this->getClient()->setMethod('PUT');
        $this->getClient()->getRequest()->setContent(json_encode($data));
        $this->setQueryParams($params);

        return $this->makeRequest();
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $params
     * @return bool
     */
    public function delete($uri, $data = [], $params = [])
    {
        $this->getClient()->setUri($this->getUri($uri));
        $this->getClient()->setMethod('DELETE');
        $this->getClient()->getRequest()->setContent(json_encode($data));
        $this->setQueryParams($params);

        return $this->makeRequest();
    }

    /**
     * @return \Zend\Http\Client
     */
    private function getClient()
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $this->client = $this->clientFactory->create();
        return $this->client;
    }

    /**
     * @param array $params
     */
    private function setQueryParams($params)
    {
        if ($this->rfst) {
            $params['rfst'] = $this->rfst;
        }

        $this->getClient()->setParameterGet($params);
    }

    /**
     * @return mixed|string
     */
    private function getEndpoint()
    {
        if ($this->endpoint !== null) {
            return $this->endpoint;
        }

        $this->endpoint = $this->config->getFpxApiUrl();
        return $this->endpoint;
    }

    /**
     * @param $uri
     * @return string
     */
    private function getUri($uri)
    {
        return $this->getEndpoint() . $uri;
    }

    /**
     * @return bool
     */
    private function makeRequest()
    {
        try {
            $this->getClient()->setHeaders(['Content-Type' => 'application/json; charset=UTF-8']);
            $this->getClient()->send();
            $this->response = json_decode($this->getClient()->getResponse()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * login into the api
     * and get rfst
     */
    private function login()
    {
        if ($this->rfst) {
            return;
        }

        $url = sprintf('/rs/%s/cpq/login', $this->config->getFpxApiVer());
        $this->post($url, ['anonymous' => ['token' => $this->getToken()]]);
        $this->rfst = $this->response['rfst'];
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if (!$this->token) {
            $auth = $this->config->getAuthData();
            if ($correlationId = $this->customerSession->getFpxCorrelationId()) {
                $auth['correlationId'] = $correlationId;
            }

            $url = sprintf('/rs/%s/anonymous/token', $this->config->getFpxApiVer());
            $this->post($url, $auth);
            $this->token = $this->getClient()->getResponse()->getBody();
        }

        return $this->token;
    }
}

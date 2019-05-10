<?php
/**
 * FPX_Connector Controller
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Controller\Fpx;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * FPX_Connector Controller
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Authenticate extends Action
{
    const RESPONSE_TYPE_SUCCESS = 'success';
    const RESPONSE_TYPE_ERROR = 'error';

    const EDIT_ACTION_PARAM_VALUE = 'edit';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \FPX\Connector\Model\Api\Client
     */
    private $client;

    /**
     * Authenticate constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session\Proxy $customerSession
     * @param \FPX\Connector\Model\Api\Client $client
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session\Proxy $customerSession,
        \FPX\Connector\Model\Api\Client $client
    ) {
        $this->customerSession = $customerSession;
        $this->client = $client;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->getRequest()->getParam('action') === self::EDIT_ACTION_PARAM_VALUE
            && !$this->customerSession->getFpxCorrelationId()
        ) {
            $message = __('Your session expired. please proceed with product configuration again.');
            $this->messageManager->addErrorMessage($message);
            $result->setData([
                'status' => self::RESPONSE_TYPE_ERROR,
                'data' => $message
            ]);

            return $result;
        }

        $token = $this->client->getToken();
        $result->setData([
            'status' => self::RESPONSE_TYPE_SUCCESS,
            'data' => $token
        ]);

        return $result;
    }
}

<?php
/**
 * Observing sales_order_place_after event
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class description
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class CleanSessionAfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private $customerSession;

    /**
     * CleanSessionAfterPlaceOrder constructor.
     * @param \Magento\Customer\Model\Session\Proxy $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session\Proxy $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Clean FPX data into Customer Session after place Order
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $observer; // for suppress Generic.CodeAnalysis.UnusedFunctionParameter.Found warning

        $this->customerSession->setFpxCorrelationId(null);
        $this->customerSession->setFpxQuoteId(null);
    }
}

<?php
/**
 * Short description / title of module
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Block;

use Magento\Checkout\Block\Cart\AbstractCart;

/**
 * Class description
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class FpxCart extends AbstractCart
{
    /**
     * Check that the Proposal link is needed.
     *
     * @return bool
     */
    public function isNeedProposalLink()
    {
        return $this->_customerSession->getFpxQuoteId()
            && $this->_customerSession->getFpxCorrelationId();
    }
}

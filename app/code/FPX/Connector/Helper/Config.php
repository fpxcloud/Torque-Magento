<?php
/**
 * FPX_Connector Helper
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * FPX_Connector Helper
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Config extends AbstractHelper
{
    /**#@+
     * Constants used as keys into fpx product attributes
     *
     * @var string
     */
    const PRODUCT_ATTRIBUTE_FPX_ID          = 'fpx_id';
    const PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE = 'fpx_load_enable';
    /**#@-*/

    /**#@+
     * XPath in the configuration where FPX_Connector configured
     *
     * @var string
     */
    const XML_PATH_MODULE_ENABLE    = 'fpxconnector/general/enable';
    const XML_PATH_API_URL          = 'fpxconnector/general/api_url';
    const XML_PATH_API_VER          = 'fpxconnector/general/api_ver';
    const XML_PATH_CLIENT_ALIAS     = 'fpxconnector/general/client_alias';
    const XML_PATH_PROFILE_NAME     = 'fpxconnector/general/profile_name';
    const XML_PATH_API_KEY          = 'fpxconnector/general/api_key';
    /**#@-*/

    /**#@+
     * Template for generate CPQ URL's
     *
     * @var string
     */
    const URL_TEMPLATE_GET_TOKEN        = '%s/rs/%s/anonymous/token';
    const URL_TEMPLATE_LOGIN            = '%s/rs/%s/cpq/login';
    const URL_TEMPLATE_GET_CONFIG_DATA  = '%s/rs/%s/anonymous/configurationData';
    const URL_TEMPLATE_GET_CONFIGURATOR = '%s/ui/cpq/modules/appOnDemand/index.do';
    const URL_TEMPLATE_GET_PROPOSAL     = '%s/rs/%s/cpq/proposal/Quote_Summary/printable';
    /**#@-*/

    /**
     * Prefix to distinguish CPQ data items from Magento data items in Add To Cart request.
     */
    const PRODUCT_DATA_PREFIX = 'cpq_';

    /**
     * @var EncryptorInterface $encryptor
     */
    private $encryptor;

    /**
     * Helper constructor.
     *
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;

        parent::__construct($context);
    }

    /**
     * Get Auth data array for request
     *
     * @return array
     */
    public function getAuthData()
    {
        return [
            'clientAlias'   => $this->getClientAlias(),
            'profileName'   => $this->getProfileName(),
            'key'           => $this->getApiKey(),
        ];
    }

    /**
     * Get Auth data in Json format
     *
     * @return string
     */
    public function getAuthDataJson()
    {
        return json_encode($this->getAuthData());
    }

    /**
     * Check whether FPX Connector module is enabled
     *
     * @return mixed
     */
    public function isFpxModuleEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE);
    }

    /**
     * Get used FPX API Version
     *
     * @return mixed
     */
    public function getFpxApiVer()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_VER);
    }

    /**
     * Get used FPX API URL
     *
     * @return mixed
     */
    public function getFpxApiUrl()
    {
        /** Removing a possible slash at the end of URL */
        return rtrim($this->scopeConfig->getValue(self::XML_PATH_API_URL), '/');
    }

    /**
     * Generate URL for getting Token from FPX
     *
     * @return string
     */
    public function getFpxTokenGenerationUrl()
    {
        return $this->getFpxApiUrlWithApiVer(self::URL_TEMPLATE_GET_TOKEN);
    }

    /**
     * Generate URL for loggin in to FPX
     *
     * @return string
     */
    public function getFpxLoginUrl()
    {
        return $this->getFpxApiUrlWithApiVer(self::URL_TEMPLATE_LOGIN);
    }

    /**
     * Generate URL for getting Configuration Data from FPX
     *
     * @param array $queryParams
     * @return string
     */
    public function getFpxConfigDataUrl($queryParams = [])
    {
        return $this->getFpxApiUrlWithApiVer(
            self::URL_TEMPLATE_GET_CONFIG_DATA,
            $queryParams
        );
    }

    /**
     * Generate URL for getting Proposal from FPX
     *
     * @param array $queryParams
     * @return string
     */
    public function getFpxProposalUrl($queryParams = [])
    {
        return $this->getFpxApiUrlWithApiVer(
            self::URL_TEMPLATE_GET_PROPOSAL,
            $queryParams
        );
    }

    /**
     * Generate URL for getting iframe from FPX
     *
     * @return string
     */
    public function getProductUrlForIframe()
    {
        return sprintf(
            self::URL_TEMPLATE_GET_CONFIGURATOR,
            $this->getFpxApiUrl()
        );
    }

    /**
     * Generate FPX API url with apiUrl and apiVer by urlTemplate.
     *
     * @param $urlTemplate
     * @param array $queryParams
     * @return string
     */
    private function getFpxApiUrlWithApiVer($urlTemplate, $queryParams = [])
    {
        $url =  sprintf(
            $urlTemplate,
            $this->getFpxApiUrl(),
            $this->getFpxApiVer()
        );

        return $this->setQueryParamsToUrl($url, $queryParams);
    }

    /**
     * Set parameters to URL
     *
     * @param string $url
     * @param array $queryParams
     * @return string
     */
    public function setQueryParamsToUrl($url, $queryParams = [])
    {
        if ($queryParams) {
            $query = http_build_query($queryParams);
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * Get and decrypt API Kkey
     *
     * @return string
     */
    public function getApiKey()
    {
        $encryptedApiKey = $this->scopeConfig->getValue(self::XML_PATH_API_KEY);

        return $this->encryptor->decrypt($encryptedApiKey);
    }

    /**
     * Get Profile Name
     *
     * @return mixed
     */
    public function getProfileName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROFILE_NAME);
    }

    /**
     * Get Client Alias
     *
     * @return mixed
     */
    public function getClientAlias()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ALIAS);
    }

    /**
     * Returns prefix for CPQ product data items
     *
     * @return string
     */
    public function getProductDataPrefix()
    {
        return self::PRODUCT_DATA_PREFIX;
    }
}

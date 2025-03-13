<?php

namespace Quantilus\MobiusUser\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_SECTION = 'mobius_user/';

    /**
     * Get Mobius API Endpoint
     *
     * @return string
     */
    public function getMobiusApiEndpoint()
    {
        return $this->getConfigValue('general/mobius_api_endpoint');
    }

    /**
     * Get API Username
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->getConfigValue('general/api_username');
    }

    /**
     * Get API Password
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->getConfigValue('general/api_password');
    }

    /**
     * Get Science Interactive Tenant Code
     *
     * @return string
     */
    public function getScienceInteractiveTenantCode()
    {
        return $this->getConfigValue('general/science_iteractive_tenantcode');
    }

    /**
     * Get Config Value
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SECTION . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Versapay\Versapay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Versapay\Versapay\Gateway\Http\Client\ClientMock;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class CountryValidator extends AbstractValidator implements \Magento\Payment\Gateway\Validator\ValidatorInterface 
{

     /**
     * @var ConfigInterface
     */
    private $config;
     /**
     * @var ResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         ResultInterfaceFactory $resultFactory
    ) {
         parent::__construct($resultFactory);
        $this->config = $scopeConfig;
        $this->resultFactory = $resultFactory;
    }
     /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        
        $isValid = true;
        $storeId = $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        //echo $this->config->getValue('payment/versapay_gateway/allowspecific', $storeScope);die;


        if ((int)$this->config->getValue('payment/versapay_gateway/allowspecific', $storeId) === 1) {
            $availableCountries = explode(
                ',',
                $this->config->getValue('payment/versapay_gateway/specificcountry', $storeId)
            );
        


            if (!in_array($validationSubject['country'], $availableCountries)) {
                $isValid =  false;
            }
        }

        
        return $this->createResult($isValid);
    }
}



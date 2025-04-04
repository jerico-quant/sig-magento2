<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Versapay\Versapay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Versapay\Versapay\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
  const CODE = 'versapay_gateway';
  protected $_customerSession;
  private $quote;
  private $_scopeConfig;
  protected $_curl;
  private $checkoutSession;
  protected $customerRepositoryInterface;
  protected $messageManager;

  public function __construct(
    \Magento\Quote\Model\Quote $quote,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Framework\HTTP\Client\Curl $curl,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Customer\Model\Session $session,
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
    \Magento\Framework\Message\ManagerInterface $messageManager
  ) {
    $this->quote = $quote;
    $this->_scopeConfig = $scopeConfig;
    $this->_curl = $curl;
    $this->checkoutSession = $checkoutSession;
    $this->_customerSession = $session;
    $this->customerRepositoryInterface = $customerRepositoryInterface;
    $this->messageManager = $messageManager;
  }


  /**
   * Retrieve assoc array of checkout configuration
   *
   * @return array
   */
  public function getConfig()
  {
    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;



    $paymentMessage = $this->_scopeConfig->getValue('payment/versapay_gateway/payment_message');
    $subdomain =  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);
    $datadogApiKey = $this->_scopeConfig->getValue('payment/versapay_gateway/log_key', $storeScope);

    $sessionKey = $this->getSessionKey();
    return [
      'payment' => [
        self::CODE => [
          'transactionResults' => [
            ClientMock::SUCCESS => __('Success'),
            ClientMock::FAILURE => __('Fraud')
          ],
          'payment_message' => $paymentMessage,
          'versapay_subdomain' => $subdomain,
          'versapay_sessionkey' => isset($sessionKey['id']) ? $sessionKey['id'] : '',
          'versapay_quote_id' => $this->checkoutSession->getQuote()->getId(),
          'ach_status' => 1

        ]
      ]
    ];
  }

  public function getSessionKey()
  {

    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    $subdomain =  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);
    $apiKey = $this->_scopeConfig->getValue('payment/versapay_gateway/api_key', $storeScope);
    $apiToken = $this->_scopeConfig->getValue('payment/versapay_gateway/api_token', $storeScope);
    $ccStatus = $this->_scopeConfig->getValue('payment/versapay_gateway/cc_enabled', $storeScope);
    $achEnableStatus = $this->_scopeConfig->getValue('payment/versapay_gateway/ach_enabled', $storeScope);
    $gcEnableStatus = $this->_scopeConfig->getValue('payment/versapay_gateway/gc_enabled', $storeScope);
    $avsRules = $this->_scopeConfig->getValue('payment/versapay_gateway/avs_rules', $storeScope);
    if ($avsRules){
      $avsRules = explode(",", $this->_scopeConfig->getValue('payment/versapay_gateway/avs_rules', $storeScope));
    }
    $datadogApiKey = $this->_scopeConfig->getValue('payment/versapay_gateway/log_key', $storeScope);
    $params = [];
    $savePaymentMethodByDefault = $this->_scopeConfig->getValue('payment/versapay_gateway/save_payment_method_by_default', $storeScope) > 0;

    $walletId = false;
    if ($this->_customerSession->isLoggedIn()) {
      $customerId = $this->_customerSession->getCustomer()->getId();
      $customer = $this->customerRepositoryInterface->getById($customerId);

      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);

      if (!$customer->getCustomAttribute('versapay_walletid')) {


        $customerDataMissing  = false;
        foreach ($customerObj->getAddresses() as $address) {
          if ($address->getData('firstname') == '' || $address->getData('lastname') == '') {
            $customerDataMissing = true;
          }
        }

        $url = "https://" . $subdomain . ".versapay.com/api/v1/wallets";
        $this->_curl->setHeaders([
          'Content-Type' => 'application/json'
        ]);
        $wparams = [];
        $wparams['gatewayAuthorization']['apiToken'] = $apiToken;
        $wparams['gatewayAuthorization']['apiKey'] = $apiKey;

        $this->_curl->post($url, json_encode($wparams, JSON_UNESCAPED_SLASHES));

        //response will contain the output of curl request
        $response = $this->_curl->getBody();
        $walletId = json_decode($response, true);
        $walletId = $walletId['walletId'];
        if ($customerDataMissing) {
          $this->messageManager->addWarning(__("The customer account is missing one or more required fields, so the wallet cannot be created."));
        } else {
          $customer->setCustomAttribute('versapay_walletid', $walletId);
          $this->customerRepositoryInterface->save($customer);
        }
      } else {

        $walletId = $customer->getCustomAttribute('versapay_walletid')->getValue();
      }
    }

    $params['gatewayAuthorization']['apiToken'] = $apiToken;
    $params['gatewayAuthorization']['apiKey'] = $apiKey;

    foreach (['rejectAddressMismatch', 'rejectPostCodeMismatch', 'rejectUnknown'] as $avsRule) {
      if ($avsRules && in_array($avsRule, $avsRules)) {
        $params['options']['avsRules'][$avsRule] = true;
      } else {
        $params['options']['avsRules'][$avsRule] = false;
      }
    }

    if ($walletId) {
      $params['options']['wallet'] = [
        'id' =>  $walletId,
        'allowAdd' => true,
        'allowEdit' => true,
        'allowDelete' => true,
        'saveByDefault' => $savePaymentMethodByDefault
      ];
    }


    if ($ccStatus) {
      $params['options']['paymentTypes'][] = [
        'name' => "creditCard",
        "promoted" => false,
        "label" => "Payment Card",
        "fields" => [
          [
            "name" => "cardholderName",
            "label" => "Cardholder Name",
            "errorLabel" => "Cardholder name"
          ],
          [
            "name" => "accountNo",
            "label" => "Account Number",
            "errorLabel" => "Credit card number"
          ],
          [
            "name" => "expDate",
            "label" => "Expiration Date",
            "errorLabel" => "Expiration date"
          ],
          [
            "name" => "cvv",
            "label" => "Security code",
            "allowLabelUpdate" => false,
            "errorLabel" => "Security code"
          ]
        ]

      ];
    }

    if ($achEnableStatus) {
      $params['options']['paymentTypes'][] = [
        'name' => "ach",
        "promoted" => false,
        "label" => "Bank Account",
        "fields" => [
          [
            "name" => "accountType",
            "label" => "Account Type",
            "errorLabel" => "Account type"
          ],
          [
            "name" => "checkType",
            "label" => "Check Type",
            "errorLabel" => "Check type"
          ],
          [
            "name" => "accountHolder",
            "label" => "Account Holder",
            "errorLabel" => "Account holder name"
          ],
          [
            "name" => "routingNo",
            "label" => "Routing Number",
            "errorLabel" => "Routing number"
          ],
          [
            "name" => "achAccountNo",
            "label" => "Account Number",
            "errorLabel" => "Bank account number"
          ]
        ]
      ];
    }

    if ($gcEnableStatus) {
      $params['options']['paymentTypes'][] = [
        'name' => "giftCard",
        "promoted" => false,
        "label" => "Gift Card",
        "fields" => [
          [
            "name" => "gcAccountNo",
            "label" => "Account Number",
            "errorLabel" => "Gift card number"
          ],
          [
            "name" => "expDate",
            "label" => "Expiration Date",
            "errorLabel" => "Expiration date"
          ],
          [
            "name" => "pin",
            "label" => "PIN",
            "errorLabel" => "PIN"
          ]
        ]
      ];
    }

    if ($datadogApiKey != '') {
      $loggingService = urlencode('versapay-payment-gateway');
      $loggingEnvironment = urlencode('production');
      $loggingHost = urlencode('magento');
      $loggingVersion = urlencode('100.0.6');
      $loggingUrl = "http-intake.logs.datadoghq.com/v1/input/";
      $data['apitoken'] = $apiToken;
      $data['checkout']['step'] = 'get_sessionid';

      $logUrl = "https://" . $loggingUrl . $datadogApiKey . "?ddsource=nodejs&service=" . $loggingService . "&environment=" . $loggingEnvironment . "&host=" . $loggingHost . "&version=" . $loggingVersion;
      $this->_curl->setHeaders([
        'Content-Type' => 'application/json'
      ]);
      $this->_curl->post($logUrl, json_encode($data));
    }

    $url = "https://" . $subdomain . ".versapay.com/api/v1/sessions";
    $this->_curl->setHeaders([
      'Content-Type' => 'application/json'
    ]);

    try {
      $this->_curl->post($url, json_encode($params, JSON_UNESCAPED_SLASHES));

      //response will contain the output of curl request
      $response = $this->_curl->getBody();

      if ($datadogApiKey != '') {
        $data['sessionid'] = json_decode($response, true);
        $data['apitoken'] = $apiToken;
        $data['checkout']['step'] = 'get_sessionid_success';
        $this->_curl->setHeaders([
          'Content-Type' => 'application/json'
        ]);
        $this->_curl->post($logUrl, json_encode($data));
      }

      return json_decode($response, true);
    } catch (\Exception $e) {
      if ($datadogApiKey != '') {
        $data['checkout']['step'] = 'get_sessionid_error';
        $this->_curl->setHeaders([
          'Content-Type' => 'application/json'
        ]);
        $this->_curl->post($logUrl, json_encode($data));
      }

      return '';
    }
  }

  public function getSubdomain()
  {
    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    return  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);
  }
}

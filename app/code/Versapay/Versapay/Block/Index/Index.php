<?php

namespace Versapay\Versapay\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{

    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_scopeConfig;
    protected $lastorderData;
    protected $_curl;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\HTTP\Client\Curl $curl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->lastorderData = $this->_checkoutSession->getData();
        $this->request = $request;
        $this->_curl = $curl;
    }


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }


    // Use this method to get ID    
    public function getRealOrderId()
    {
        $lastorderId = $this->_checkoutSession->getLastOrderId();
        return $lastorderId;
    }

    public function getOrder()
    {



        $data = $this->lastorderData;
        if (isset($data['last_real_order_id'])) {
            $order = $this->_orderFactory->create()->loadByIncrementId($data['last_real_order_id']);
            $order->setState("pending")->setStatus("pending");
            $order->save();
            return $order;
        } else {
            $orderId = $this->request->getParam('order_id');
            $order = $this->_orderFactory->create()->load($orderId);
            if ($order)
                return $order;
        }
        return false;
    }



    public function getSessionKey()
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $subdomain =  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);
        $email = $this->_scopeConfig->getValue('payment/versapay_gateway/api_id', $storeScope);
        $password = $this->_scopeConfig->getValue('payment/versapay_gateway/merchant_gateway_key', $storeScope);
        $gateway = $this->_scopeConfig->getValue('payment/versapay_gateway/gateway', $storeScope);
        $account = $this->_scopeConfig->getValue('payment/versapay_gateway/account', $storeScope);


        $params['gatewayAuthorization']['gateway'] = $gateway;
        $params['gatewayAuthorization']['email'] = $email;
        $params['gatewayAuthorization']['password'] = $password;
        $params['gatewayAuthorization']['accounts'][] = ['type' => "creditCard", 'account' => $account];



        $params['options']['fields'][] = [
            'name' =>  "cardholderName",
            'label' => "Cardholder Name",
            'placeholder' => "John Doe",
            'errorLabel' => "Cardholder name"
        ];


        $params['options']['fields'][] = [
            'name' =>  "accountNo",
            'label' => "Account Number",
            'placeholder' => "4111 1111 1111 1111",
            'errorLabel' => "Credit card number"
        ];

        $params['options']['fields'][] = [
            'name' =>  "expDate",
            'label' => "Expiration Date",
            'placeholder' => "MM / YY",
            'errorLabel' => "Please check the Expiration Date"
        ];


        $params['options']['fields'][] = [
            'name' =>  "cvv",
            'label' => "Security code",
            'placeholder' => "123",
            'errorLabel' => "Enter Security Code",
            'allowLabelUpdate' => false
        ];



        $url = "https://" . $subdomain . ".versapay.com/api/v1/sessions";
        $this->_curl->setHeaders([
            'Content-Type' => 'application/json'
        ]);
        $this->_curl->post($url, json_encode($params, JSON_UNESCAPED_SLASHES));

        //response will contain the output of curl request
        $response = $this->_curl->getBody();
        return json_decode($response, true);
    }

    public function getSubdomain()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);
    }
}

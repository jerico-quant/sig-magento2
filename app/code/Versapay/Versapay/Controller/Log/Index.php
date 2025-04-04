<?php

namespace Versapay\Versapay\Controller\Log;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $request;
    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_scopeConfig;
    protected $_curl;
    protected $resultJsonFactory;
    protected $_transactionBuilder;
    protected $resultFactory;
    protected $messageManager;
    protected $cart;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory,
        \Magento\Framework\Webapi\Rest\Request  $request,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager



    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_curl = $curl;
        $this->_scopeConfig = $scopeConfig;
        $this->_transactionBuilder = $transactionBuilder;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->cart = $cart;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }


    public function execute()
    {
        $order_id = $this->_checkoutSession->getLastOrderId();
        $result = $this->resultJsonFactory->create();
        $sessionId = $this->request->getParam('session_id');

        $browserOs = $this->request->getParam('browser_os');
        $browserVersion = $this->request->getParam('browser_version');
        $checkoutStep = $this->request->getParam('checkout_initialization_step');
        $paymentMethod = $this->request->getParam('payment_method');
        
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $subdomain =  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);
        $datadogApiKey = $this->_scopeConfig->getValue('payment/versapay_gateway/log_key', $storeScope);

        if ($datadogApiKey !== '') {
            $loggingService = urlencode('versapay-payment-gateway');
            $loggingEnvironment = urlencode('production');
            $loggingHost = urlencode('magento');
            $loggingVersion = urlencode('100.0.6');
            $loggingUrl = "http-intake.logs.datadoghq.com/v1/input/";
            $data['orderid'] = $order_id;
            $data['sessionid'] = $sessionId;
            $data['subdomain'] = $subdomain;
            $data['browser']['os'] = $browserOs;
            $data['browser']['version'] = $browserVersion;
            $data['checkout']['step'] = $checkoutStep;
            $data['checkout']['paymentMethod'] = $paymentMethod;
    
            $url = "https://" . $loggingUrl . $datadogApiKey . "?ddsource=nodejs&service=". $loggingService . "&environment=" . $loggingEnvironment . "&host=". $loggingHost . "&version=" . $loggingVersion;
            $this->_curl->setHeaders([
                'Content-Type' => 'application/json'
            ]);
            $this->_curl->post($url, json_encode($data));
    
            //response will contain the output of curl request
            $response = $this->_curl->getBody();
    
            $response = json_decode($response, true);
    
            return $result->setData($response);
        }

        return '';
    }
}

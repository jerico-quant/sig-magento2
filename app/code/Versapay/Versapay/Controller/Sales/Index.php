<?php

namespace Versapay\Versapay\Controller\Sales;

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
    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Webapi\Rest\Request  $request,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
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
        $this->_logger = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
        function validateAddress($address, $street) 
        {
            if (empty($address->getFirstname())) {
                return false;
            }
            if (empty($address->getLastName())) {
                return false;
            }
            if (empty($address->getCity())) {
                return false;
            }
            if (empty($street[0])) {
                return false;
            }
            if (empty($address->getPostCode())) {
                return false;
            }
            if (empty($address->getCountryId())) {
                return false;
            }

            return true;
        }

        $order_id = $this->_checkoutSession->getLastOrderId();
        $result = $this->resultJsonFactory->create();

        $req_data = $this->request->getPost();
        // echo "<script>console.log(" . $req_data . ");</script>";

        $payments = $req_data['data']['payments'];
        $sessionId = $req_data['data']['session_id'];
        $quoteId = $req_data['data']['quote_id'];
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $paymentsTotal = 0;
        $paymentsArray = array();
        for ($i = 0; $i < count($payments); $i++) {
            $paymentsTotal += (float)$payments[$i]['amount'];
            $paymentType = $payments[$i]['payment_type'];
            $paymentArray = array(
                "type" => $paymentType,
                "token" => $payments[$i]['token'],
                "amount" => (float)$payments[$i]['amount'],
                "capture" => ($paymentType != 'creditCard') ? true : false
            );

            switch($paymentType) {
                case 'ach':
                    if ($this->_scopeConfig->getValue('payment/versapay_gateway/ach_settlement_token', $storeScope) != '') {
                        $paymentArray["settlementToken"] = $this->_scopeConfig->getValue('payment/versapay_gateway/ach_settlement_token', $storeScope);
                        break;
                    }
                case 'creditCard':
                    if ($this->_scopeConfig->getValue('payment/versapay_gateway/cc_settlement_token', $storeScope) != '') {
                        $paymentArray["settlementToken"] = $this->_scopeConfig->getValue('payment/versapay_gateway/cc_settlement_token', $storeScope);
                        break;
                    }
            }

            $paymentsArray[] = $paymentArray;
        }     

        $quote = $this->cart->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $baddress = $billingAddress->getStreet();
        $shippingAddress = $quote->getShippingAddress();
        $saddress = $shippingAddress->getStreet();
        $grandTotal = $quote->getGrandTotal();
        $orderEmail = $billingAddress->getEmail() ?? $req_data['data']['guest_email'];

        if (!validateAddress($billingAddress, $baddress)) {
            $this->_logger->debug('Required billing address fields are null or empty.');
            $response = json_decode('{"orderId": false }', true);
            return $result->setData($response);
        }

        if ($paymentsTotal != $grandTotal) {
            $this->_logger->debug('Payment total does not equal grand total.');
            $response = json_decode('{"orderId": false }', true);
            return $result->setData($response);
        }

        $payment = $this->_checkoutSession->getQuote()->getPayment();

        $data['customerNumber'] = ($quote->getCustomer()->getId()) ? $quote->getCustomer()->getId() : "";
        $data['orderNumber'] = $quote->getId();
        $data['purchaseOrderNumber'] = ($payment->getPoNumber()) ? $payment->getPoNumber() : " ";
        $data['shippingAgentNumber'] = ($shippingAddress->getShippingMethod()) ? $shippingAddress->getShippingMethod() : " ";
        $data['shippingAgentServiceNumber'] = ($shippingAddress->getShippingMethod()) ? $shippingAddress->getShippingMethod() : " ";
        $data['shippingAgentDescription'] = ($shippingAddress->getShippingDescription()) ? $shippingAddress->getShippingDescription() : " ";
        $data['shippingAgentServiceDescription'] = ($shippingAddress->getShippingDescription()) ? $shippingAddress->getShippingDescription() : " ";
        $data['currency'] = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $data['billingAddress']['contactFirstName'] = $billingAddress->getFirstname();
        $data['billingAddress']['contactLastName'] = $billingAddress->getLastname();
        $data['billingAddress']['companyName'] = ($billingAddress->getCompany()) ? $billingAddress->getCompany() : " ";
        $data['billingAddress']['address1'] = $baddress[0];
        $data['billingAddress']['address2'] = isset($baddress[1]) ? "$baddress[1]" : "";
        $data['billingAddress']['city'] = $billingAddress->getCity();
        $data['billingAddress']['stateOrProvince'] = is_string($billingAddress->getRegionCode()) ? $billingAddress->getRegionCode() : "";
        $data['billingAddress']['postCode'] = $billingAddress->getPostcode();
        $data['billingAddress']['country'] = $billingAddress->getCountryId();
        $data['billingAddress']['phone'] = $billingAddress->getTelephone();
        $data['billingAddress']['email'] =  $orderEmail;

        if (!validateAddress($shippingAddress, $saddress)) {
            $this->_logger->debug('Required shipping address fields are null or empty, using billing address.');
            $data['shippingAddress']['contactFirstName'] = $billingAddress->getFirstname();
            $data['shippingAddress']['contactLastName'] = $billingAddress->getLastname();
            $data['shippingAddress']['companyName'] = ($billingAddress->getCompany()) ? $billingAddress->getCompany() : " ";
            $data['shippingAddress']['address1'] = $baddress[0];
            $data['shippingAddress']['address2'] = isset($baddress[1]) ? "$baddress[1]" : "";
            $data['shippingAddress']['city'] = $billingAddress->getCity();
            $data['shippingAddress']['stateOrProvince'] = is_string($billingAddress->getRegionCode()) ? $billingAddress->getRegionCode() : "";
            $data['shippingAddress']['postCode'] = $billingAddress->getPostcode();
            $data['shippingAddress']['country'] = $billingAddress->getCountryId();
            $data['shippingAddress']['phone'] = $billingAddress->getTelephone();
            $data['shippingAddress']['email'] =  $orderEmail;
        } else {
            $data['shippingAddress']['contactFirstName'] = $shippingAddress->getFirstname();
            $data['shippingAddress']['contactLastName'] = $shippingAddress->getLastname();
            $data['shippingAddress']['companyName'] = ($shippingAddress->getCompany()) ? $shippingAddress->getCompany() : " ";
            $data['shippingAddress']['address1'] = $saddress[0];
            $data['shippingAddress']['address2'] = isset($saddress[1]) ? "$saddress[1]" : "";
            $data['shippingAddress']['city'] = $shippingAddress->getCity();
            $data['shippingAddress']['stateOrProvince'] = is_string($shippingAddress->getRegionCode()) ? $shippingAddress->getRegionCode() : "";
            $data['shippingAddress']['postCode'] = $shippingAddress->getPostcode();
            $data['shippingAddress']['country'] = $shippingAddress->getCountryId();
            $data['shippingAddress']['phone'] = $shippingAddress->getTelephone();
            $data['shippingAddress']['email'] = $orderEmail;
        }

        $items = [];
        foreach ($quote->getAllItems() as $item) {
            $items[] = ['type' => 'Item', 'number' => $item->getSku(), 'description' => $item->getName(), 'price' => round($item->getPrice(), 2), 'quantity' => round($item->getQty()), 'discount' => round($item->getDiscountAmount(), 2)];
            //break;
        }

        $data['lines'] = $items;
        $data['shippingAmount'] = round($shippingAddress->getShippingAmount(), 2);
        $data['discountAmount'] = (float)$quote->getDiscountAmount();
        $data['taxAmount'] = round($shippingAddress->getTaxAmount(), 2);
        $data['payments'] = $paymentsArray;

        $subdomain =  $this->_scopeConfig->getValue('payment/versapay_gateway/subdomain', $storeScope);

        $url = "https://" . $subdomain . ".versapay.com/api/v1/sessions/" . $sessionId . "/sales";

        $this->_curl->setHeaders(['Content-Type' => 'application/json']);
        $this->_curl->post($url, json_encode($data));

        //response will contain the output of curl request
        $response = $this->_curl->getBody();
        $response = json_decode($response, true);

        return $result->setData($response);
    }

    public function saveTransactionDetails($order, $paymentData)
    {
        //get payment object from order object
        $payment = $order->getPayment();
        $payment->setTransactionId(htmlentities($paymentData['payment']['approvalCode']));
        $payment->setIsTransactionClosed(0)->setTransactionAdditionalInfo("some text",htmlentities($paymentData['payment']['approvalCode']));

        $formatedPrice = $order->getBaseCurrency()->formatTxt($order->getGrandTotal());

        $message = __('The authorized amount is %1.', $formatedPrice);
        //get the object of builder class
        $trans = $this->_transactionBuilder;

        //build method creates the transaction and returns the object
        $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($paymentData['payment']['approvalCode'])
            ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData])
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID);

        $payment->addTransactionCommentsToOrder($transaction,$message);
        $payment->setParentTransactionId(null);
        $payment->save();
        $order->setState("processing")->setStatus("processing");

        $order->save();

        return  $transaction->save()->getTransactionId();
    }
}

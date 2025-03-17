<?php

namespace Versapay\Versapay\Controller\Sales;

class Savetransaction extends \Magento\Framework\App\Action\Action
{

    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_scopeConfig;
    protected $dir;
    protected $_transactionBuilder;
    protected $request;
    protected $resultFactory;
    protected $resultRedirect;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $dir,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Webapi\Rest\Request  $request,

        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->dir = $dir;
        $this->_transactionBuilder = $transactionBuilder;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->resultRedirect = $context->getResultFactory();

        return parent::__construct($context);
    }


    public function execute()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $approvalCode = $this->request->getParam('approvalCode');
        $order_id = $this->_checkoutSession->getLastOrderId();
        $order = $this->_orderFactory->create()->load($order_id);

        $this->saveTransactionDetails($order, $approvalCode );
        $resultRedirect = $this->resultRedirect->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/onepage/success', ['_secure' => true]);

        return $resultRedirect;
    }


    public function saveTransactionDetails($order , $approvalCode){
          //get payment object from order object
            $versapay_orderid = $this->request->getParam('versapay_orderid');

            $payment = $order->getPayment();
            $payment->setTransactionId(htmlentities($approvalCode));
            $payment->setIsTransactionClosed(0)->setTransactionAdditionalInfo("some text",htmlentities($approvalCode));   

            $formatedPrice = $order->getBaseCurrency()->formatTxt($order->getGrandTotal());
 
            $message = __('The authorized amount is %1.', $formatedPrice);
            //get the object of builder class
            $trans = $this->_transactionBuilder;
            
            //build method creates the transaction and returns the object
           
            $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($approvalCode)
            ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $approvalCode])
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH);
 
            $payment->addTransactionCommentsToOrder($transaction,$message);
            $payment->setParentTransactionId(null);
            $payment->save();

            $order->setState("processing")->setStatus("processing");
            $order->addStatusHistoryComment('Versapay Order Id: '.$versapay_orderid);
            $order->setVersapayOrderid($versapay_orderid);
            $order->save();
 
            return  $transaction->save()->getTransactionId();
    }
}
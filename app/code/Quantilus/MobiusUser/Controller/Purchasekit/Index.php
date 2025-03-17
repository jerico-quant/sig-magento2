<?php
namespace Quantilus\MobiusUser\Controller\Purchasekit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\SessionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Framework\UrlInterface;

class Index extends Action
{
    /**
     * @var SessionFactory
     */
    protected $_checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected $_cartRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Coupon
     */
    protected $_coupon;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @param Context $context
     * @param SessionFactory $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param Coupon $coupon
     * @param RuleFactory $ruleFactory
     * @param UrlInterface $url
     */
    public function __construct(
        Context $context,
        SessionFactory $checkoutSession,
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        Coupon $coupon,
        RuleFactory $ruleFactory,
        UrlInterface $url
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_cartRepository = $cartRepository;
        $this->_productRepository = $productRepository;
        $this->_logger = $logger;
        $this->_coupon = $coupon;
        $this->_ruleFactory = $ruleFactory;
        $this->_url = $url;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = 1;
        $couponCode = $this->getRequest()->getParam('voucher_code');
        $referrerUrl = $this->_redirect->getRefererUrl();

        try {
            $product = $this->_productRepository->getById($productId);
            $qty = 1;
            $session = $this->_checkoutSession->create();
            $quote = $session->getQuote();

            // Check if the product is already in the cart
            $isProductInCart = false;
            foreach ($quote->getAllItems() as $item) {
                if ($item->getProductId() == $productId) {
                    $isProductInCart = true;
                    break;
                }
            }

            if ($isProductInCart) {
                // Product is already in the cart
                $this->messageManager->addNoticeMessage(__('This product is already in your cart.'));
            } else {
                // Add the product to the cart
                $quote->addProduct($product, $qty);

                // Validate the coupon code
                if($couponCode){
                    $coupon = $this->_coupon->load($couponCode, 'code');
                    $rule = $this->_ruleFactory->create()->load($coupon->getRuleId());

                    if ($coupon->getId() && $rule->getIsActive()) {
                        $quote->setCouponCode($couponCode)->collectTotals();
                        $this->messageManager->addSuccessMessage(__('Coupon code "%1" was successfully applied.', $couponCode));
                    } else {
                        $this->messageManager->addErrorMessage(__('Coupon code "%1" is invalid or no longer active.', $couponCode));
                        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                        $resultRedirect->setUrl($referrerUrl ?: $this->_url->getUrl('checkout/cart'));
                        return $resultRedirect;
                    }
                }
                
                // call to calculate all fields related to totals
                $quote->collectTotals();
                $this->_cartRepository->save($quote);
                $session->replaceQuote($quote)->unsLastRealOrderId();

                $this->messageManager->addSuccessMessage(__('Product was successfully added to your shopping cart.'));
            }
        } catch (\Exception $e) {
            // Log the error and display an error message
            $this->messageManager->addErrorMessage(__('There was an error adding the product to your cart or applying the coupon code.'));
            $this->_logger->error($e->getMessage());

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($referrerUrl ?: $this->_url->getUrl('checkout/cart'));
            return $resultRedirect;
        }

        // Redirect to the cart page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/index/index');

        return $resultRedirect;
    }
}
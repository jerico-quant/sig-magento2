<?php
namespace Quantilus\MobiusUser\Controller\Purchasekit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\SessionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

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
     * @param Context $context
     * @param SessionFactory $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SessionFactory $checkoutSession,
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_cartRepository = $cartRepository;
        $this->_productRepository = $productRepository;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Product ID to add to the cart
        $productId = 1;

        try {
            $product = $this->_productRepository->getById($productId);
            $qty = 1;

            $session = $this->_checkoutSession->create();
            $quote = $session->getQuote();
            $quote->addProduct($product, $qty);

            $this->_cartRepository->save($quote);
            $session->replaceQuote($quote)->unsLastRealOrderId();

            // Set success message
            $this->messageManager->addSuccessMessage(__('Product was successfully added to your shopping cart.'));
        } catch (\Exception $e) {
            // Log the error and display an error message
            $this->messageManager->addErrorMessage(__('There was an error adding the product to your cart.'));
            $this->_logger->error($e->getMessage());
        }

        // Redirect to the cart page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/index/index');

        return $resultRedirect;
    }
}
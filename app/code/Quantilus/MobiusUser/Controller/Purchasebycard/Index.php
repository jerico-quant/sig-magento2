<?php
namespace Quantilus\MobiusUser\Controller\Purchasebycard;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\SessionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\UrlInterface;
use Quantilus\CourseReference\Model\CourseReferenceRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Quantilus\MobiusUser\Controller\Landingpage;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index extends Landingpage
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
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var CourseReferenceRepository $_courseReferenceRepository
     */
    protected $_courseReferenceRepository;

    /**
     * @var SearchCriteriaBuilder $_searchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param SessionFactory $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param UrlInterface $url
     * @param CustomerSession $customerSession
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        SessionFactory $checkoutSession,
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        UrlInterface $url,
        CourseReferenceRepository $courseReferenceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PageFactory $pageFactory,
        CustomerSession $customerSession,
        JsonFactory $resultJsonFactory,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_cartRepository = $cartRepository;
        $this->_productRepository = $productRepository;
        $this->_logger = $logger;
        $this->_url = $url;
        $this->_courseReferenceRepository = $courseReferenceRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $pageFactory, $customerSession, $resultJsonFactory, $resultRedirectFactory);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $course_id = $this->_customerSession->getCourseId();
        $customer_number = $this->_customerSession->getCustomerNumber();

        try {
            // load the Course Reference record, it has the product sku in item_number field
            $courseReference = $this->getCourseReferenceByCourseIdAndCustomerNumber(0, $customer_number);
            $sku = $courseReference->getItemNumber();
            $product = $this->_productRepository->get($sku);
            $productId = $product->getId();
            $this->_customerSession->setMainProductId($productId); //store the product id in the customer session

            $custom_price = number_format($courseReference->getUnitPrice(),2); // fetch the price set for the product, not from the admin
            $product->setPrice($custom_price);
            $product->setBasePrice($custom_price);
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
                $quoteItem = $quote->addProduct($product, $qty);
                
                // Set custom price properly
                if (is_array($quoteItem)) {
                    $quoteItem = $quoteItem[0]; // handle case where addProduct returns array
                }
                
                // set the custom price on the fly to fix issue with calculating totals
                $quoteItem->setCustomPrice($custom_price);
                $quoteItem->setOriginalCustomPrice($custom_price);
                $quoteItem->getProduct()->setIsSuperMode(true); // This bypasses validation
                
                // this is a fix to the 0 subtotal in cart
                $shippingAddress = $quote->getShippingAddress();
                $shippingAddress->setCollectShippingRates(true)->collectShippingRates();
                
                // call to calculate all fields related to totals
                $quote->collectTotals();
                $this->_cartRepository->save($quote);
                $session->replaceQuote($quote)->unsLastRealOrderId();

                $this->messageManager->addSuccessMessage(__('Product was successfully added to your shopping cart.'));
            }
        } catch (\Exception $e) {
            // Log the error and display an error message
            $this->messageManager->addErrorMessage(__('There was an error adding the product to your cart or applying the coupon code.'));
            $this->_logger->error(
                __(
                    "Error: %1\nStack Trace:\n%2",
                    $e->getMessage(),
                    $e->getTraceAsString()
                )
            );

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($referrerUrl ?: $this->_url->getUrl('checkout/cart'));
            return $resultRedirect;
        }

        // Redirect to the cart page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/index/index');

        return $resultRedirect;
    }

    protected function getCourseReferenceByCourseIdAndCustomerNumber($course_id, $customer_number)
    {        
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('course_id', $course_id, 'eq')
            ->addFilter('customer_number', $customer_number, 'eq')
            ->setPageSize(1)
            ->create();
        
        $searchResult = $this->_courseReferenceRepository->getList($searchCriteria);
        
        if ($searchResult->getTotalCount() === 0) {
            throw new NoSuchEntityException(__(
                'CourseReference with course_id "%1" and customer_number "%2" does not exist.',
                $course_id,
                $customer_number
            ));
        }
        
        return current($searchResult->getItems());
    }
}

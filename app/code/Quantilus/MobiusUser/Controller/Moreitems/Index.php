<?php
namespace Quantilus\MobiusUser\Controller\Moreitems;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session as CustomerSession;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @param Context $context
     */
    public function __construct(
       Context $context,
       PageFactory $pageFactory,
       Product $product,
       Registry $registry,
       CustomerSession $customerSession
    )
    {
        $this->_product = $product;
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = $this->_customerSession->getMainProductId();
        $product = $this->_product->load($productId);
        
        if (!$product->getId()) {
            return $this->_redirect('noroute');
        }

        // Register the product
        $this->_registry->unregister('product');
        $this->_registry->unregister('current_product');
        $this->_registry->register('product', $product);
        $this->_registry->register('current_product', $product);

        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('More Items'));
        return $resultPage;
    }
}

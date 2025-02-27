<?php
namespace Quantilus\FrontendRestriction\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RedirectObserver implements ObserverInterface
{
    protected $responseFactory;
    protected $url;
    protected $scopeConfig;

    const XML_PATH_ALLOWED_URLS = 'frontend_restriction/settings/allowed_urls';

    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $fullactionname = $request->getFullActionName();

        // Get allowed URLs from the admin configuration
        $allowedUrls = $this->scopeConfig->getValue(self::XML_PATH_ALLOWED_URLS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        // Convert comma-separated string to an array
        $allowedRoutes = explode(',', $allowedUrls);
        $allowedRoutes = array_map('trim', $allowedRoutes);

        // Check if the current URL is allowed
        if (!in_array($fullactionname, $allowedRoutes)) {
            // Redirect to the cart page
            $redirectUrl = $this->url->getUrl('checkout/cart');
            $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit; // Stop further execution
        }
    }
}
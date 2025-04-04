<?php
namespace Quantilus\FrontendRestriction\Plugin;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RedirectToCartPlugin
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $responseFactory;

    protected $url;

    /**
     * XML path for allowed URLs
     */
    const XML_PATH_ALLOWED_URLS = 'frontend_restriction/settings/allowed_urls';

    /**
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url,
        ResponseFactory $responseFactory
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Around plugin for the execute method
     *
     * @param Action $subject
     * @param callable $proceed
     */
    public function aroundExecute(Action $subject, callable $proceed)
    {
        // Get the full action name
        $fullActionName = $this->request->getFullActionName();

        // Get the allowed URLs from configuration
        $allowedUrls = $this->scopeConfig->getValue(
            self::XML_PATH_ALLOWED_URLS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // Convert comma-separated string to an array
        $allowedRoutes = explode(',', $allowedUrls);
        $allowedRoutes = array_map('trim', $allowedRoutes);

        // Check if the current action is allowed
        if (!in_array($fullActionName, $allowedRoutes)) {
            $redirectUrl = $this->url->getUrl('checkout/cart');
            $subject->getResponse()->setRedirect($redirectUrl)->sendResponse();
            exit;
        }

        // Proceed with the original execute method
        return $proceed();
    }
}
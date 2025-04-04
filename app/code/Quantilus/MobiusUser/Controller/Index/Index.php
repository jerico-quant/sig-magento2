<?php
namespace Quantilus\MobiusUser\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * @var string
     */
    protected $allowedReferrer = 'https://allowed-referrer.com'; // Replace with your allowed referrer

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param CustomerSession $customerSession
     * @param JsonFactory $resultJsonFactory
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        CustomerSession $customerSession,
        JsonFactory $resultJsonFactory,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // Validate the referrer
        $referrer = $this->getRequest()->getServer('HTTP_REFERER');
        /*if ($referrer !== $this->allowedReferrer) {
            // Return unauthorized status (HTTP 403 Forbidden)
            $result = $this->_resultJsonFactory->create();
            $result->setHttpResponseCode(403); // HTTP 403 Forbidden
            $result->setData(['error' => 'Unauthorized access.']);
            return $result;
        }*/

        // Get query parameters
        $course_id = $this->getRequest()->getParam('courseid');
        $customer_number = $this->getRequest()->getParam('customernumber');
        $email = $this->getRequest()->getParam('email');
        $returnurl = $this->getRequest()->getParam('returnurl');

        // Check if any required parameter is missing
        if (empty($course_id) || empty($customer_number) || empty($email) || empty($returnurl)) {
            $result = $this->_resultJsonFactory->create();
            $result->setHttpResponseCode(403);
            $result->setData(['error' => 'Missing required parameters. All parameters (courseid, customernumber, email, returnurl) are required.']);
            return $result;
        }

        // Store parameters in session
        $this->_customerSession->setCourseId($course_id);
        $this->_customerSession->setCustomerNumber($customer_number);
        $this->_customerSession->setEmail($email);
        $this->_customerSession->setReturnUrl($returnurl);
        
        return $this->_pageFactory->create();
    }
}
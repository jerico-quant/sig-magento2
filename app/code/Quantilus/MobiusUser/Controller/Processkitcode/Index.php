<?php
namespace Quantilus\MobiusUser\Controller\Processkitcode;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\RedirectFactory;
use Quantilus\MobiusUser\Model\MobiusUserRepository;
use Quantilus\MobiusUser\Api\Data\MobiusUserInterfaceFactory;
use Quantilus\MobiusUser\Helper\Config as ConfigHelper;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

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
     * @var RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * @var MobiusUserRepository
     */
    protected $_mobiusUserRepository;

    /**
     * @var MobiusUserInterfaceFactory
     */
    protected $_mobiusUserFactory;

    /**
     * @var ConfigHelper
     */
    protected $_configHelper;

    /**
     * @var Curl
     */
    protected $_curlClient;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       Context $context,
       PageFactory $pageFactory,
       CustomerSession $customerSession,
       RedirectFactory $resultRedirectFactory,
       MobiusUserRepository $mobiusUserRepository,
       MobiusUserInterfaceFactory $mobiusUserFactory,
       ConfigHelper $configHelper,
       Curl $curlClient,
       LoggerInterface $logger
    )
    {
        $this->_customerSession = $customerSession;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        $this->_mobiusUserRepository = $mobiusUserRepository;
        $this->_mobiusUserFactory = $mobiusUserFactory;
        $this->_configHelper = $configHelper;
        $this->_curlClient = $curlClient;
        $this->_logger = $logger;
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
        $accountNumber = $this->_customerSession->setAccountNumber('accountNumber');
        $email = $this->_customerSession->setEmail('email');
        $returnUrl = $this->_customerSession->setReturnUrl('returnUrl');

        $kitcode = $this->getRequest()->getParam('kitcode');

        $return_url_params = '';
        // Call the API to get a redemption code
        $redemptionCode = $this->callMobiusToCreateAccessCode();
        if ($redemptionCode) {
            $mobiusUser = $this->_mobiusUserFactory->create();
            $mobiusUser->setAccountNumber($accountNumber);
            $mobiusUser->setEmail($email);
            $mobiusUser->setKitcode($kitcode);
            $this->_mobiusUserRepository->save($mobiusUser);
            $return_url_params = http_build_query([
                'account_number' => $accountNumber,
                'accessCode' => $redemptionCode,
                'success' => 1,
            ]);
        } else {
            $return_url_params = http_build_query([
                'account_number' => $accountNumber,
                'success' => 0,
            ]);
        }

        // Redirect to the constructed URL
        $return_url .= "?" . $return_url_params;
        $resultRedirect = $this->_resultRedirectFactory->create();
        $resultRedirect->setUrl($return_url);

        return $resultRedirect;
    }

    private function callMobiusToCreateAccessCode()
    {
        $api_endpoint = $this->_configHelper->getMobiusApiEndpoint();
        $username = $this->_configHelper->getApiUsername();
        $password = $this->_configHelper->getApiPassword();
        $tenantcode = $this->_configHelper->getScienceInteractiveTenantCode();

        // Construct the API URL
        $api_url = $api_endpoint . "?numberOfCodes=1&tenant=" . $tenantcode . "&username=" . $username . "&password=" . $password;

        // Set headers and make the API call
        $this->_curlClient->addHeader("Content-Type", "application/json");
        $this->_curlClient->get($api_url);

        // Get the HTTP status code
        $statusCode = $this->_curlClient->getStatus();

        // Check if the status code is 200 (OK)
        if ($statusCode !== 200) {
            // Log the error or handle it as needed
            $this->_logger->error("Mobius API call failed with status code: " . $statusCode);
            return false;
        }

        // Get the response body
        $response = $this->_curlClient->getBody();
        $responseData = json_decode($response, true);

        // Check if the response contains redemptionCodes and has at least one code
        if (isset($responseData['redemptionCodes']) && is_array($responseData['redemptionCodes']) && !empty($responseData['redemptionCodes'])) {
            return $responseData['redemptionCodes'][0];
        }

        // Return false if no valid redemption codes are found
        return false;
    }
}

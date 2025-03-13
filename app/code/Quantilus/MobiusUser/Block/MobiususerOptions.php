<?php
namespace Quantilus\MobiusUser\Block;

use Magento\Framework\App\RequestInterface;

class MobiususerOptions extends \Magento\Framework\View\Element\Template
{   
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @param RequestInterface $request
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_request = $request;
        parent::__construct($context, $data);
    }

    public function getUserData(){
        return [
            'accountnumber' => $this->_request->getParam('accountnumber') ?? '',
            'email' => $this->_request->getParam('email') ?? ''
        ];
    }

    public function getProcessKitCodeUrl(){
        return $this->getUrl('*/processkitcode/*');
    }

    public function getPurchaseKitUrl(){
        return $this->getUrl('*/purchasekit/*');
    }

    public function getViewMyOrdersUrl(){
        return $this->getUrl('*/viewmyorders/*');
    }
}

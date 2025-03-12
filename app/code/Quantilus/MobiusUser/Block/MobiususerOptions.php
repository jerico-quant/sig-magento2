<?php
namespace Quantilus\MobiusUser\Block;

class MobiususerOptions extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProcessKitCodeUrl(){
        return $this->getUrl('*/processkitcode/*');
    }

    public function getPurchaseKitUrl(){
        return $this->getUrl('*/purchasekit/*');
    }
}

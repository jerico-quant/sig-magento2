<?php
namespace Quantilus\MobiusUser\Controller\Purchasebyvoucher;

use Quantilus\MobiusUser\Controller\Landingpage;

class Index extends Landingpage
{
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->fetchParameters();
        return $this->_pageFactory->create();
    }
}

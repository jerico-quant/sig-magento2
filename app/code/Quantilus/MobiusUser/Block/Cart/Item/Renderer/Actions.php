<?php

namespace Quantilus\MobiusUser\Block\Cart\Item\Renderer;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Framework\View\Element\Text;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * @api
 * @since 100.0.2
 */
class Actions extends Text
{
    /**
     * @var AbstractItem
     */
    protected $item;

    /**
     * Returns current quote item
     *
     * @return AbstractItem
     * @codeCoverageIgnore
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set current quote item
     *
     * @param AbstractItem $item
     * @return $this
     * @codeCoverageIgnore
     */
    public function setItem(AbstractItem $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Render html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setText('');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get(\Magento\Customer\Model\Session::class);
        $mainProductId = $customerSession->getMainProductId(); //get the main product id that was added

        $layout = $this->getLayout();
        $item = $this->getItem();
        foreach ($this->getChildNames() as $child) {
            // this removes the delete icon for the item that match the $mainProductId
            // if($child == "checkout.cart.item.renderers.simple.actions.remove" && $item->getData('product_id') == $mainProductId)
            // {
            //     continue;
            // }
            /** @var Generic $childBlock */
            $childBlock = $layout->getBlock($child);
            if ($childBlock instanceof Generic) {
                $childBlock->setItem($item);
                $this->addText($layout->renderElement($child, false));
            }
        }

        return parent::_toHtml();
    }
}

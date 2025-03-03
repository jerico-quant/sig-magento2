<?php

namespace Quantilus\InstitutionManagement\Ui\Component\DataProvider;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductOptions implements OptionSourceInterface
{
    protected $productCollectionFactory;

    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['entity_id', 'name', 'sku', 'price', 'status']);

        $options = [];
        foreach ($collection as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => sprintf(
                    '%s (SKU: %s, Price: %s)',
                    $product->getName(),
                    $product->getSku(),
                    number_format($product->getPrice(), 2) . ' ' . $this->getCurrencySymbol()
                ),
                'sku' => $product->getSku(),
                'price' => $product->getPrice()
            ];
        }

        return $options;
    }

    /**
     * Get currency symbol (Optional: If price needs currency formatting)
     */
    private function getCurrencySymbol()
    {
        return $this->_objectManager->get(\Magento\Directory\Model\Currency::class)
            ->getCurrencySymbol();
    }

}

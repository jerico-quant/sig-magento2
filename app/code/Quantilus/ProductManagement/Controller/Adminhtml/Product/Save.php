<?php
// app/code/Quantilus/ProductManagement/Controller/Adminhtml/Product/Save.php
namespace Quantilus\ProductManagement\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class Save extends Action
{
    protected $productFactory;

    public function __construct(
        Action\Context $context,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $product = $this->productFactory->create();
                $product->setSku($data['sku']);
                $product->setName($data['name']);
                $product->setDescription($data['description']);
                $product->setPrice($data['price']);
                $product->setTypeId(Type::TYPE_SIMPLE);
                $product->setAttributeSetId(4); // Default attribute set
                $product->setStatus(Status::STATUS_ENABLED);
                $product->setVisibility(Visibility::VISIBILITY_BOTH);
                $product->setStockData([
                    'use_config_manage_stock' => 1,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                ]);
                $product->save();
                $this->messageManager->addSuccessMessage(__('Product saved successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $this->_redirect('quantilus_productmanagement/product/index');
    }
}
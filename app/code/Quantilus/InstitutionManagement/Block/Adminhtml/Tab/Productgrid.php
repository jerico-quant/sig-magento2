<?php

namespace Quantilus\InstitutionManagement\Block\Adminhtml\Tab;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ProductFactory;
use Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts\CollectionFactory;
use Magento\Framework\Registry;

class Productgrid extends Extended
{
    protected $_coreRegistry;
    protected $_productFactory;
    protected $_productCollectionFactory;
    protected $institutionId;

    public function __construct(
        Context $context,
        Data $backendHelper,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set the institution ID for the grid
     *
     * @param int $institutionId
     * @return $this
     */
    public function setInstitutionId($institutionId)
    {
        $this->institutionId = $institutionId;
        return $this;
    }

    /**
     * Get the institution ID
     *
     * @return int|null
     */
    public function getInstitutionId()
    {
        return $this->institutionId;
    }

    /**
     * Prepare the collection for the grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create()->getCollection()
            ->addAttributeToSelect(['name', 'sku', 'price']);

        // Filter products by institution ID (if set)
        if ($this->getInstitutionId()) {
            $productIds = $this->_getSelectedProducts();
            if (!empty($productIds)) {
                $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare the grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_institution',
            [
                'type' => 'checkbox',
                'name' => 'in_institution',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction',
                'field_name' => 'selected_products[]' // Add this line
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get the grid URL for AJAX requests
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('quantilus_institutionmanagement/institution/productsgrid', ['_current' => true]);
    }

    /**
     * Get the selected products for the current institution
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        if ($this->getInstitutionId()) {
            $productCollection = $this->_productCollectionFactory->create()
                ->addFieldToFilter('institution_id', ['eq' => $this->getInstitutionId()])
                ->addFieldToSelect('product_id');

            $products = [];
            foreach ($productCollection as $item) {
                $products[] = $item->getProductId();
            }
            return $products;
        }
        return [];
    }
}
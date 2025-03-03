<?php

namespace Quantilus\InstitutionManagement\Block\Adminhtml;

class InstitutionProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/institution_products.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Productgrid
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts\CollectionFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Framework\Registry                                       $registry
     * @param \Magento\Framework\Json\EncoderInterface                          $jsonEncoder
     * @param \Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts\CollectionFactory $productFactory
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts\CollectionFactory $productFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Quantilus\InstitutionManagement\Block\Adminhtml\Tab\Productgrid',
                'institution.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $institutionId = $this->getRequest()->getParam('institution_id');
        $productCollection = $this->productFactory->create()
            ->addFieldToSelect(['product_id'])
            ->addFieldToFilter('institution_id', ['eq' => $institutionId]);

        $result = [];
        foreach ($productCollection as $item) {
            $result[$item->getProductId()] = '';
        }
        return $this->jsonEncoder->encode($result);
    }

    public function getItem()
    {
        return $this->registry->registry('current_institution');
    }
}
<?php

namespace Quantilus\InstitutionManagement\Controller\Adminhtml\Institution;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

class Grids extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Execute the controller action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        // Create a raw result object
        $resultRaw = $this->resultRawFactory->create();

        // Get the layout and create the product grid block
        $layout = $this->layoutFactory->create();
        $gridBlock = $layout->createBlock(
            \Quantilus\InstitutionManagement\Block\Adminhtml\Tab\Productgrid::class,
            'institution.product.grid'
        );

        // Set the grid block's institution ID (if available)
        $institutionId = $this->getRequest()->getParam('institution_id');
        if ($institutionId) {
            $gridBlock->setInstitutionId($institutionId);
        }

        // Return the grid HTML as the response
        return $resultRaw->setContents($gridBlock->toHtml());
    }
}
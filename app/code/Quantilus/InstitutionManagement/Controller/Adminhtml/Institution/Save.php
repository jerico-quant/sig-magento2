<?php

namespace Quantilus\InstitutionManagement\Controller\Adminhtml\Institution;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Quantilus\InstitutionManagement\Model\InstitutionFactory;
use Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts\CollectionFactory as InstitutionProductsCollectionFactory;

class Save extends Action
{
    protected $dataPersistor;
    protected $institutionFactory;
    protected $institutionProductsCollectionFactory;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param InstitutionFactory $institutionFactory
     * @param InstitutionProductsCollectionFactory $institutionProductsCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        InstitutionFactory $institutionFactory,
        InstitutionProductsCollectionFactory $institutionProductsCollectionFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->institutionFactory = $institutionFactory;
        $this->institutionProductsCollectionFactory = $institutionProductsCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('institution_id');

            // Load the institution model
            $model = $this->institutionFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Institution no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            // Save the institution data
            $model->setData($data);

            try {
                $model->save();

                // Save the selected products
                $selectedProducts = $this->getRequest()->getParam('selected_products', []);
                $this->saveInstitutionProducts($model->getId(), $selectedProducts);

                $this->messageManager->addSuccessMessage(__('You saved the Institution.'));
                $this->dataPersistor->clear('quantilus_institutionmanagement_institution');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['institution_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Institution.'));
            }

            $this->dataPersistor->set('quantilus_institutionmanagement_institution', $data);
            return $resultRedirect->setPath('*/*/edit', ['institution_id' => $this->getRequest()->getParam('institution_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Save the institution-product relationships
     *
     * @param int $institutionId
     * @param array $selectedProducts
     * @return void
     */
    protected function saveInstitutionProducts($institutionId, $selectedProducts)
    {
        // Convert selected products to an array
        $selectedProducts = is_array($selectedProducts) ? $selectedProducts : explode(',', $selectedProducts);

        // Load existing institution-product relationships
        $institutionProductsCollection = $this->institutionProductsCollectionFactory->create()
            ->addFieldToFilter('institution_id', $institutionId);

        // Delete existing relationships
        foreach ($institutionProductsCollection as $institutionProduct) {
            $institutionProduct->delete();
        }

        // Save new relationships
        foreach ($selectedProducts as $productId) {
            $institutionProduct = $this->institutionProductsCollectionFactory->create()
                ->getNewItem()
                ->setData([
                    'institution_id' => $institutionId,
                    'product_id' => $productId,
                ]);
            $institutionProduct->save();
        }
    }
}
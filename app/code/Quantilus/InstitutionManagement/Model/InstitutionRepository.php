<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\InstitutionManagement\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Quantilus\InstitutionManagement\Api\Data\InstitutionInterface;
use Quantilus\InstitutionManagement\Api\Data\InstitutionInterfaceFactory;
use Quantilus\InstitutionManagement\Api\Data\InstitutionSearchResultsInterfaceFactory;
use Quantilus\InstitutionManagement\Api\InstitutionRepositoryInterface;
use Quantilus\InstitutionManagement\Model\ResourceModel\Institution as ResourceInstitution;
use Quantilus\InstitutionManagement\Model\ResourceModel\Institution\CollectionFactory as InstitutionCollectionFactory;

class InstitutionRepository implements InstitutionRepositoryInterface
{

    /**
     * @var InstitutionInterfaceFactory
     */
    protected $institutionFactory;

    /**
     * @var InstitutionCollectionFactory
     */
    protected $institutionCollectionFactory;

    /**
     * @var ResourceInstitution
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Institution
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceInstitution $resource
     * @param InstitutionInterfaceFactory $institutionFactory
     * @param InstitutionCollectionFactory $institutionCollectionFactory
     * @param InstitutionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceInstitution $resource,
        InstitutionInterfaceFactory $institutionFactory,
        InstitutionCollectionFactory $institutionCollectionFactory,
        InstitutionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->institutionFactory = $institutionFactory;
        $this->institutionCollectionFactory = $institutionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(InstitutionInterface $institution)
    {
        try {
            $this->resource->save($institution);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the institution: %1',
                $exception->getMessage()
            ));
        }
        return $institution;
    }

    /**
     * @inheritDoc
     */
    public function get($institutionId)
    {
        $institution = $this->institutionFactory->create();
        $this->resource->load($institution, $institutionId);
        if (!$institution->getId()) {
            throw new NoSuchEntityException(__('Institution with id "%1" does not exist.', $institutionId));
        }
        return $institution;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->institutionCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(InstitutionInterface $institution)
    {
        try {
            $institutionModel = $this->institutionFactory->create();
            $this->resource->load($institutionModel, $institution->getInstitutionId());
            $this->resource->delete($institutionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Institution: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($institutionId)
    {
        return $this->delete($this->get($institutionId));
    }
}


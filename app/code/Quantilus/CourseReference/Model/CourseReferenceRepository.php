<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Quantilus\CourseReference\Api\CourseReferenceRepositoryInterface;
use Quantilus\CourseReference\Api\Data\CourseReferenceInterface;
use Quantilus\CourseReference\Api\Data\CourseReferenceInterfaceFactory;
use Quantilus\CourseReference\Api\Data\CourseReferenceSearchResultsInterfaceFactory;
use Quantilus\CourseReference\Model\ResourceModel\CourseReference as ResourceCourseReference;
use Quantilus\CourseReference\Model\ResourceModel\CourseReference\CollectionFactory as CourseReferenceCollectionFactory;

class CourseReferenceRepository implements CourseReferenceRepositoryInterface
{

    /**
     * @var CourseReference
     */
    protected $searchResultsFactory;

    /**
     * @var CourseReferenceInterfaceFactory
     */
    protected $courseReferenceFactory;

    /**
     * @var ResourceCourseReference
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var CourseReferenceCollectionFactory
     */
    protected $courseReferenceCollectionFactory;


    /**
     * @param ResourceCourseReference $resource
     * @param CourseReferenceInterfaceFactory $courseReferenceFactory
     * @param CourseReferenceCollectionFactory $courseReferenceCollectionFactory
     * @param CourseReferenceSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceCourseReference $resource,
        CourseReferenceInterfaceFactory $courseReferenceFactory,
        CourseReferenceCollectionFactory $courseReferenceCollectionFactory,
        CourseReferenceSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->courseReferenceFactory = $courseReferenceFactory;
        $this->courseReferenceCollectionFactory = $courseReferenceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        CourseReferenceInterface $courseReference
    ) {
        try {
            $this->resource->save($courseReference);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the courseReference: %1',
                $exception->getMessage()
            ));
        }
        return $courseReference;
    }

    /**
     * @inheritDoc
     */
    public function get($courseReferenceId)
    {
        $courseReference = $this->courseReferenceFactory->create();
        $this->resource->load($courseReference, $courseReferenceId);
        if (!$courseReference->getId()) {
            throw new NoSuchEntityException(__('CourseReference with id "%1" does not exist.', $courseReferenceId));
        }
        return $courseReference;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->courseReferenceCollectionFactory->create();
        
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
    public function delete(
        CourseReferenceInterface $courseReference
    ) {
        try {
            $courseReferenceModel = $this->courseReferenceFactory->create();
            $this->resource->load($courseReferenceModel, $courseReference->getCoursereferenceId());
            $this->resource->delete($courseReferenceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CourseReference: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($courseReferenceId)
    {
        return $this->delete($this->get($courseReferenceId));
    }
}


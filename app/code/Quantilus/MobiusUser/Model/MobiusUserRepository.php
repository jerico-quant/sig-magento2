<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\MobiusUser\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Quantilus\MobiusUser\Api\Data\MobiusUserInterface;
use Quantilus\MobiusUser\Api\Data\MobiusUserInterfaceFactory;
use Quantilus\MobiusUser\Api\Data\MobiusUserSearchResultsInterfaceFactory;
use Quantilus\MobiusUser\Api\MobiusUserRepositoryInterface;
use Quantilus\MobiusUser\Model\ResourceModel\MobiusUser as ResourceMobiusUser;
use Quantilus\MobiusUser\Model\ResourceModel\MobiusUser\CollectionFactory as MobiusUserCollectionFactory;

class MobiusUserRepository implements MobiusUserRepositoryInterface
{
    /**
     * @var MobiusUserInterfaceFactory
     */
    protected $mobiusUserFactory;

    /**
     * @var MobiusUserCollectionFactory
     */
    protected $mobiusUserCollectionFactory;

    /**
     * @var ResourceMobiusUser
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var MobiusUserSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @param ResourceMobiusUser $resource
     * @param MobiusUserInterfaceFactory $mobiusUserFactory
     * @param MobiusUserCollectionFactory $mobiusUserCollectionFactory
     * @param MobiusUserSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceMobiusUser $resource,
        MobiusUserInterfaceFactory $mobiusUserFactory,
        MobiusUserCollectionFactory $mobiusUserCollectionFactory,
        MobiusUserSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->mobiusUserFactory = $mobiusUserFactory;
        $this->mobiusUserCollectionFactory = $mobiusUserCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(MobiusUserInterface $mobiusUser)
    {
        try {
            $this->resource->save($mobiusUser);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the mobius user: %1',
                $exception->getMessage()
            ));
        }
        return $mobiusUser;
    }

    /**
     * @inheritDoc
     */
    public function get($mobiusUserId)
    {
        $mobiusUser = $this->mobiusUserFactory->create();
        $this->resource->load($mobiusUser, $mobiusUserId);
        if (!$mobiusUser->getMobiusUserId()) {
            throw new NoSuchEntityException(__('Mobius User with id "%1" does not exist.', $mobiusUserId));
        }
        return $mobiusUser;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->mobiusUserCollectionFactory->create();
        
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
    public function delete(MobiusUserInterface $mobiusUser)
    {
        try {
            $mobiusUserModel = $this->mobiusUserFactory->create();
            $this->resource->load($mobiusUserModel, $mobiusUser->getMobiusUserId());
            $this->resource->delete($mobiusUserModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Mobius User: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($mobiusUserId)
    {
        return $this->delete($this->get($mobiusUserId));
    }
}
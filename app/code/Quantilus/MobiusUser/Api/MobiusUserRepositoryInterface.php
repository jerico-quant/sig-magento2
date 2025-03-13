<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\MobiusUser\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface MobiusUserRepositoryInterface
{
    /**
     * Save Mobius User
     * @param \Quantilus\MobiusUser\Api\Data\MobiusUserInterface $mobiusUser
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Quantilus\MobiusUser\Api\Data\MobiusUserInterface $mobiusUser
    );

    /**
     * Retrieve Mobius User
     * @param string $mobiusUserId
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($mobiusUserId);

    /**
     * Retrieve Mobius User matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Mobius User
     * @param \Quantilus\MobiusUser\Api\Data\MobiusUserInterface $mobiusUser
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Quantilus\MobiusUser\Api\Data\MobiusUserInterface $mobiusUser
    );

    /**
     * Delete Mobius User by ID
     * @param string $mobiusUserId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($mobiusUserId);
}
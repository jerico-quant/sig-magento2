<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\InstitutionManagement\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface InstitutionRepositoryInterface
{

    /**
     * Save Institution
     * @param \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface $institution
     * @return \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface $institution
    );

    /**
     * Retrieve Institution
     * @param string $institutionId
     * @return \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($institutionId);

    /**
     * Retrieve Institution matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Quantilus\InstitutionManagement\Api\Data\InstitutionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Institution
     * @param \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface $institution
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface $institution
    );

    /**
     * Delete Institution by ID
     * @param string $institutionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($institutionId);
}


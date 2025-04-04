<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CourseReferenceRepositoryInterface
{

    /**
     * Save CourseReference
     * @param \Quantilus\CourseReference\Api\Data\CourseReferenceInterface $courseReference
     * @return \Quantilus\CourseReference\Api\Data\CourseReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Quantilus\CourseReference\Api\Data\CourseReferenceInterface $courseReference
    );

    /**
     * Retrieve CourseReference
     * @param string $coursereferenceId
     * @return \Quantilus\CourseReference\Api\Data\CourseReferenceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($coursereferenceId);

    /**
     * Retrieve CourseReference matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Quantilus\CourseReference\Api\Data\CourseReferenceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CourseReference
     * @param \Quantilus\CourseReference\Api\Data\CourseReferenceInterface $courseReference
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Quantilus\CourseReference\Api\Data\CourseReferenceInterface $courseReference
    );

    /**
     * Delete CourseReference by ID
     * @param string $coursereferenceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($coursereferenceId);
}


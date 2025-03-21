<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Api\Data;

interface CourseReferenceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get CourseReference list.
     * @return \Quantilus\CourseReference\Api\Data\CourseReferenceInterface[]
     */
    public function getItems();

    /**
     * Set course_id list.
     * @param \Quantilus\CourseReference\Api\Data\CourseReferenceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}


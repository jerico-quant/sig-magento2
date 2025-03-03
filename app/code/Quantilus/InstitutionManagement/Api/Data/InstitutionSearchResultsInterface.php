<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\InstitutionManagement\Api\Data;

interface InstitutionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Institution list.
     * @return \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface[]
     */
    public function getItems();

    /**
     * Set code list.
     * @param \Quantilus\InstitutionManagement\Api\Data\InstitutionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}


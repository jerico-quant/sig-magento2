<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\MobiusUser\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface MobiusUserSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Mobius User list.
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface[]
     */
    public function getItems();

    /**
     * Set Mobius User list.
     * @param \Quantilus\MobiusUser\Api\Data\MobiusUserInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
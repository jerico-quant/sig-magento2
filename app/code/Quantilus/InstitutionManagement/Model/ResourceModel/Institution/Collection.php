<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\InstitutionManagement\Model\ResourceModel\Institution;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'institution_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Quantilus\InstitutionManagement\Model\Institution::class,
            \Quantilus\InstitutionManagement\Model\ResourceModel\Institution::class
        );
    }
}


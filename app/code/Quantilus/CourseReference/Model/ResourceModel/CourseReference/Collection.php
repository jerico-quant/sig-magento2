<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Model\ResourceModel\CourseReference;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'coursereference_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Quantilus\CourseReference\Model\CourseReference::class,
            \Quantilus\CourseReference\Model\ResourceModel\CourseReference::class
        );
    }
}


<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CourseReference extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('coursereference', 'coursereference_id');
    }
}


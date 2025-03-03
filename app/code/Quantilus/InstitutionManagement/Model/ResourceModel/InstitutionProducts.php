<?php

namespace Quantilus\InstitutionManagement\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InstitutionProducts extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('quantilus_institutionmanagement_institution_product', 'id');
    }
}
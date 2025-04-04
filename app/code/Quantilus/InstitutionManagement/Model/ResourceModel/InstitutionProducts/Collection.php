<?php

namespace Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Quantilus\InstitutionManagement\Model\InstitutionProducts::class,
            \Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts::class
        );
    }
}
<?php
namespace Quantilus\MobiusUser\Model\ResourceModel\MobiusUser;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
  protected $_idFieldName = 'mobius_user_id';
  protected $_eventPrefix = 'quantilus_mobiususer_mobius_user_collection';
  protected $_eventObject = 'mobius_user_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Quantilus\MobiusUser\Model\MobiusUser', 'Quantilus\MobiusUser\Model\ResourceModel\MobiusUser');
    }
}

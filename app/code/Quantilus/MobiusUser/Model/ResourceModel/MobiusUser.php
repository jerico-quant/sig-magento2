<?php
namespace Quantilus\MobiusUser\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MobiusUser extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('mobius_user', 'mobius_user_id');
    }
}

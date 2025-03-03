<?php

namespace Quantilus\InstitutionManagement\Model;

use Magento\Framework\Model\AbstractModel;

class InstitutionProducts extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Quantilus\InstitutionManagement\Model\ResourceModel\InstitutionProducts::class);
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * Get Institution ID
     *
     * @return int
     */
    public function getInstitutionId()
    {
        return $this->getData('institution_id');
    }

    /**
     * Set Institution ID
     *
     * @param int $institutionId
     * @return $this
     */
    public function setInstitutionId($institutionId)
    {
        return $this->setData('institution_id', $institutionId);
    }

    /**
     * Get Product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * Set Product ID
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData('product_id', $productId);
    }
}
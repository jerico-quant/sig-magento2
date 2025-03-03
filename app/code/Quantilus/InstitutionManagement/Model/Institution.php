<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\InstitutionManagement\Model;

use Magento\Framework\Model\AbstractModel;
use Quantilus\InstitutionManagement\Api\Data\InstitutionInterface;

class Institution extends AbstractModel implements InstitutionInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Quantilus\InstitutionManagement\Model\ResourceModel\Institution::class);
    }

    /**
     * @inheritDoc
     */
    public function getInstitutionId()
    {
        return $this->getData(self::INSTITUTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setInstitutionId($institutionId)
    {
        return $this->setData(self::INSTITUTION_ID, $institutionId);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }
}


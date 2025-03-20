<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Model;

use Magento\Framework\Model\AbstractModel;
use Quantilus\CourseReference\Api\Data\CourseReferenceInterface;

class CourseReference extends AbstractModel implements CourseReferenceInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Quantilus\CourseReference\Model\ResourceModel\CourseReference::class);
    }

    /**
     * @inheritDoc
     */
    public function getCoursereferenceId()
    {
        return $this->getData(self::COURSEREFERENCE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCoursereferenceId($coursereferenceId)
    {
        return $this->setData(self::COURSEREFERENCE_ID, $coursereferenceId);
    }

    /**
     * @inheritDoc
     */
    public function getCourseId()
    {
        return $this->getData(self::COURSE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCourseId($courseId)
    {
        return $this->setData(self::COURSE_ID, $courseId);
    }

    /**
     * @inheritDoc
     */
    public function getCourseName()
    {
        return $this->getData(self::COURSE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCourseName($courseName)
    {
        return $this->setData(self::COURSE_NAME, $courseName);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerNumber()
    {
        return $this->getData(self::CUSTOMER_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerNumber($customerNumber)
    {
        return $this->setData(self::CUSTOMER_NUMBER, $customerNumber);
    }

    /**
     * @inheritDoc
     */
    public function getItemNumber()
    {
        return $this->getData(self::ITEM_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setItemNumber($itemNumber)
    {
        return $this->setData(self::ITEM_NUMBER, $itemNumber);
    }

    /**
     * @inheritDoc
     */
    public function getUnitPrice()
    {
        return $this->getData(self::UNIT_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setUnitPrice($unitPrice)
    {
        return $this->setData(self::UNIT_PRICE, $unitPrice);
    }

    /**
     * @inheritDoc
     */
    public function getBlanketSalesOrderNumber()
    {
        return $this->getData(self::BLANKET_SALES_ORDER_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setBlanketSalesOrderNumber($blanketSalesOrderNumber)
    {
        return $this->setData(self::BLANKET_SALES_ORDER_NUMBER, $blanketSalesOrderNumber);
    }

    /**
     * @inheritDoc
     */
    public function getBlanketSalesOrderLine()
    {
        return $this->getData(self::BLANKET_SALES_ORDER_LINE);
    }

    /**
     * @inheritDoc
     */
    public function setBlanketSalesOrderLine($blanketSalesOrderLine)
    {
        return $this->setData(self::BLANKET_SALES_ORDER_LINE, $blanketSalesOrderLine);
    }
}


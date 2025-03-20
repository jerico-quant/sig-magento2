<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\CourseReference\Api\Data;

interface CourseReferenceInterface
{

    const UNIT_PRICE = 'unit_price';
    const CUSTOMER_NUMBER = 'customer_number';
    const BLANKET_SALES_ORDER_LINE = 'blanket_sales_order_line';
    const COURSE_NAME = 'course_name';
    const ITEM_NUMBER = 'item_number';
    const COURSE_ID = 'course_id';
    const COURSEREFERENCE_ID = 'coursereference_id';
    const BLANKET_SALES_ORDER_NUMBER = 'blanket_sales_order_number';

    /**
     * Get coursereference_id
     * @return string|null
     */
    public function getCoursereferenceId();

    /**
     * Set coursereference_id
     * @param string $coursereferenceId
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setCoursereferenceId($coursereferenceId);

    /**
     * Get course_id
     * @return string|null
     */
    public function getCourseId();

    /**
     * Set course_id
     * @param string $courseId
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setCourseId($courseId);

    /**
     * Get course_name
     * @return string|null
     */
    public function getCourseName();

    /**
     * Set course_name
     * @param string $courseName
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setCourseName($courseName);

    /**
     * Get customer_number
     * @return string|null
     */
    public function getCustomerNumber();

    /**
     * Set customer_number
     * @param string $customerNumber
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setCustomerNumber($customerNumber);

    /**
     * Get item_number
     * @return string|null
     */
    public function getItemNumber();

    /**
     * Set item_number
     * @param string $itemNumber
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setItemNumber($itemNumber);

    /**
     * Get unit_price
     * @return string|null
     */
    public function getUnitPrice();

    /**
     * Set unit_price
     * @param string $unitPrice
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setUnitPrice($unitPrice);

    /**
     * Get blanket_sales_order_number
     * @return string|null
     */
    public function getBlanketSalesOrderNumber();

    /**
     * Set blanket_sales_order_number
     * @param string $blanketSalesOrderNumber
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setBlanketSalesOrderNumber($blanketSalesOrderNumber);

    /**
     * Get blanket_sales_order_line
     * @return string|null
     */
    public function getBlanketSalesOrderLine();

    /**
     * Set blanket_sales_order_line
     * @param string $blanketSalesOrderLine
     * @return \Quantilus\CourseReference\CourseReference\Api\Data\CourseReferenceInterface
     */
    public function setBlanketSalesOrderLine($blanketSalesOrderLine);
}


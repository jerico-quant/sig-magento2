<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\InstitutionManagement\Api\Data;

interface InstitutionInterface
{

    const NAME = 'name';
    const CODE = 'code';
    const INSTITUTION_ID = 'institution_id';

    /**
     * Get institution_id
     * @return string|null
     */
    public function getInstitutionId();

    /**
     * Set institution_id
     * @param string $institutionId
     * @return \Quantilus\InstitutionManagement\Institution\Api\Data\InstitutionInterface
     */
    public function setInstitutionId($institutionId);

    /**
     * Get code
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     * @param string $code
     * @return \Quantilus\InstitutionManagement\Institution\Api\Data\InstitutionInterface
     */
    public function setCode($code);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Quantilus\InstitutionManagement\Institution\Api\Data\InstitutionInterface
     */
    public function setName($name);
}


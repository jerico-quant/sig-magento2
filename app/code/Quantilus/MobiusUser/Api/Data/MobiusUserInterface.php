<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Quantilus\MobiusUser\Api\Data;

interface MobiusUserInterface
{
    const MOBIUS_USER_ID = 'mobius_user_id';
    const ACCOUNT_NUMBER = 'account_number';
    const EMAIL = 'email';
    const KITCODE = 'kitcode';

    /**
     * Get mobius_user_id
     * @return string|null
     */
    public function getMobiusUserId();

    /**
     * Set mobius_user_id
     * @param string $mobiusUserId
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface
     */
    public function setMobiusUserId($mobiusUserId);

    /**
     * Get account_number
     * @return string|null
     */
    public function getAccountNumber();

    /**
     * Set account_number
     * @param string $accountNumber
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface
     */
    public function setAccountNumber($accountNumber);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface
     */
    public function setEmail($email);

    /**
     * Get kitcode
     * @return string|null
     */
    public function getKitcode();

    /**
     * Set kitcode
     * @param string $kitcode
     * @return \Quantilus\MobiusUser\Api\Data\MobiusUserInterface
     */
    public function setKitcode($kitcode);
}
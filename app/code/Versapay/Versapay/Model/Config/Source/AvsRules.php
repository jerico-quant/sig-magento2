<?php

namespace Versapay\Versapay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AvsRules implements OptionSourceInterface 
{
    public function toOptionArray() {
        return [
            ['value' => 'rejectAddressMismatch', 'label' => __('Reject Address Mismatch')],
            ['value' => 'rejectPostCodeMismatch', 'label' => __('Reject Postal Code Mismatch')],
            ['value' => 'rejectUnknown', 'label' => __('Reject AVS Unknown')]
        ];
    }
}
<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Versapay\Versapay\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Versapay\Versapay\Gateway\Request\MockDataRequest;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        TransferBuilder $transferBuilder
    ) {
        $this->transferBuilder = $transferBuilder;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        //print_r($request);die;
        return $this->transferBuilder
            ->setBody($request)
            ->setMethod('POST')
            ->setHeaders(
                [
                    'force_result' => isset($request[MockDataRequest::FORCE_RESULT])
                        ? $request[MockDataRequest::FORCE_RESULT]
                        : null
                ]
            )
            ->build();
    }
}

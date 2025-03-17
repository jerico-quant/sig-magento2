/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
var versapay_paymentType;
var versapay_token;
var approvalCodes;
var versapayOrderId;
var refreshClientTotals = false;
var client;
var clientOnApprovalFirstRun = true;
var formSubmitEventListenerFirstRun = true;
var submitButtonEventListenerFirstRun = true;
var clientSession = window.checkoutConfig.payment.versapay_gateway.versapay_sessionkey;
var quote_id = window.checkoutConfig.payment.versapay_gateway.versapay_quote_id;
var subDomainvpay = window.checkoutConfig.payment.versapay_gateway.versapay_subdomain.trim();
var ach_status = window.checkoutConfig.payment.versapay_gateway.ach_status;
define(
    [
        'ko',
        'https://' + subDomainvpay + '.versapay.com/client.js',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Ui/js/model/messageList',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/get-totals',
        'uiRegistry',
        'mage/translate'
    ],
    function (
        ko,
        versapay,
        Component,
        urlBuilder,
        url,
        messageList,
        $,
        quote,
        getTotalsAction,
        uiRegistry
        ) {
        'use strict';

        const isElementLoaded = async (selector) => {
            while (document.querySelector(selector) === null) {
                await new Promise(resolve => requestAnimationFrame(resolve));
            }
            return document.querySelector(selector);
        }

        var errorMessage = $.mage.__('There was a problem processing your payment. Please check your billing address and payment details or use a different payment method.');

        const isPlaceOrderActionAllowed = ko.observable(quote.billingAddress() != null);

        approvalCodes = [];

        const initVersapayPaymentMethod = () => {
            const form = document.querySelector('#co-payment-form');

            var styles = {
                form: {
                    "margin-left": 0
                }
            };

            let grandTotal = parseFloat(quote.totals()['grand_total']);
            refreshClientTotals = true;

            if (client) {    
                versapay.teardownClient(client);
            }
            client = versapay.initClient(clientSession, styles, [], grandTotal);

            var docWidth = Math.min(500, document.documentElement.clientWidth);
            var docWidthMod = docWidth < 500 ? 40 : 0;
            var docHeight = 500;

            let frameLoaded = false;
            let frameReadyPromise;
            isElementLoaded('#versapay-container').then((selector) => {
                frameReadyPromise = client.initFrame(selector, `${docHeight}px`, `${docWidth - docWidthMod}px`);
                frameReadyPromise.then(function () {
                    frameLoaded = true;
    
                    client.onPartialPayment(
                        (result) => {
                            submitButtonEventListenerFirstRun = true, formSubmitEventListenerFirstRun = true;
                            messageList.clear();
                            messageList.addErrorMessage({ message: result.message });
                            $('#versapay-submit').attr("disabled", false);
                        }
                    );
    
                    client.onApproval(
                        (result) => {
                            if (clientOnApprovalFirstRun) {
                                messageList.clear();
                                clientOnApprovalFirstRun = false;
                                refreshClientTotals = false;
    
                                var payments = result.partialPayments.map(partialPayment => ({
                                    token: partialPayment.token,
                                    payment_type: partialPayment.paymentTypeName,
                                    amount: partialPayment.amount ?? 0.0
                                }));
    
                                payments.push({
                                    token: result.token,
                                    payment_type: result.paymentTypeName,
                                    amount: result.amount ?? 0.0
                                });
    
                                var aurl = url.build("versapay/sales/");
    
                                var param = {
                                    payments: payments,
                                    quote_id: quote_id,
                                    session_id: clientSession,
                                    guest_email: quote.guestEmail ?? ''
                                };
                                $.ajax({
                                    url: aurl,
                                    data: { data: param },
                                    type: "POST",
                                    dataType: 'json'
                                }).done(function (data) {
                                    if (!data.orderId) {
                                        clientOnApprovalFirstRun = true, submitButtonEventListenerFirstRun = true, formSubmitEventListenerFirstRun = true;
                                        $('#versapay-submit').removeAttr("disabled");
                                        jQuery("html, body").animate({ scrollTop: 0 }, "fast");
                                        messageList.addErrorMessage({ message: errorMessage });
                                    }
                                    else {
                                        versapayOrderId = data.orderId;
                                        if (data.payments && data.payments.length > 0) {
                                            for (var i = 0; i < data.payments.length; i++) {
                                                if (data.payments[i].payment.approvalCode) {
                                                    approvalCodes.push(data.payments[i].payment.approvalCode);
                                                }
                                                else {
                                                    approvalCodes.push(data.payments[i].payment.transactionId);
                                                }
                                            }
                                        }
                                        $("#versapay-final-submit").click();
                                    }
                                });
                            }
                        },
                        (error) => {
                            clientOnApprovalFirstRun = true, submitButtonEventListenerFirstRun = true, formSubmitEventListenerFirstRun = true;
                            $('#versapay-submit').removeAttr("disabled");
                            jQuery("html, body").animate({ scrollTop: 0 }, "fast");
                            messageList.addErrorMessage({ message: errorMessage });
                        }
                    );
    
                    $('#versapay-submit').removeAttr("disabled");
    
                    form.addEventListener('submit', function (event) {
                        if (formSubmitEventListenerFirstRun) {
                            formSubmitEventListenerFirstRun = false;
                            let billingAddressComponent = uiRegistry.get('checkout.steps.billing-step.payment.payments-list.versapay_gateway-form') ?? 
                                uiRegistry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form');
                            billingAddressComponent.updateAddress();
                            $('#versapay-submit').attr("disabled", true);
                            event.preventDefault();
                            client.submitEvents();
                        } 
                    });
    
                    var $submitButton = document.getElementById("versapay-submit");
                    $submitButton.addEventListener('click', function (event) {
                        if (isPlaceOrderActionAllowed && submitButtonEventListenerFirstRun) {
                            submitButtonEventListenerFirstRun = false;
                            document.querySelector('#versapay-container').scrollIntoView(true);
                        }
                    });
                });
            })
        }

        quote.paymentMethod.subscribe((method) => {
            if (method.method === 'versapay_gateway') {
                let deferred = $.Deferred();
                let defferedAction = getTotalsAction([], deferred);
                defferedAction.success(function(){
                    initVersapayPaymentMethod();
                });
            }
        }, null, 'change');

        if (quote.paymentMethod._latestValue?.method === 'versapay_gateway') {
            initVersapayPaymentMethod();
        }

        $(document).on( "customer-data-reload", function(event, sections) {
            if (refreshClientTotals){
                if (sections.indexOf('cart') !== -1) {
                    let deferred = $.Deferred();
                    let defferedAction = getTotalsAction([], deferred);
                    defferedAction.success(function(){
                        if (quote.getPaymentMethod()().method === 'versapay_gateway') {
                            let versapayIframe = document.querySelector('#versapay-container');
                            if (versapayIframe) {
                                let newIframe = versapayIframe.cloneNode();
                                versapayIframe.parentNode.replaceChild(newIframe, versapayIframe);
                                initVersapayPaymentMethod();
                            }
                        }
                    });
                }
            }
        });

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'Versapay_Versapay/payment/form',
                transactionResult: ''
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },

            getCode: function () {
                return 'versapay_gateway';
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                };
            },
            afterPlaceOrder: function () {
                for (var i = 0; i < approvalCodes.length; i++) {
                    window.location.replace(url.build('versapay/sales/savetransaction?approvalCode=' + approvalCodes[i] + '&versapay_orderid=' + versapayOrderId));
                }
            },
            getPaymentMessage: function () {
                return window.checkoutConfig.payment.versapay_gateway.payment_message;
            },
            getSubdomainVersapay: function () {
                return window.checkoutConfig.payment.versapay_gateway.subdomain_versapay;
            },
            getTransactionResults: function () {
                return _.map(window.checkoutConfig.payment.versapay_gateway.transactionResults, function (value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
        });
    }
);

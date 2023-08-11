<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\AmazonPay\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\AmazonPay\Core\Config;
use OxidSolutionCatalysts\AmazonPay\Core\Constants;
use OxidSolutionCatalysts\AmazonPay\Core\Logger;
use OxidSolutionCatalysts\AmazonPay\Core\Payload;
use OxidSolutionCatalysts\AmazonPay\Core\Provider\OxidServiceProvider;
use OxidSolutionCatalysts\AmazonPay\Core\Repository\LogRepository;

class OrderOverview extends OrderOverview_parent
{
    /**
     * @throws DatabaseErrorException
     * @throws DatabaseConnectionException
     */
    public function render()
    {
        $oOrder = oxNew(\OxidSolutionCatalysts\AmazonPay\Model\Order::class);
        $filteredLogs = [];
        $ipnLogs = [];
        $isCaptured = false;
        $isOneStepCapture = false;

        $existingItems = [];

        $orderLoaded = $oOrder->load($this->getEditObjectId());
        /** @var string $paymentType */
        $paymentType = $oOrder->getFieldData('oxpaymenttype');
        if (
            $orderLoaded &&
            Constants::isAmazonPayment($paymentType)
        ) {
            $orderLogs = OxidServiceProvider::getAmazonService()->getOrderLogs($oOrder);

            foreach ($orderLogs as $orderLog) {
                $newFilteredLog = [];

                if ($orderLog['OSC_AMAZON_REQUEST_TYPE'] === 'IPN') {
                    $ipnLog = [];
                    $ipnLog['time'] = $orderLog['OXTIMESTAMP'];
                    $ipnLog['identifier'] = $orderLog['OSC_AMAZON_IDENTIFIER'];
                    $ipnLog['requestType'] = $orderLog['OSC_AMAZON_OBJECT_TYPE'];
                    $ipnLogs[] = $ipnLog;
                    continue;
                }

                if (in_array($orderLog['OSC_AMAZON_PAYLOGID'], $existingItems, true)) {
                    continue;
                }

                $existingItems[] = $orderLog['OSC_AMAZON_PAYLOGID'];

                $newFilteredLog['time'] = $orderLog['OXTIMESTAMP'];
                $newFilteredLog['identifier'] = $orderLog['OSC_AMAZON_IDENTIFIER'];
                $newFilteredLog['statusCode'] = $orderLog['OSC_AMAZON_STATUS_CODE'] === '200' ? 'success' : 'error';

                if (strpos($orderLog['OSC_AMAZON_REQUEST_TYPE'], 'Error') !== false) {
                    $newFilteredLog['statusCode'] = 'error';
                    $newFilteredLog['identifier'] = 'ORDER ID:' . $orderLog['OSC_AMAZON_OXORDERID'];
                }

                $newFilteredLog['requestType'] = $orderLog['OSC_AMAZON_RESPONSE_MSG'];
                if ($orderLog['OSC_AMAZON_RESPONSE_MSG'] === 'Captured') {
                    $newFilteredLog['requestType'] = 'Payment captured';
                    $isCaptured = true;
                    $isOneStepCapture = false;
                } elseif ($orderLog['OSC_AMAZON_RESPONSE_MSG'] === 'Completed') {
                    $newFilteredLog['requestType'] = 'Checkout complete';
                    $isOneStepCapture = false;
                } elseif ($orderLog['OSC_AMAZON_RESPONSE_MSG'] === 'Completed & Captured') {
                    $newFilteredLog['requestType'] = 'Checkout & Capture complete';
                    $isOneStepCapture = true;
                    $isCaptured = true;
                } elseif ($orderLog['OSC_AMAZON_RESPONSE_MSG'] === 'Canceled') {
                    $newFilteredLog['requestType'] = 'Canceled';
                    $isCaptured = true;
                } elseif ($orderLog['OSC_AMAZON_RESPONSE_MSG'] === 'Refunded') {
                    $newFilteredLog['requestType'] = 'Refund Complete';
                    $isCaptured = true;
                }

                $filteredLogs[] = $newFilteredLog;
            }

            if (empty($orderLogs)) {
                $filteredLog = [];
                $filteredLog['time'] = 'no data';
                $filteredLog['identifier'] = 'no data';
                $filteredLog['requestType'] = 'no data';
                $filteredLogs[] = $filteredLog;
            }
            $this->addTplParam('orderLogs', $filteredLogs);

            if (empty($ipnLogs)) {
                $ipnLog = [];
                $ipnLog['time'] = 'no data';
                $ipnLog['identifier'] = 'no data';
                $ipnLog['requestType'] = 'no data';
                $ipnLogs[] = $ipnLog;
            }
            $this->addTplParam('ipnLogs', $ipnLogs);
        }

        $this->addtplParam('isOneStepCapture', $isOneStepCapture);
        $this->addTplParam('isCaptured', $isCaptured);

        return parent::render();
    }

    /**
     * @throws DatabaseErrorException
     * @throws DatabaseConnectionException
     */
    public function refundpayment()
    {
        $oOrder = oxNew(Order::class);
        /** @var float $refundAmount */
        $refundAmount = Registry::getRequest()->getRequestParameter("refundAmount");
        $orderLoaded = $oOrder->load($this->getEditObjectId());
        /** @var string $paymentType */
        $paymentType = $oOrder->getFieldData('oxpaymenttype');
        if (
            $orderLoaded &&
            Constants::isAmazonPayment($paymentType) &&
            $oOrder->getId() !== null
        ) {
            $logger = new Logger();
            $errorMessage = OxidServiceProvider::getAmazonService()->createRefund(
                $oOrder->getId(),
                $refundAmount,
                $logger
            );

            if (is_string($errorMessage)) {
                $this->_aViewData["oViewConf"]->setAmazonServiceErrorMessage($errorMessage);
            }
        }
    }

    public function makecharge()
    {
        $oOrder = oxNew(Order::class);
        /** @var float $captureAmount */
        $captureAmount = Registry::getRequest()->getRequestParameter("captureAmount");
        $amazonConfig = oxNew(Config::class);
        $currencyCode = $order->oxorder__oxcurrency->rawValue ?? $amazonConfig->getPresentmentCurrency();
        $orderLoaded = $oOrder->load($this->getEditObjectId());
        /** @var string $paymentType */
        $paymentType = $oOrder->getFieldData('oxpaymenttype');

        if (
            $orderLoaded &&
            Constants::isAmazonPayment($paymentType) &&
            $oOrder->getId() !== null
        ) {
            $chargeId = '';

            $repository = oxNew(LogRepository::class);
            $logMessages = $repository->findLogMessageForOrderId($this->getEditObjectId());
            if (!empty($logMessages)) {
                foreach ($logMessages as $logMessage) {
                    $logsWithChargePermission = $repository->findLogMessageForChargePermissionId(
                        $logMessage['OSC_AMAZON_CHARGE_PERMISSION_ID']
                    );
                    foreach ($logsWithChargePermission as $logWithChargePermission) {
                        if ($logWithChargePermission['OSC_AMAZON_RESPONSE_MSG'] === 'Captured') {
                            return '-1';
                        }
                        $chargeIdSet = isset($logWithChargePermission['OSC_AMAZON_CHARGE_ID'])
                            && $logWithChargePermission['OSC_AMAZON_CHARGE_ID'] !== 'null';
                        if ($chargeIdSet) {
                            $chargeId = $logWithChargePermission['OSC_AMAZON_CHARGE_ID'];
                            break;
                        }
                    }
                }
            }

            OxidServiceProvider::getAmazonService()->capturePaymentForOrder($chargeId, $captureAmount, $currencyCode);
        }
    }
}

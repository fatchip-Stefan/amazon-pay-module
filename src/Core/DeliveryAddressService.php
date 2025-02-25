<?php

namespace OxidSolutionCatalysts\AmazonPay\Core;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;

class DeliveryAddressService
{
    /**
     * avoid the delivery address is saved in core checkout from deladrid session variable and make it readable
     * in amazondeladrid to be used in checkout template to display it
     *
     * @return void
     */
    public function moveInSession()
    {
        $session = $this->getSession();
        $deliveryAddressId = $session->getVariable('deladrid');
        $session->setVariable(Constants::SESSION_TEMP_DELIVERY_ADDRESS_ID, $deliveryAddressId);
        $session->setVariable('deladrid', null);
    }

    /**
     * @return bool
     */
    public function isPaymentInSessionIsAmazonPay()
    {
        $paymentId = $this->getSession()->getVariable('paymentid');

        return $paymentId === Constants::PAYMENT_ID;
    }

    /**
     * @return Address
     */
    public function getTempDeliveryAddressAddress()
    {
        $deliveryAddress = oxNew(Address::class);
        $delAddressId = $this->getSession()->getVariable(Constants::SESSION_TEMP_DELIVERY_ADDRESS_ID);
        if (!$delAddressId) {
            $delAddressId = $this->getSession()->getVariable('deladrid');
        }
        $deliveryAddress->load($delAddressId);

        return $deliveryAddress;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return Session
     */
    protected function getSession()
    {
        return Registry::getSession();
    }
}

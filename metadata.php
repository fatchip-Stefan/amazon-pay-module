<?php

/**
 * This file is part of OXID eSales AmazonPay module.
 *
 * OXID eSales AmazonPay module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales AmazonPay module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales AmazonPay module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2020
 */

use OxidEsales\Eshop\Application\Component\UserComponent as CoreUserComponent;
use OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMain;
use OxidEsales\Eshop\Application\Controller\Admin\OrderList as CoreOrderListController;
use OxidEsales\Eshop\Application\Controller\Admin\OrderMain as OrderMainController;
use OxidEsales\Eshop\Application\Controller\Admin\OrderOverview as CoreOrderOverviewmodel;
use OxidEsales\Eshop\Application\Controller\ArticleDetailsController as CoreArticleDetailsController;
use OxidEsales\Eshop\Application\Controller\FrontendController as CoreFrontendController;
use OxidEsales\Eshop\Application\Controller\OrderController as CoreOrderController;
use OxidEsales\Eshop\Application\Controller\UserController as CoreUserController;
use OxidEsales\Eshop\Application\Model\Article as CoreArticleModel;
use OxidEsales\Eshop\Application\Model\Basket as CoreBasketModel;
use OxidEsales\Eshop\Application\Model\Category as CoreCategoryModel;
use OxidEsales\Eshop\Application\Model\Order as CoreOrderModel;
use OxidEsales\Eshop\Application\Model\User as CoreUserModel;
use OxidEsales\Eshop\Core\ViewConfig as CoreViewConfig;
use OxidEsales\Eshop\Core\InputValidator as CoreInputValidator;
use OxidProfessionalServices\AmazonPay\Component\UserComponent;
use OxidProfessionalServices\AmazonPay\Controller\Admin\ConfigController;
use OxidProfessionalServices\AmazonPay\Controller\Admin\DeliverySetMain as AmazonDeliverySetMain;
use OxidProfessionalServices\AmazonPay\Controller\Admin\OrderListController;
use OxidProfessionalServices\AmazonPay\Controller\Admin\OrderMain as AmazonOrderMain;
use OxidProfessionalServices\AmazonPay\Controller\Admin\OrderOverview as ModuleOrderOverview;
use OxidProfessionalServices\AmazonPay\Controller\AmazonCheckoutController;
use OxidProfessionalServices\AmazonPay\Controller\ArticleDetailsController;
use OxidProfessionalServices\AmazonPay\Controller\DispatchController;
use OxidProfessionalServices\AmazonPay\Controller\OrderController;
use OxidProfessionalServices\AmazonPay\Controller\UserController;
use OxidProfessionalServices\AmazonPay\Core\ViewConfig;
use OxidProfessionalServices\AmazonPay\Core\AmazonInputValidator;
use OxidProfessionalServices\AmazonPay\Model\Article as ModuleArticle;
use OxidProfessionalServices\AmazonPay\Model\Basket as ModuleBasket;
use OxidProfessionalServices\AmazonPay\Model\Category as ModuleCategory;
use OxidProfessionalServices\AmazonPay\Model\Order as ModuleOrder;
use OxidProfessionalServices\AmazonPay\Model\User as ModuleUser;

$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => 'oxps_amazonpay',
    'title' => [
        'de' => 'OXPS :: Amazon Pay - Online-Bezahldienst',
        'en' => 'OXPS :: Amazon Pay - Online-Payment'
    ],
    'description' => [
        'de' => 'Nutzung des Online-Bezahldienstes von amazon.de',
        'en' => 'Use of the online payment service from amazon.com'
    ],
    'thumbnail' => 'out/img/amazon-pay-logo.png',
    'version' => '1.1',
    'author' => 'Oxid Professional Services',
    'url' => '',
    'email' => '',
    'extend' => [
        CoreViewConfig::class => ViewConfig::class,
        CoreUserController::class => UserController::class,
        CoreOrderController::class => OrderController::class,
        CoreArticleDetailsController::class => ArticleDetailsController::class,
        CoreOrderOverviewmodel::class => ModuleOrderOverview::class,
        CoreOrderListController::class => OrderListController::class,
        CoreUserComponent::class => UserComponent::class,
        CoreOrderModel::class => ModuleOrder::class,
        CoreUserModel::class => ModuleUser::class,
        CoreArticleModel::class => ModuleArticle::class,
        CoreBasketModel::class => ModuleBasket::class,
        CoreCategoryModel::class => ModuleCategory::class,
        DeliverySetMain::class => AmazonDeliverySetMain::class,
        OrderMainController::class => AmazonOrderMain::class,
        CoreInputValidator::class => AmazonInputValidator::class,
    ],
    'controllers' => [
        'amazonconfig' => ConfigController::class,
        'amazoncheckout' => AmazonCheckoutController::class,
        'amazondispatch' => DispatchController::class
    ],
    'templates' => [
        'amazonpay/amazonconfig.tpl' => 'oxps/amazonpay/views/admin/tpl/amazonconfig.tpl',
        'amazonpay/amazonbutton.tpl' => 'oxps/amazonpay/views/elements/amazonbutton.tpl',
        'amazonpay/filtered_billing_address.tpl' => 'oxps/amazonpay/views/elements/filtered_billing_address.tpl',
        'amazonpay/filtered_delivery_address.tpl' => 'oxps/amazonpay/views/elements/filtered_delivery_address.tpl',
        'amazonpay/user_checkout_shipping_head_flow.tpl' =>
            'oxps/amazonpay/views/elements/user_checkout_shipping_head_flow.tpl',
        'amazonpay/user_checkout_shipping_head_wave.tpl' =>
            'oxps/amazonpay/views/elements/user_checkout_shipping_head_wave.tpl',
        'amazonpay/basket_btn_next_bottom_flow.tpl' => 'oxps/amazonpay/views/elements/basket_btn_next_bottom_flow.tpl',
        'amazonpay/basket_btn_next_bottom_wave.tpl' => 'oxps/amazonpay/views/elements/basket_btn_next_bottom_wave.tpl',
        'amazonpay/change_payment_block_flow.tpl' => 'oxps/amazonpay/views/elements/change_payment_block_flow.tpl',
        'amazonpay/change_payment_block_wave.tpl' => 'oxps/amazonpay/views/elements/change_payment_block_wave.tpl',
        'amazonpay/change_payment_form_flow.tpl' => 'oxps/amazonpay/views/elements/change_payment_form_flow.tpl',
        'amazonpay/change_payment_form_wave.tpl' => 'oxps/amazonpay/views/elements/change_payment_form_wave.tpl',
        'amazonpay/checkout_order_address_flow.tpl' => 'oxps/amazonpay/views/elements/checkout_order_address_flow.tpl',
        'amazonpay/checkout_order_address_wave.tpl' => 'oxps/amazonpay/views/elements/checkout_order_address_wave.tpl',
        'amazonpay/checkout_order_btn_submit_bottom_flow.tpl' =>
            'oxps/amazonpay/views/elements/checkout_order_btn_submit_bottom_flow.tpl',
        'amazonpay/checkout_order_btn_submit_bottom_wave.tpl' =>
            'oxps/amazonpay/views/elements/checkout_order_btn_submit_bottom_wave.tpl',
        'amazonpay/checkout_user_main_flow.tpl' => 'oxps/amazonpay/views/elements/checkout_user_main_flow.tpl',
        'amazonpay/checkout_user_main_wave.tpl' => 'oxps/amazonpay/views/elements/checkout_user_main_wave.tpl',
        'amazonpay/shippingandpayment_flow.tpl' => 'oxps/amazonpay/views/elements/shippingandpayment_flow.tpl',
        'amazonpay/shippingandpayment_wave.tpl' => 'oxps/amazonpay/views/elements/shippingandpayment_wave.tpl',
        'amazonpay/details_productmain_tobasket.tpl' =>
            'oxps/amazonpay/views/elements/details_productmain_tobasket.tpl',
        'amazonpay/dd_layout_page_header_icon_menu_minibasket_functions_flow.tpl' =>
            'oxps/amazonpay/views/elements/dd_layout_page_header_icon_menu_minibasket_functions_flow.tpl',
       'amazonpay/dd_layout_page_header_icon_menu_minibasket_functions_wave.tpl' =>
            'oxps/amazonpay/views/elements/dd_layout_page_header_icon_menu_minibasket_functions_wave.tpl',
    ],
    'events' => [
        'onActivate' => '\OxidProfessionalServices\AmazonPay\Core\Events::onActivate',
        'onDeactivate' => '\OxidProfessionalServices\AmazonPay\Core\Events::onDeactivate'
    ],
    'blocks' => [
        [
            'template' => 'headitem.tpl',
            'block' => 'admin_headitem_inccss',
            'file' => 'views/blocks/admin/admin_headitem_inccss.tpl'
        ],
        [
            'template' => 'deliveryset_main.tpl',
            'block'    => 'admin_deliveryset_main_form',
            'file'     => 'views/blocks/admin/deliveryset_main.tpl',
            'position' => '5'
        ],
        [
            'template' => 'order_overview.tpl',
            'block' => 'admin_order_overview_checkout',
            'file' => 'views/blocks/admin/admin_order_overview_reset_form.tpl',
            'position' => '5'
        ],
        [
            'template' => 'order_overview.tpl',
            'block' => 'admin_order_overview_send_form',
            'file' => 'views/blocks/admin/admin_order_overview_send_form.tpl',
            'position' => '5'
        ],
        [
            'template' => 'order_overview.tpl',
            'block' => 'admin_order_overview_checkout',
            'file' => 'views/blocks/admin/admin_order_overview_checkout.tpl',
            'position' => '5'
        ],
        [
            'template' => 'article_main.tpl',
            'block' => 'admin_article_main_extended',
            'file' => 'views/blocks/admin/admin_article_main_extended.tpl',
            'position' => '5'
        ],
        [
            'template' => 'include/category_main_form.tpl',
            'block' => 'admin_category_main_form',
            'file' => 'views/blocks/admin/category_main_form.tpl',
            'position' => '5'
        ],
        [
            'template' => 'layout/base.tpl',
            'block' => 'base_js',
            'file' => 'views/blocks/layout/base_js.tpl'
        ],
        [
            'template' => 'layout/base.tpl',
            'block' => 'base_style',
            'file' => 'views/blocks/layout/base_style.tpl'
        ],
        [
            'template' => 'form/user_checkout_change.tpl',
            'block' => 'user_checkout_shipping_form',
            'file' => '/views/blocks/form/checkout_shipping_form.tpl',
            'position' => '5'
        ],
        [
            'template' => 'form/user_checkout_change.tpl',
            'block' => 'user_checkout_shipping_change',
            'file' => '/views/blocks/form/checkout_shipping_change.tpl',
            'position' => '5'
        ],
        [
            'template' => 'form/user_checkout_change.tpl',
            'block' => 'user_checkout_shipping_head',
            'file' => '/views/blocks/form/user_checkout_shipping_head.tpl',
            'position' => '5'
        ],
        [
            'template' => 'form/user_checkout_change.tpl',
            'block' => 'user_checkout_billing_feedback',
            'file' => '/views/blocks/form/checkout_billing_feedback.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/details/inc/productmain.tpl',
            'block' => 'details_productmain_tobasket',
            'file' => '/views/blocks/page/details/inc/details_productmain_tobasket.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/basket.tpl',
            'block' => 'basket_btn_next_bottom',
            'file' => '/views/blocks/page/checkout/basket_btn_next_bottom.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'checkout_order_address',
            'file' => '/views/blocks/page/checkout/checkout_order_address.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'checkout_order_btn_submit_bottom',
            'file' => '/views/blocks/page/checkout/checkout_order_btn_submit_bottom.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'checkout_order_btn_confirm_bottom',
            'file' => '/views/blocks/page/checkout/checkout_order_btn_confirm_bottom.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/order.tpl',
            'block' => 'shippingAndPayment',
            'file' => '/views/blocks/page/checkout/shippingandpayment.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/user.tpl',
            'block' => 'checkout_user_main',
            'file' => '/views/blocks/page/checkout/checkout_user_main.tpl',
            'position' => '5'
        ],
        [
            'template' => 'widget/minibasket/minibasket.tpl',
            'block' => 'dd_layout_page_header_icon_menu_minibasket_functions',
            'file' =>
                '/views/blocks/widget/minibasket/dd_layout_page_header_icon_menu_minibasket_functions.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => '/views/blocks/page/checkout/select_payment.tpl',
            'position' => '5'
        ],
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'change_payment',
            'file' => '/views/blocks/page/checkout/change_payment.tpl',
            'position' => '5'
        ],
    ],
    'settings' => [
        ['name' => 'blAmazonPaySandboxMode', 'type' => 'bool', 'value' => 'false', 'group' => null],
        ['name' => 'sAmazonPayPrivKey', 'type' => 'str', 'value' => '', 'group' => null],
        ['name' => 'sAmazonPayPubKeyId', 'type' => 'str', 'value' => '', 'group' => null],
        ['name' => 'sAmazonPayMerchantId', 'type' => 'str', 'value' => '', 'group' => null],
        ['name' => 'sAmazonPayStoreId', 'type' => 'str', 'value' => '', 'group' => null],
        ['name' => 'blAmazonPayPDP', 'type' => 'bool', 'value' => 'true', 'group' => null],
        ['name' => 'blAmazonPayMinicartAndModal', 'type' => 'bool', 'value' => 'true', 'group' => null],
        ['name' => 'blAmazonPayUseExclusion', 'type' => 'bool', 'value' => 'false', 'group' => null],
        ['name' => 'amazonPayCapType', 'type' => 'str', 'value' => '', 'group' => null],
    ]
];

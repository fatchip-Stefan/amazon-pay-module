[{if $oViewConf->isAmazonActive() && !$oViewConf->isAmazonSessionActive() && !$oViewConf->isAmazonExclude()}]
    [{if $oViewConf->isFlowCompatibleTheme()}]
        [{include file="amazonpay/checkout_order_btn_submit_bottom_flow.tpl"}]
    [{else}]
        [{include file="amazonpay/checkout_order_btn_submit_bottom_wave.tpl"}]
    [{/if}]
[{/if}]
[{$smarty.block.parent}]
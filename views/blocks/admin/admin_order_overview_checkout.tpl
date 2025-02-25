[{$smarty.block.parent}]
[{if $oViewConf->isAmazonPaymentId($edit->oxorder__oxpaymenttype->value)}]
    <tr>
        <td class="edittext"><b>[{oxmultilang ident="OSC_AMAZONPAY_REMARK" suffix="COLON"}]</b></td>
        <td class="edittext">[{$edit->oxorder__osc_amazon_remark->value}] [{$edit->oxorder__oxcurrency->value}]<br></td>
    </tr>
    [{if $edit->oxorder__oxtransstatus->value == 'OK' && $oViewConf->isAmazonActive()}]
        <tr>
            <td class="edittext">
                [{oxmultilang ident="OSC_AMAZONPAY_REFUND_ANNOTATION"}]
                [{$oViewConf->getMaximalRefundAmount($oxid)}]
                [{$edit->oxorder__oxcurrency->value}]
            </td>
        </tr>
        <tr>
            <td>
                <form name="refundpayment" id="refundpayment" action="[{$oViewConf->getSelfLink()}]" >
                    [{$oViewConf->getHiddenSid()}] 
                    <input type="text" name="refundAmount" value="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" />
                    <input type="button" name="refundButton" value="[{oxmultilang ident="OSC_AMAZONPAY_REFUND"}]" onclick="document.refundpayment.submit()" />
                    <input type="hidden" name="oxid" value="[{$oxid}]">
                    <input type="hidden" name="cl" value="order_overview">
                    <input type="hidden" name="fnc" value="refundpayment">
                </form>
            </td>
        </tr>
    [{/if}]
[{/if}]
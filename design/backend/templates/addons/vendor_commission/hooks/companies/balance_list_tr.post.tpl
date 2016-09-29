<td>
    {if $payout.commission_type == "A"}{include file="common/price.tpl" value=$payout.commission}{else}{$payout.commission}%{/if}
</td>

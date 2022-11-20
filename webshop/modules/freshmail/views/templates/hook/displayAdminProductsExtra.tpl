<div class="panel col-lg-12">
    <div class="panel-heading">{l s="Notifications for discounts" mod="freshmail"}</div>
    <div class="table-responsive-row clearfix">
        {include file="{$tpl_path}table.tpl" list=$list_discount}
    </div>
</div>

<div class="panel col-lg-12">
    <div class="panel-heading">{l s="Notifications about availability" mod="freshmail"}</div>
    <div class="table-responsive-row clearfix">
        {include file="{$tpl_path}table.tpl" list=$list_available}
    </div>
</div>
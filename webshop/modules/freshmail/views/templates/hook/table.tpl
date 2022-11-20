<table id="table-freshmail_list_email" class="table freshmail_list_email">
    <thead>
    <tr class="nodrag nodrop">
        <th class=""><span class="title_box">Adres email</span></th>
        <th class=""><span class="title_box">Data dodania</span></th>
        <th class=""><span class="title_box">Status</span></th>
    </tr>
    </thead>

    <tbody>
    {if !empty($list)}
        {foreach $list item}
            <tr class=" odd">
                <td class="pointer" style="width: 50%">
                    {$item['email']}
                </td>
                <td class="pointer" style="width: 25%;" {* onclick="document.location = 'index.php?controller=AdminFreshmailSubscribers&amp;updatefreshmail_list_email=&amp;id_freshmail_list_email=1&amp;token=fada985cf484f4d72a983343a9b2004a'"*}>
                    {$item['date_add']}
                </td>
                <td class="pointer" style="width: 25%">
                    <a class="btn tooltip-link js-delete-customer-row-action dropdown-item" href="{$remove_notification_link}&id_notification={$item['id_freshmail_product_notification']}" >
                        <i class="material-icons">delete</i>
                        Usuń
                    </a>

                </td>
            </tr>
        {/foreach}
    {else}
        <tr>
            <td class="list-empty" colspan="{count($fields_display)+1}">
                <div class="list-empty-msg"><i class="icon-warning-sign list-empty-icon"></i>{l s='No records found'}</div>
            </td>
        </tr>
    {/if}

    </tbody>
</table>

<script type="application/javascript">
    $('.js-delete-customer-row-action').click(function (){
        $.get($(this).attr('href'));
        $(this).parent().parent().remove();
        return false;
    });
</script>
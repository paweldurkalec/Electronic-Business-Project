<div class="panel">
    <div class="content-div ">
       <div class="row ">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <h3 class="card-header">
                                <i class="material-icons">info_outline</i>{l s='Information' mod="freshmail"}
                            </h3>
                            <div class="card-block">
                                <div class="card-text">
                                    <p>{l s='In email template You can use tags:' mod="freshmail"}</p>
                                    {literal}
                                    <ul>
                                        <li>{firstname}</li>
                                        <li>{lastname}</li>
                                        <li>{content}</li>
                                        <li>{shop_url}</li>
                                        <li>{discount_code}</li>
                                        <li>{cartrule_validto}</li>
                                    </ul>
                                    {/literal}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-wrapper">
    <div class="alert alert-info"><button data-dismiss="alert" type="button" class="close">
            ×
        </button>
        {l s='Encourage your customers to buy.' mod='freshmail'}
    </div>
</div>

{literal}
    <script type="application/javascript">
        $('.fm-class').parent().parent().each(function(){
            $(this).css('display', 'none');
        });

        $('.fm-discount-type').each(function (){
            let id_group = $(this).attr('id').replace('fm-discount-type-','');

            setVisible($(this).val(), id_group);
        });

        $('.fm-discount-type').on('change', function (){
            let id_group = $(this).attr('id').replace('fm-discount-type-','');
            $('.fm-class.fm-group-'+id_group).parent().parent().each(function(){
                $(this).css('display', 'none');
            });

            setVisible($(this).val(), id_group);
        });

        function setVisible(value, id_group){
            if('percent' == value ){
                $('.fm-percent.fm-group-'+id_group).parent().parent().css('display','block');
            } else if('code' == value){
                $('.fm-code.fm-group-'+id_group).parent().parent().css('display','block');
            }
        }

    </script>
{/literal}
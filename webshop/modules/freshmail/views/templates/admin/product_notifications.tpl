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
                                        <li>{products_list}</li>
                                        <li>{product_name}</li>
                                        <li>{product_url}</li>
                                        <li>{product_price}</li>
                                        <li>{img}</li>
                                        <li>{img_big}</li>
                                        <li>{content}</li>
                                        <li>{shop_url}</li>
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
        {l s='Take care of relationships. Inform your customer when the product they are waiting for or the price of the product changes.' mod='freshmail'}
    </div>
</div>
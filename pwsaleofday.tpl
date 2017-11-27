{assign var="specificPrice" value=$product->specificPrice.0}
<div class="col-lg-4 col-md-5 act-left">
              <div class="headline">{l s="Скидка дня"}</div>
              <div class="action-box">
                <img src="{$product->image}" alt="">
                <div class="label">
                  {if $specificPrice.reduction_type == 'percentage'}
                    -{$specificPrice.reduction*100}%
                  {elseif $specificPrice.reduction_type == 'amount'}
                    {$specificPrice.reduction} ₽
                  {/if}
                </div>
                <div class="text">
                  <div class="item">
                    <div class="name"><a href="{$product->link}" title="{$product->name|escape:'html':'UTF-8'}" itemprop="url">{$product->name}</a></div>
                    <div class="presense yes">{$product->available_now}</div>

                  </div>
                  <div class="item">
                    <div class="last-price"><span>{displayPrice price=$product->price|number_format:0}</span></div>
                    <div class="price"><span>{displayPrice price=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)|number_format:0}</span></div>
                  </div>
                  <div class="item">
                    <div class="prod-nav">                  
                      <a href="#" class="add_to_cart"><i class="ico"></i>{l s="В корзину"}</a>
                        <a class="buy-one-click">{l s="Купить в 1 клик"}</a>                                         
                    </div>
                  </div>
                </div>
              </div>
            </div>
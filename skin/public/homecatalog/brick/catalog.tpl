{widget_homecatalog_data}
{*<pre>{$homeCatalog|print_r}</pre>*}
{if isset($homeCatalog) && $homeCatalog != null && !empty($hc_conf)}
    {if $hc_conf['type_hc'] === 'categories'}
        <section id="homecatalog" class="homecatalog clearfix">
            <div class="container">
                <p class="h2">{#homecatalog_title#}</p>
                {foreach $homeCatalog as $key => $cat}
                    {if $cat.products}
                        <div class="list-grid product-list">
                            {include file="catalog/loop/product.tpl" data=$cat.products classCol='vignette' nocache}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </section>
    {else}
        <section id="homecatalog" class="homecatalog clearfix">
            <div class="container">
                <p class="h2">{#homecatalog_title#}</p>
                <div class="list-grid product-list" itemprop="mainEntity" itemscope itemtype="http://schema.org/ItemList">
                    {include file="catalog/loop/product.tpl" data=$homeCatalog classCol='vignette' nocache}
                </div>
            </div>
        </section>
    {/if}
{/if}
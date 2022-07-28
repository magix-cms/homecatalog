{widget_homecatalog_data}
{if isset($hc_products) && $hc_products != null}
    <section id="homecatalog" class="clearfix">
        <div class="container">
            <h3 class="h2">{#homecatalog_title#}</h3>
            <div class="vignette-list">
                <div class="row row-center" itemprop="mainEntity" itemscope itemtype="http://schema.org/ItemList">
                    {include file="catalog/loop/product.tpl" data=$hc_products classCol='vignette col-12 col-xs-8 col-sm-6 col-md-4'}
                </div>
            </div>
        </div>
    </section>
{/if}
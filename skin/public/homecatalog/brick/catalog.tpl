{widget_homecatalog_data}
{if isset($hc_products) && $hc_products != null}
    <section id="homecatalog" class="clearfix">
        <div class="container section-block">
            <h3 class="h2">{#homecatalog_title#}</h3>
            <div class="vignette-list">
                <div class="{*row row-center *}owl-carousel owl-theme owl-products">
                    {*{include file="home/loop/category.tpl" data=$categories classCol='vignette'}*}
                    {include file="homecatalog/loop/products.tpl" data=$hc_products classCol='vignette'}
                </div>
            </div>
        </div>
    </section>
{/if}
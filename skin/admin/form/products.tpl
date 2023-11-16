<div class="row">
    <form action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=add&type=products" class="col-ph-12 col-md-6 col-lg-4 validate_form add_to_ullist">
        <fieldset>
            <h2>{#hc_add_product#}</h2>
            <div class="form-group">
                <div class="form-group">
                    <div id="products" class="btn-group btn-block selectpicker" data-clear="true" data-live="true">
                        <a href="#" class="clear"><span class="fa fa-times"></span><span class="sr-only">{#cancel_selection#}</span></a>
                        <button data-id="parent" type="button" class="btn btn-block btn-default dropdown-toggle">
                            <span class="placeholder">{#choose_product#}</span>
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="live-filtering" data-clear="true" data-autocomplete="true" data-keys="true">
                                <label class="sr-only" for="input-products">{#search_in_list#}</label>
                                <div class="search-box">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="search-products">
                                            <span class="fa fa-search"></span>
                                            <a href="#" class="fa fa-times hide filter-clear"><span class="sr-only">{#clear_filter#}</span></a>
                                        </span>
                                        <input type="text" placeholder="Rechercher dans la liste" id="input-products" class="form-control live-search" aria-describedby="search-products" tabindex="1" />
                                    </div>
                                </div>
                                <div id="filter-products" class="list-to-filter tree-display">
                                    <ul class="list-unstyled">
                                        {foreach $products as $p}
                                            <li class="filter-item items" data-filter="{$p.name_p}" data-value="{$p.id_product}" data-id="{$p.id_product}">
                                                {$p.id_product} - {$p.name_p}
                                            </li>
                                        {/foreach}
                                    </ul>
                                    <div class="no-search-results">
                                        <div class="alert alert-warning" role="alert"><i class="fa fa-warning margin-right-sm"></i>{sprintf(#hc_no_entry_for#,"<strong>'<span></span>'</strong>")}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="products_id" id="products_id" class="form-control mygroup" value="" />
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-main-theme" type="submit"><span class="fa fa-plus"></span> {#add#}</button>
            </div>
        </fieldset>
    </form>
    <div class="col-ph-12 col-md-6 col-lg-4">
        <h2>{#hc_on_homepage#}</h2>
        <ul id="table-products" class="list-group sortable" data-tabs="products" role="tablist">
            {foreach $hc_products as $hc}
                {include file="loop/products.tpl"}
            {/foreach}
        </ul>
        <p class="no-entry alert alert-info{if {$hc_products|count}} hide{/if}">
            <span class="fa fa-info"></span> {#hc_no_products#}
        </p>
    </div>
</div>
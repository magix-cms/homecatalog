<div class="row">
    <form action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=add&type=category" class="col-ph-12 col-md-6 col-lg-4 validate_form add_to_ullist">
        <fieldset>
            <h2>{#hc_add_category#}</h2>
            <div class="form-group">
                <div class="form-group">
                    <div id="cats" class="btn-group btn-block selectpicker" data-clear="true" data-live="true">
                        <a href="#" class="clear"><span class="fa fa-times"></span><span class="sr-only">{#cancel_selection#}</span></a>
                        <button data-id="parent" type="button" class="btn btn-block btn-default dropdown-toggle">
                            <span class="placeholder">{#choose_category#}</span>
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="live-filtering" data-clear="true" data-autocomplete="true" data-keys="true">
                                <label class="sr-only" for="input-cats">{#search_in_list#}</label>
                                <div class="search-box">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="search-cats">
                                            <span class="fa fa-search"></span>
                                            <a href="#" class="fa fa-times hide filter-clear"><span class="sr-only">{#clear_filter#}</span></a>
                                        </span>
                                        <input type="text" placeholder="Rechercher dans la liste" id="input-cats" class="form-control live-search" aria-describedby="search-cats" tabindex="1" />
                                    </div>
                                </div>
                                <div id="filter-cats" class="list-to-filter tree-display">
                                    <ul class="list-unstyled">
                                        {foreach $categories as $c}
                                            <li class="filter-item items" data-filter="{$c.name_cat}" data-value="{$c.id_cat}" data-id="{$c.id_cat}">
                                                {$c.name_cat}&nbsp;{if $c.id_parent != '0'}<small>({$c.id_cat})</small>{/if}
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
                    <input type="hidden" name="cats_id" id="cats_id" class="form-control mygroup" value="" />
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-main-theme" type="submit"><span class="fa fa-plus"></span> {#add#}</button>
            </div>
        </fieldset>
    </form>
    <div class="col-ph-12 col-md-6 col-lg-4">
        <h2>{#hc_on_homepage#}</h2>
        <ul id="table-category" class="list-group sortable" data-tabs="category" role="tablist">
            {foreach $hc_categories as $hc}
                {include file="loop/category.tpl"}
            {/foreach}
        </ul>
        <p class="no-entry alert alert-info{if {$hc_categories|count}} hide{/if}">
            <span class="fa fa-info"></span> {#hc_no_cats#}
        </p>
    </div>
</div>
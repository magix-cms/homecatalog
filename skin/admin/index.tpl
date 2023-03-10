{extends file="layout.tpl"}
{block name='head:title'}homecatalog{/block}
{block name='body:id'}homecatalog{/block}
{block name="stylesheets" append}
    <link rel="stylesheet" href="/{baseadmin}/min/?f=plugins/{$smarty.get.controller}/css/admin.min.css" media="screen" />
{/block}
{block name='article:header'}
    <h1 class="h2">{#homecatalog_plugin#}</h1>
{/block}
{block name="article:content"}
    {if {employee_access type="view" class_name=$cClass} eq 1}
        <div class="panels row">
            <section class="panel col-ph-12">
                {if $debug}
                    {$debug}
                {/if}
                <header class="panel-header panel-nav">
                    <h2 class="panel-heading h5">{#root_homecatalog#}</h2><ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{if $hcconfig.type_hc === 'products'}{#hc_products#}{else}{#hc_category#}{/if}</a></li>
                        <li role="presentation"><a href="#config" aria-controls="config" role="tab" data-toggle="tab">{#hc_config#}</a></li>
                    </ul>
                </header>
                <div class="panel-body panel-body-form">
                    <div class="mc-message-container clearfix">
                        <div class="mc-message"></div>
                    </div>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="general">
                            {if $hcconfig.type_hc === 'products'}{include file="form/products.tpl"}{else}{include file="form/category.tpl"}{/if}
                        </div>
                        <div role="tabpanel" class="tab-pane" id="config">
                            <div class="row">
                                <form id="edit_config" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$hcconfig.id_config}" method="post" class="validate_form add_form col-ph-12 col-md-4">
                                    <div class="form-group">
                                        <label>{#type_hc#}</label>
                                        <div id="hc_type" class="btn-group">
                                            <label class="btn {if $hcconfig.type_hc === 'products'}btn-main-theme{else}btn-default{/if}">
                                                <input type="radio" name="hcconfig[type_hc]" value="products" id="hc_pro" autocomplete="off"{if $hcconfig.type_hc === 'products'} checked{/if}> {#hc_products#}
                                            </label>
                                            <label class="btn {if $hcconfig.type_hc === 'categories'}btn-main-theme{else}btn-default{/if}">
                                                <input type="radio" name="hcconfig[type_hc]" value="categories" id="hc_cat" autocomplete="off"{if $hcconfig.type_hc === 'categories'} checked{/if}> {#hc_category#}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="limit">{#limit_hc#}</label>
                                        <input class="form-control" type="number" name="hcconfig[limit_hc]" id="limit" value="{$hcconfig.limit_hc}" min="1" max="24">
                                    </div>
                                    <div class="form-group">
                                        <div class="switch">
                                            <input type="checkbox" id="random" name="hcconfig[sort_hc]" class="switch-native-control"{if $hcconfig.sort_hc} checked{/if} />
                                            <div class="switch-bg">
                                                <div class="switch-knob"></div>
                                            </div>
                                        </div>
                                        <label for="random">Aléatoire (uniquement mode catégorie)</label>
                                    </div>
                                    <div id="submit">
                                        <input type="hidden" id="id_config" name="hcconfig[id_config]" value="{$hcconfig.id_config}">
                                        <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    {include file="modal/delete.tpl" data_type='products' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#modal_delete_message#}}
    {include file="modal/error.tpl"}
    {else}
        {include file="section/brick/viewperms.tpl"}
    {/if}
{/block}
{block name="foot" append}
    {capture name="scriptForm"}{strip}
        /{baseadmin}/min/?f=libjs/vendor/jquery-ui-1.12.min.js,
        libjs/vendor/tabcomplete.min.js,
        libjs/vendor/livefilter.min.js,
        libjs/vendor/bootstrap-select.min.js,
        libjs/vendor/filterlist.min.js,
        plugins/homecatalog/js/admin.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
    <script type="text/javascript">
        $(function(){
            if (typeof homecatalog == "undefined")
            {
                console.log("setting is not defined");
            }else{
                var controller = "{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}";
                homecatalog.run(controller);
            }
        });
    </script>
{/block}
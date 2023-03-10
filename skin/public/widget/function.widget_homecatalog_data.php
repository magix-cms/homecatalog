<?php
function smarty_function_widget_homecatalog_data($params, $smarty){
    $modelTemplate = $smarty->tpl_vars['modelTemplate']->value instanceof frontend_model_template ? $smarty->tpl_vars['modelTemplate']->value : new frontend_model_template();
    $collection = new plugins_homecatalog_public($modelTemplate);
    $modelTemplate->addConfigFile(
        [component_core_system::basePath().'/plugins/homecatalog/i18n/'],
        ['public_local_']);
    $modelTemplate->configLoad();
    $smarty->assign('hc_conf',$collection->getConf());
    $smarty->assign('homeCatalog',$collection->getHcs());
}
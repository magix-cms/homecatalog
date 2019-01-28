<?php
function smarty_function_widget_homecatalog_data($params, $template){
    $collection = new plugins_homecatalog_public();
    $template->assign('hc_products',$collection->getHcs());;
}
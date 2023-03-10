<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2021 magix-cms.com <support@magix-cms.com>
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------
 #
 # DISCLAIMER
 #
 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
/**
 * MAGIX CMS
 * @category plugins
 * @package homecatalog
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2021 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 2.0.0
 * @author: Salvatore Di Salvo
 * @name plugins_homecatalog_public
 */
class plugins_homecatalog_public extends plugins_homecatalog_db {
    /**
     * @var frontend_model_template $template
     * @var frontend_model_data $data
     * @var frontend_model_catalog $modelCatalog
     * @var frontend_db_catalog $dbCatalog
     * @var component_format_math $math
     */
    protected
        $template,
        $data,
        $modelCatalog,
        $dbCatalog,
        $math;

    /**
     * @var array $conf
     */
    protected
        $conf;

    /**
     * @var string $lang
     */
    protected
        $lang;

    /**
     * plugins_homecatalog_public constructor.
     * @param null|object|frontend_model_template $t
     */
    public function __construct($t = null) {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
		$this->data = new frontend_model_data($this);
        $this->modelCatalog = new frontend_model_catalog($this->template);
        $this->dbCatalog = new frontend_db_catalog();
        $this->math = new component_format_math();
        $this->lang = $this->template->lang;
    }

	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param string|int|null $id
	 * @param string|null $context
	 * @param bool|string $assign
	 * @return mixed
	 */
	private function getItems(string $type, $id = null, string $context = null, $assign = true) {
		return $this->data->getItems($type, $id, $context, $assign);
	}

    /**
     * Load modules attached to homecatalog
     */
    private function loadModules() {
        if(!isset($this->module)) $this->module = new frontend_model_module();
        if(!isset($this->mods)) $this->mods = $this->module->load_module('homecatalog');
    }

    /**
     * @param array $ids
     * @return array
     */
	private function getBuildProductList(array $ids): array {
        $products = [];
        if(!empty($ids) && isset($ids['listids'])) {
            $conditions = ' JOIN mc_homecatalog_p AS hc ON ( hc.id_product = p.id_product )
		                WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND p.id_product IN ('.$ids['listids'].')
						ORDER BY hc.order_hc ASC';
            $collection = $this->dbCatalog->fetchData(
                ['context' => 'all', 'type' => 'product', 'conditions' => $conditions],
                ['iso' => $this->lang]
            );
            // Get id attribute_data if exists
            $this->loadModules();
            if(isset($this->mods['attribute']) && method_exists($this->mods['attribute'],'getBuildAttribute')) {
                $products = $this->mods['attribute']->getBuildAttribute($collection);
            }
            else {
                foreach ($collection as $item) {
                    $products[] = $this->modelCatalog->setItemShortData($item,null);
                }
            }
        }
		return $products;
	}

    /**
     * @param int|string $id
     * @param int|string $limit
     * @return array
     */
	private function getBuildCatProductList($id, $limit): array {
        $products = [];
        if(!empty($id) && !empty($limit)) {
            if(!$this->conf['sort_hc']) {
                $conditions = ' WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND catalog.id_product IN (SELECT id_product FROM mc_catalog WHERE id_cat = :id_cat)
						ORDER BY catalog.order_p ASC
						LIMIT 0,'.$limit;

                $collection = $this->dbCatalog->fetchData(
                    ['context' => 'all', 'type' => 'product', 'conditions' => $conditions],
                    ['iso' => $this->lang,'id_cat' => $id]
                );
            }
            else {
                $conditions = ' WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND catalog.id_product IN (SELECT id_product FROM mc_catalog WHERE id_cat = '.$id.')';

                $ttp = parent::fetchData(
                    ['context' => 'one', 'type' => 'tot_product', 'conditions' => $conditions],
                    ['iso' => $this->lang],
                );

                $limit = $limit < $ttp['tot'] ? $limit : $ttp['tot'];
                $product_ids = $this->math->getRandomIds($limit,$ttp['tot']);

                //$conditions .= ' AND rows.row_id IN  (' . implode(',',$product_ids) .')';
                $ids = [];
                foreach ($product_ids as $id) $ids[] = "($id)";
                $ids = implode(',',$ids);

                $collection = $this->dbCatalog->fetchData(
                    ['context' => 'all', 'type' => 'rand_product', 'conditions' => $conditions],
                    ['iso' => $this->lang, 'ids' => $ids],
                );
            }

            // Get id attribute_data if exists
            $this->loadModules();
            if(isset($this->mods['attribute']) && method_exists($this->mods['attribute'],'getBuildAttribute')){
                $products = $this->mods['attribute']->getBuildAttribute($collection);
            }
            else {
                foreach ($collection as $item) {
                    $products[] = $this->modelCatalog->setItemShortData($item,null);
                }
            }
        }
		return $products;
	}

    private function setProductData(array $rawData): array {
        $hc = [];
        if (!empty($rawData)) {
            foreach ($rawData as $key => $value) {
                if (isset($value['id_hc'])) {
                    $hc[$key]['id_hc'] = $value['id_hc'];
                }
            }
        }
        return $hc;
    }
    /**
     * @param array $data
     * @return array
     */
    public function extendListProduct(array $data): array {
        return $this->setProductData($data);
    }
    /**
     * @param array $filter
     * @return array
     */
    public function getProductList(array $filter = []): array {
        if(http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);
        $extend = [];
        if(!isset($this->controller)) {
        $conf = $this->getItems('hcconfig', null, 'one', false);
        $hcs = $this->getItems('homeHcs',array('limit'=>$conf['limit_hc']),'one', false);
            if (!empty($hcs) && isset($hcs['listids'])) {
                $extend['extendQueryParams'] = [
                    'select' => [
                        'hc.id_hc'
                    ],
                    'join' => [
                        ['type' => 'LEFT JOIN',
                            'table' => 'mc_homecatalog_p',
                            'as' => 'hc',
                            'on' => [
                                'table' => 'p',
                                'key' => 'id_product'
                            ]
                        ]

                    ],
                    'where' => [
                        [
                            'type' => 'AND',
                            'condition' => 'p.id_product IN (' . $hcs['listids'] . ')'
                        ]
                    ]];
                //AND p.id_product IN ('.$ids['listids'].')
                $extend['newRow'] = ['homecatalog' => 'homecatalog'];
                $extend['collection'] = 'homecatalog';
                //print_r($extend);
            }
        }
        return $extend;
    }
    /**
     * @return array
     */
    public function getConf(): array {
        return $this->getItems('hcconfig',null,'one',false);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getHcs(array $params = []): array {
		$this->conf = $this->getItems('hcconfig',null,'one',false);
        $hcs = [];
		if(!is_array($params) || empty($params)) {
			if($this->conf['type_hc'] === 'products') {
				/*$hcs = $this->getItems('homeHcs',array('limit'=>$this->conf['limit_hc']),'one', false);
				$hcs = $this->getBuildProductList($hcs);*/
                $product = new frontend_controller_catalog();
                $hcs = $product->getProductList(null,false);
			}
			elseif($this->conf['type_hc'] === 'categories') {
                $homeCats = $this->getItems('homeHcc',['lang' => $this->lang],'all', false);
                if(!empty($homeCats)) {
					$catalog = new frontend_controller_catalog();
                    foreach ($homeCats as $cat) {
                        $cat = $this->modelCatalog->setItemShortData($cat);
                        //$cat['products'] = $this->getBuildCatProductList($cat['id_cat'],);
                        $cat['products'] = $catalog->getProductList($cat['id'],false,['limit' => $this->conf['limit_hc']]);
                        $hcs[] = $cat;
                    }
                }
			}
    	}
        return $hcs;
    }
}
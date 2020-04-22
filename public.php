<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2013 magix-cms.com <support@magix-cms.com>
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

 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------

 # DISCLAIMER

 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
 /**
 * MAGIX CMS
 * @category   advantage
 * @package    plugins
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2015 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license    Dual licensed under the MIT or GPL Version 3 licenses.
 * @version    2.0
 * Author: Salvatore Di Salvo
 * Date: 17-12-15
 * Time: 10:38
 * @name plugins_advantage_public
 * Le plugin advantage
 */
class plugins_homecatalog_public extends plugins_homecatalog_db{
    /**
     * @var frontend_model_template
     */
    protected $template, $data, $lang, $modelCatalog, $dbCatalog, $conf, $math;

    /**
     * plugins_homecatalog_public constructor.
     * @param null $t
     */
    public function __construct($t = null){
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
		$this->data = new frontend_model_data($this);
		$this->lang = $this->template->lang;
		$this->modelCatalog = new frontend_model_catalog($this->template);
		$this->dbCatalog = new frontend_db_catalog();
		$this->math = new component_format_math();
    }

	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param string|int|null $id
	 * @param string $context
	 * @param boolean $assign
	 * @return mixed
	 */
	private function getItems($type, $id = null, $context = null, $assign = true) {
		return $this->data->getItems($type, $id, $context, $assign);
	}

    /**
     * set Data from database
     * @access private
     * @param $ids
     * @return array
     * @throws Exception
     */
	private function getBuildProductList($ids)
	{
		$conditions = ' JOIN mc_homecatalog_p AS hc ON ( hc.id_product = p.id_product )
		                WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND p.id_product IN ('.$ids['listids'].')
						ORDER BY hc.order_hc ASC';
		$collection = $this->dbCatalog->fetchData(
			array('context' => 'all', 'type' => 'product', 'conditions' => $conditions),
			array('iso' => $this->lang)
		);
		$newarr = array();
		foreach ($collection as $item) {
			$newarr[] = $this->modelCatalog->setItemData($item,null);
		}
		return $newarr;
	}

    /**
     * set Data from database
     * @access private
     * @param $id
     * @param $limit
     * @return array
     * @throws Exception
     */
	private function getBuildCatProductList($id,$limit)
	{
		if(!$this->conf['sort_hc']) {
			$conditions = ' WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND catalog.id_product IN (SELECT id_product FROM mc_catalog WHERE id_cat = :id_cat)
						ORDER BY catalog.order_p ASC
						LIMIT 0,'.$limit;

			$collection = $this->dbCatalog->fetchData(
				array('context' => 'all', 'type' => 'product', 'conditions' => $conditions),
				array('iso' => $this->lang,'id_cat' => $id)
			);
		}
		else {
			$conditions = ' WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND catalog.id_product IN (SELECT id_product FROM mc_catalog WHERE id_cat = '.$id.')';

			$ttp = parent::fetchData(
				array('context' => 'one', 'type' => 'tot_product', 'conditions' => $conditions),
				array('iso' => $this->lang)
			);

			$limit = $limit < $ttp['tot'] ? $limit : $ttp['tot'];
			$product_ids = $this->math->getRandomIds($limit,$ttp['tot'],1,false);

			//$conditions .= ' AND rows.row_id IN  (' . implode(',',$product_ids) .')';
			$ids = array();
			foreach ($product_ids as $id) $ids[] = "($id)";
			$ids = implode(',',$ids);

			$collection = $this->dbCatalog->fetchData(
				array('context' => 'all', 'type' => 'rand_product', 'conditions' => $conditions),
				array('iso' => $this->lang, 'ids' => $ids)
			);
		}

		$newarr = array();
		foreach ($collection as $item) {
			$newarr[] = $this->modelCatalog->setItemData($item,null);
		}

		return $newarr;
	}

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getHcs($params = array()){
		$this->conf = $this->getItems('hcconfig',null,'one',false);
		if(!is_array($params) || empty($params)) {
			$hcs = null;
			if($this->conf['type_hc'] === 'products') {
				$hcs = $this->getItems('homeHcs',array('limit'=>$this->conf['limit_hc']),'one', false);
				$hcs = $this->getBuildProductList($hcs);
			}
			elseif($this->conf['type_hc'] === 'category') {
				$hcs = $this->getItems('cat',null,'one', false);
				$hcs = $this->getBuildCatProductList($hcs['id_cat'],$this->conf['limit_hc']);
			}
			return $hcs;
    	}
    }
}
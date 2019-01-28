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
    protected $template, $data, $lang, $modelCatalog, $dbCatalog;
    /**
     * Class constructor
     */
    public function __construct(){
        $this->template = new frontend_model_template();
		$this->data = new frontend_model_data($this);
		$this->lang = $this->template->currentLanguage();
		$this->modelCatalog = new frontend_model_catalog($this->template);
		$this->dbCatalog = new frontend_db_catalog();
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
	 * @return array
	 */
	private function getBuildProductList($ids)
	{
		$conditions = ' WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND p.id_product IN ('.$ids['listids'].')
						ORDER BY c2.order_p ASC';
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
	 * @return array
	 */
	private function getBuildCatProductList($id,$limit)
	{
		$conditions = ' WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL)
						AND catalog.default_c = 1 
						AND c2.id_cat = :id_cat
						ORDER BY c2.order_p ASC
						LIMIT 0,'.$limit;
		$collection = $this->dbCatalog->fetchData(
			array('context' => 'all', 'type' => 'product', 'conditions' => $conditions),
			array('iso' => $this->lang,'id_cat' => $id)
		);
		$newarr = array();
		foreach ($collection as $item) {
			$newarr[] = $this->modelCatalog->setItemData($item,null);
		}
		return $newarr;
	}

	/**
	 * @param array $params
	 * @return array
	 */
    public function getHcs($params = array()){
		$config = $this->getItems('hcconfig',null,'one',false);
		if(!is_array($params) || empty($params)) {
			$hcs = null;
			if($config['type_hc'] === 'products') {
				$hcs = $this->getItems('homeHcs',array('limit'=>$config['limit_hc']),'one', false);
				$hcs = $this->getBuildProductList($hcs);
			}
			elseif($config['type_hc'] === 'category') {
				$hcs = $this->getItems('cat',null,'one', false);
				$hcs = $this->getBuildCatProductList($hcs['id_cat'],$config['limit_hc']);
			}
			return $hcs;
    	}
    }
}
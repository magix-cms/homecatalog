<?php
require_once ('db.php');
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
 * @category   advantage
 * @package    plugins
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2015 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license    Dual licensed under the MIT or GPL Version 3 licenses.
 * @version    2.0
 * Author: Salvatore Di Salvo
 * Date: 16-12-15
 * Time: 14:00
 * @name plugins_advantage_admin
 * Le plugin advantage
 */
class plugins_homecatalog_admin extends plugins_homecatalog_db {
	/**
	 * @var object
	 */
	protected $controller,
		$data,
		$template,
		$message,
		$plugins,
		$modelLanguage,
		$collectionLanguage,
		$header,
		$settings,
		$setting;

	/**
	 * Les variables globales
	 * @var integer $edit
	 * @var string $action
	 * @var string $tabs
	 */
	public $edit = 0,
		$action = '',
		$tabs = '';

	/**
	 * Les variables plugin
	 * @var array $adv
	 * @var integer $id
	 * @var array $advantage
	 */
	public
		$id = 0,
		$type = 'products',
		$hcconfig = array(),
		$product = array();

    /**
	 * Construct class
	 */
	public function __construct(){
		$this->template = new backend_model_template();
		$this->plugins = new backend_controller_plugins();
		$this->message = new component_core_message($this->template);
		$this->modelLanguage = new backend_model_language($this->template);
		$this->collectionLanguage = new component_collections_language();
		$this->data = new backend_model_data($this);
		$this->settings = new backend_model_setting();
		$this->setting = $this->settings->getSetting();
		$this->header = new http_header();

		$formClean = new form_inputEscape();

		// --- GET
		if(http_request::isGet('controller')) {
			$this->controller = $formClean->simpleClean($_GET['controller']);
		}
		if (http_request::isGet('edit')) {
			$this->edit = $formClean->numeric($_GET['edit']);
		}
		if (http_request::isGet('action')) {
			$this->action = $formClean->simpleClean($_GET['action']);
		} elseif (http_request::isPost('action')) {
			$this->action = $formClean->simpleClean($_POST['action']);
		}
		if (http_request::isGet('tabs')) {
			$this->tabs = $formClean->simpleClean($_GET['tabs']);
		}
		if (http_request::isGet('type')) {
			$this->type = $formClean->simpleClean($_GET['type']);
		}

		// --- ADD or EDIT
		if (http_request::isPost('products_id')) {
			$this->id = intval($formClean->numeric($_POST['products_id']));
		}
		if (http_request::isPost('cats_id')) {
			$this->id = intval($formClean->numeric($_POST['cats_id']));
		}
		if (http_request::isPost('id')) {
			$this->id = intval($formClean->simpleClean($_POST['id']));
		}

		// --- Order
		if (http_request::isPost('order')) {
			$this->product = $formClean->arrayClean($_POST['order']);
		}

		if (http_request::isPost('hcconfig')) {
			$this->hcconfig = $formClean->arrayClean($_POST['hcconfig']);
		}
	}

	/**
	 * Method to override the name of the plugin in the admin menu
	 * @return string
	 */
	public function getExtensionName()
	{
		return $this->template->getConfigVars('homecatalog_plugin');
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
	 * Insert data
	 * @param array $config
	 */
	private function add($config)
	{
		switch ($config['type']) {
			case 'hc_p':
			case 'hc_c':
				parent::insert(
					array('type' => $config['type']),
					$config['data']
				);
				break;
		}
	}

	/**
	 * Update data
	 * @param array $config
	 */
	private function upd($config)
	{
		switch ($config['type']) {
			case 'hc_c':
			case 'config':
				parent::update(
					array('type' => $config['type']),
					$config['data']
				);
				break;
		}
	}

	/**
	 * Delete a record
	 * @param $config
	 */
	private function del($config)
	{
		switch ($config['type']) {
			case 'hc':
				parent::delete(
					array('type' => $config['type']),
					$config['data']
				);
				$this->message->json_post_response(true,'delete',array('id' => $this->id));
				break;
		}
	}

	/**
	 * Update order
	 */
	public function order(){
		$p = $this->product;
		for ($i = 0; $i < count($p); $i++) {
			parent::update(
				array(
					'type' => 'order'
				),
				array(
					'id_hc'    => $p[$i],
					'order_hc' => $i
				)
			);
		}
	}

	/**
	 * Execute the plugin
	 */
	public function run()
	{
		if($this->action) {
			switch ($this->action) {
				case 'add':
					if($this->type === 'products'){
						$this->add(array(
							'type' => 'hc_p',
							'data' => array('id' => $this->id)
						));
						$defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
						$this->getItems('newHc',array('default_lang'=>$defaultLanguage['id_lang']),'one','hc');
						$this->modelLanguage->getLanguage();
						$display = $this->template->fetch('loop/product.tpl');
						$this->message->json_post_response(true,'add',array('result' => $display,'extend' => array('id' =>$this->id)));
					}
					elseif($this->type === 'category'){
						$cat = $this->getItems('cat',null,'one',false);
						if($cat) {
							$this->upd(array(
								'type' => 'hc_c',
								'data' => array(
									'id' => $this->id,
									'id_hc' => $cat['id_hc']
								)
							));
						}
						else {
							$this->add(array(
								'type' => 'hc_c',
								'data' => array('id' => $this->id)
							));
						}
						$this->message->json_post_response(true,'update');
					}
					break;
				case 'edit':
					if(isset($this->hcconfig)) {
						$this->hcconfig['sort_hc'] = !isset($this->hcconfig['sort_hc']) ? 0 : 1;
						$this->upd(array(
							'type' => 'config',
							'data' => $this->hcconfig
						));
						$this->message->json_post_response(true, 'add_redirect', array('result'=>$this->hcconfig['id_config']));
					}
					break;
				case 'delete':
					if(isset($this->id) && !empty($this->id)) {
						$this->del(
							array(
								'type' => 'hc',
								'data' => array(
									'id' => $this->id
								)
							)
						);
					}
					break;
				case 'order':
					if (isset($this->product) && is_array($this->product)) {
						$this->order();
					}
					break;
			}
		}
		else {
			$this->modelLanguage->getLanguage();
			$defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
			$config = $this->getItems('hcconfig',null,'one');
			$this->getItems('hcs',array('default_lang'=>$defaultLanguage['id_lang']),'all');
			if($config['type_hc'] === 'products') $this->getItems('products',array('default_lang'=>$defaultLanguage['id_lang']),'all');
			elseif($config['type_hc'] === 'category') {
				$this->getItems('cat',null,'one','hc_c');
				$this->getItems('cats',array('default_lang'=>$defaultLanguage['id_lang']),'all');
			}
			$this->template->display('index.tpl');
		}
	}
}
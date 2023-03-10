<?php
require_once ('db.php');
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
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2021 Gerits Aurelien, http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 2.0.0
 * @author: Salvatore Di Salvo
 * @name plugins_homecatalog_admin
 */
class plugins_homecatalog_admin extends plugins_homecatalog_db {
	/**
	 * @var backend_model_template $template
	 * @var backend_model_data $data
	 * @var component_core_message $message
	 * @var backend_controller_plugins $plugins
	 * @var backend_model_language $modelLanguage
	 * @var component_collections_language $collectionLanguage
	 */
    protected backend_model_template $template;
	protected backend_model_data $data;
	protected component_core_message $message;
    protected backend_controller_plugins $plugins;
    protected backend_model_language $modelLanguage;
    protected component_collections_language $collectionLanguage;

	/**
	 * @var array $setting
	 */
	protected array $setting;

	/**
	 * @var integer $edit
	 * @var integer $id
	 */
	public int
        $edit,
        $id;

	/**
	 * @var string $action
	 * @var string $tabs
	 * @var string $type
	 */
	public string
        $action,
        $tabs,
		$type = 'products',$controller;

	/**
	 * @var array $hcconfig
	 * @var array $product
	 */
	public array
		$hcconfig,
		$order;

    /**
	 * Construct class
	 */
    public function __construct(backend_model_template $t = null) {
        $this->template = $t instanceof backend_model_template ? $t : new backend_model_template();
		$this->plugins = new backend_controller_plugins();
		$this->message = new component_core_message($this->template);
		$this->modelLanguage = new backend_model_language($this->template);
		$this->collectionLanguage = new component_collections_language();
		$this->data = new backend_model_data($this);
		$this->setting = $this->template->settings;
	}

	/**
	 * Method to override the name of the plugin in the admin menu
	 * @return string
	 */
	public function getExtensionName(): string {
		return $this->template->getConfigVars('homecatalog_plugin');
	}

	// --- Database actions
	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param string|int|null $id
	 * @param ?string $context
	 * @param string|bool $assign
	 * @return mixed
	 */
	private function getItems(string $type, $id = null, ?string $context = null, $assign = true) {
		return $this->data->getItems($type, $id, $context, $assign);
	}

	/**
	 * Insert data
	 * @param string $type
	 * @param array $params
	 */
	private function add(string $type, array $params) {
		switch ($type) {
			case 'hc_products':
			case 'hc_category':
				parent::insert($type, $params);
				break;
		}
	}

	/**
	 * Update data
	 * @param string $type
	 * @param array $params
	 */
	private function upd(string $type, array $params) {
		switch ($type) {
			case 'hc_c':
			case 'config':
				parent::update($type, $params);
				break;
		}
	}

	/**
	 * Delete a record
	 * @param string $type
	 * @param array $params
	 */
	private function del(string $type, array $params) {
		switch ($type) {
			case 'hc_products':
			case 'hc_category':
				parent::delete($type, $params);
				$this->message->json_post_response(true,'delete',['id' => $this->id]);
				break;
		}
	}
	// --------------------

	// --- Methods
    /**
     * Update order
     * @param string $type
     */
	private function order(string $type) {
		if(!empty($type)) {
            $p = $this->order;
            for ($i = 0; $i < count($p); $i++) {
                parent::update('order_'.$type, ['id_hc' => $p[$i], 'order_hc' => $i]);
            }
        }
	}
	// --------------------

	/**
	 * @return void
	 */
	public function run() {
		// --- GET
		if (http_request::isGet('edit')) $this->edit = form_inputEscape::numeric($_GET['edit']);
		if (http_request::isGet('tabs')) $this->tabs = form_inputEscape::simpleClean($_GET['tabs']);
		if (http_request::isGet('type')) $this->type = form_inputEscape::simpleClean($_GET['type']);
		if (http_request::isRequest('action')) $this->action = form_inputEscape::simpleClean($_REQUEST['action']);
        if(http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);

		if(http_request::isMethod('POST') && isset($this->action)) {
			// --- ADD or EDIT
			if (http_request::isPost('products_id')) $this->id = form_inputEscape::numeric($_POST['products_id']);
			if (http_request::isPost('cats_id')) $this->id = form_inputEscape::numeric($_POST['cats_id']);
			if (http_request::isPost('id')) $this->id = form_inputEscape::numeric($_POST['id']);

			// --- Order
			if (http_request::isPost('order')) $this->order = form_inputEscape::arrayClean($_POST['order']);
			if (http_request::isPost('hcconfig')) $this->hcconfig = form_inputEscape::arrayClean($_POST['hcconfig']);

			switch ($this->action) {
				case 'add':
                    $defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
                    $this->modelLanguage->getLanguage();
					if($this->type === 'products' || $this->type === 'category'){
						$this->add('hc_'.$this->type, ['id' => $this->id]);
						$this->getItems('newHc_'.$this->type,['default_lang'=>$defaultLanguage['id_lang']],'one','hc');
						$display = $this->template->fetch('loop/'.$this->type.'.tpl');
						$this->message->json_post_response(true,'add',['result' => $display,'extend' => [['id' =>$this->id]]]);
					}
					break;
				case 'edit':
					if(isset($this->hcconfig)) {
						$this->hcconfig['sort_hc'] = !isset($this->hcconfig['sort_hc']) ? 0 : 1;
						$this->upd('config', $this->hcconfig);
						$this->message->json_post_response(true, 'add_redirect', ['result'=>$this->hcconfig['id_config']]);
					}
					break;
				case 'delete':
					if(isset($this->id) && !empty($this->id)) {
						$this->del('hc_'.$this->tabs, ['id' => $this->id]);
					}
					break;
				case 'order':
					if (isset($this->order)) $this->order($this->tabs);
					break;
			}
		}
		else {
			$this->modelLanguage->getLanguage();
			$defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
			$config = $this->getItems('hcconfig',null,'one');
			if(!empty($config['type_hc'])) {
                $this->getItems('hc_'.$config['type_hc'],['default_lang'=>$defaultLanguage['id_lang']],'all');
                $this->getItems($config['type_hc'],['default_lang'=>$defaultLanguage['id_lang']],'all');
            }
			$this->template->display('index.tpl');
		}
	}
}
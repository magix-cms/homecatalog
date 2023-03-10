<?php
class plugins_homecatalog_db {
	/**
	 * @var debug_logger $logger
	 */
	protected debug_logger $logger;
	
	/**
	 * @param array $config
	 * @param array $params
	 * @return array|bool
	 */
	public function fetchData(array $config, array $params = []) {
		if ($config['context'] === 'all') {
			switch ($config['type']) {
				case 'hc_products':
					$query = 'SELECT 
								hc.id_hc,
								hc.id_product,
								pc.name_p
							FROM mc_homecatalog_p AS hc
							JOIN mc_catalog_product_content AS pc USING(id_product)
							JOIN mc_lang AS lang ON(pc.id_lang = lang.id_lang)
							WHERE pc.id_lang = :default_lang
							ORDER BY order_hc';
					break;
				case 'hc_categories':
					$query = 'SELECT 
								hc.id_hc,
								hc.id_cat,
								cc.name_cat
							FROM mc_homecatalog_c AS hc
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang
							ORDER BY order_hc';
					break;
				case 'products':
					$query = 'SELECT 
								p.id_product,
								pc.name_p
							FROM mc_catalog_product AS p
							JOIN mc_catalog_product_content AS pc USING(id_product)
							JOIN mc_lang AS lang ON(pc.id_lang = lang.id_lang)
							WHERE pc.id_lang = :default_lang AND pc.published_p = 1
							AND p.id_product NOT IN (
							    SELECT id_product FROM mc_homecatalog_p
							)';
					break;
				case 'categories':
					$query = 'SELECT 
								c.id_cat,
								cc.name_cat
							FROM mc_catalog_cat AS c
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang';
					break;
                case 'homeHcc':
                    $query = 'SELECT 
								mhc.id_hc,
								mhc.id_cat,
								mccc.name_cat,
                                mccc.url_cat,
                                mcc.img_cat,
                                COALESCE(mccc.alt_img, mccc.name_cat) as alt_img,
								COALESCE(mccc.title_img, mccc.alt_img, mccc.name_cat) as title_img,
								COALESCE(mccc.caption_img, mccc.title_img, mccc.alt_img, mccc.name_cat) as caption_img,
                                ml.iso_lang
							FROM mc_homecatalog_c mhc
							JOIN mc_catalog_cat mcc USING(id_cat)
							JOIN mc_catalog_cat_content mccc USING(id_cat)
							JOIN mc_lang ml ON(mccc.id_lang = ml.id_lang)
							WHERE ml.iso_lang = :lang 
                            ORDER BY order_hc ';
                    break;
				default:
					return false;
			}

			try {
				return component_routing_db::layer()->fetchAll($query, $params);
			}
			catch (Exception $e) {
				if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
				$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			}
		}
		elseif ($config['context'] === 'one') {
			switch ($config['type']) {
                case 'hcconfig':
                    $query = 'SELECT * FROM mc_homecatalog ORDER BY id_config DESC LIMIT 0,1';
                    break;
				case 'newHc_products':
					$query = 'SELECT 
								hc.id_hc,
								hc.id_product,
								pc.name_p
							FROM mc_homecatalog_p AS hc
							JOIN mc_catalog_product_content AS pc USING(id_product)
							JOIN mc_lang AS lang ON(pc.id_lang = lang.id_lang)
							WHERE pc.id_lang = :default_lang ORDER BY id_hc DESC LIMIT 0,1';
					break;
				case 'newHc_category':
					$query = 'SELECT 
								hc.id_hc,
								hc.id_cat,
								cc.name_cat
							FROM mc_homecatalog_c AS hc
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang ORDER BY id_hc DESC LIMIT 0,1';
					break;
				case 'homeHcs':
					$query = "SELECT substring_index(GROUP_CONCAT( `id_product` ORDER BY order_hc SEPARATOR ','), ',', :limit) AS listids FROM mc_homecatalog_p";
					break;
                case 'tot_product':
                    $config["conditions"] ? $conditions = $config["conditions"] : $conditions = '';
                    $query = "SELECT 
								COUNT(DISTINCT p.id_product) as tot
							FROM mc_catalog AS catalog
							JOIN mc_catalog_cat AS c ON ( catalog.id_cat = c.id_cat )
							JOIN mc_catalog_cat_content AS cat ON ( c.id_cat = cat.id_cat )
							JOIN mc_catalog_product AS p ON ( catalog.id_product = p.id_product )
							JOIN mc_catalog_product_content AS pc ON ( p.id_product = pc.id_product )
							LEFT JOIN mc_catalog_product_img AS img ON (p.id_product = img.id_product)
							LEFT JOIN mc_catalog_product_img_content AS imgc ON (imgc.id_img = img.id_img and pc.id_lang = imgc.id_lang)
							JOIN mc_lang AS lang ON ( pc.id_lang = lang.id_lang ) AND (cat.id_lang = lang.id_lang) $conditions";
                    break;
				default:
					return false;
			}

			try {
				return component_routing_db::layer()->fetch($query, $params);
			}
			catch (Exception $e) {
				if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
				$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			}
		}
		return false;
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
	public function insert(string $type, array $params = []): bool {
		switch ($type) {
			case 'hc_products':
				$query = 'INSERT INTO mc_homecatalog_p (id_product, order_hc)  
						SELECT :id, COUNT(order_hc) FROM mc_homecatalog_p';
				break;
			case 'hc_category':
				$query = 'INSERT INTO mc_homecatalog_c (id_cat, order_hc)
                        SELECT :id, COUNT(order_hc) FROM mc_homecatalog_c';
				break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->insert($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
	public function update(string $type, array $params = []): bool {
		switch ($type) {
			case 'order_products':
				$query = 'UPDATE mc_homecatalog_p 
						SET order_hc = :order_hc
						WHERE id_hc = :id_hc';
				break;
			case 'order_category':
				$query = 'UPDATE mc_homecatalog_c 
						SET order_hc = :order_hc
						WHERE id_hc = :id_hc';
				break;
			case 'config':
				$query = 'UPDATE mc_homecatalog 
						SET 
							type_hc = :type_hc,
							limit_hc = :limit_hc,
							sort_hc = :sort_hc
						WHERE id_config = :id_config';
				break;
			default:
				return false;
		}

		try {
			component_routing_db::layer()->update($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @return bool
	 */
	public function delete(string $type, array $params = []): bool {
        switch ($type) {
            case 'hc_products':
                $query = 'DELETE FROM mc_homecatalog_p WHERE id_hc = :id';
                break;
            case 'hc_category':
                $query = 'DELETE FROM mc_homecatalog_c WHERE id_hc = :id';
                break;
			default:
				return false;
        }

		try {
			component_routing_db::layer()->delete($query,$params);
			return true;
		}
		catch (Exception $e) {
			if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
			$this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
			return false;
		}
	}
}
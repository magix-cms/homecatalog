<?php
class plugins_homecatalog_db
{
	/**
	 * @param $config
	 * @param bool $params
	 * @return mixed|null
	 * @throws Exception
	 */
	public function fetchData($config, $params = false)
	{
		if (!is_array($config)) return '$config must be an array';

		$sql = '';

		if ($config['context'] === 'all') {
			switch ($config['type']) {
				case 'hcs':
					$sql = 'SELECT 
								hc.id_hc,
								hc.id_product,
								pc.name_p
							FROM mc_homecatalog_p AS hc
							JOIN mc_catalog_product_content AS pc USING(id_product)
							JOIN mc_lang AS lang ON(pc.id_lang = lang.id_lang)
							WHERE pc.id_lang = :default_lang
							ORDER BY order_hc';
					break;
				case 'products':
					$sql = 'SELECT 
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
				case 'cats':
					$sql = 'SELECT 
								c.id_cat,
								cc.name_cat
							FROM mc_catalog_cat AS c
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang';
					break;
			}

			return $sql ? component_routing_db::layer()->fetchAll($sql, $params) : null;
		}
		elseif ($config['context'] === 'one') {
			switch ($config['type']) {
				case 'newHc':
					$sql = 'SELECT 
								hc.id_hc,
								hc.id_product,
								pc.name_p
							FROM mc_homecatalog_p AS hc
							JOIN mc_catalog_product_content AS pc USING(id_product)
							JOIN mc_lang AS lang ON(pc.id_lang = lang.id_lang)
							WHERE pc.id_lang = :default_lang ORDER BY id_hc DESC LIMIT 0,1';
					break;
				case 'homeHcs':
					$sql = "SELECT substring_index(GROUP_CONCAT( `id_product` ORDER BY order_hc SEPARATOR ','), ',', :limit) AS listids FROM mc_homecatalog_p";
					break;
				case 'cat':
					$sql = 'SELECT * FROM mc_homecatalog_c ORDER BY id_hc DESC LIMIT 0,1';
					break;
				case 'hcconfig':
					$sql = 'SELECT * FROM mc_homecatalog ORDER BY id_config DESC LIMIT 0,1';
					break;
				case 'tot_product':
					$config["conditions"] ? $conditions = $config["conditions"] : $conditions = '';
					$sql = "SELECT 
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
			}

			return $sql ? component_routing_db::layer()->fetch($sql, $params) : null;
		}
	}

	/**
	 * @param $config
	 * @param array $params
	 * @return bool|string
	 */
	public function insert($config, $params = array())
	{
		if (!is_array($config)) return '$config must be an array';

		$sql = '';

		switch ($config['type']) {
			case 'hc_p':
				$sql = 'INSERT INTO mc_homecatalog_p (id_product, order_hc)  
						SELECT :id, COUNT(order_hc) FROM mc_homecatalog_p';
				break;
			case 'hc_c':
				$sql = 'INSERT INTO mc_homecatalog_c (id_cat) VALUES (:id)';
				break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->insert($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}

	/**
	 * @param $config
	 * @param array $params
	 * @return bool|string
	 */
	public function update($config, $params = array())
	{
		if (!is_array($config)) return '$config must be an array';

		$sql = '';

		switch ($config['type']) {
			case 'order':
				$sql = 'UPDATE mc_homecatalog_p 
						SET order_hc = :order_hc
						WHERE id_hc = :id_hc';
				break;
			case 'hc_c':
				$sql = 'UPDATE mc_homecatalog_c 
						SET id_cat = :id
						WHERE id_hc = :id_hc';
				break;
			case 'config':
				$sql = 'UPDATE mc_homecatalog 
						SET 
							type_hc = :type_hc,
							limit_hc = :limit_hc,
							sort_hc = :sort_hc
						WHERE id_config = :id_config';
				break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->update($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}

	/**
	 * @param $config
	 * @param array $params
	 * @return bool|string
	 */
	public function delete($config, $params = array())
	{
		if (!is_array($config)) return '$config must be an array';
			$sql = '';

			switch ($config['type']) {
				case 'hc':
					$sql = 'DELETE FROM mc_homecatalog_p
							WHERE id_hc = :id';
					break;
			}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->delete($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}
}
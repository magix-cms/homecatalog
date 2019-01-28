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
							WHERE pc.id_lang = :default_lang';
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
					$sql = "SELECT GROUP_CONCAT( `id_product` SEPARATOR ',' ) AS listids FROM mc_homecatalog_p ORDER BY order_hc LIMIT 0,:limit";
					break;
				case 'cat':
					$sql = 'SELECT * FROM mc_homecatalog_c ORDER BY id_hc DESC LIMIT 0,1';
					break;
				case 'hcconfig':
					$sql = 'SELECT * FROM mc_homecatalog ORDER BY id_config DESC LIMIT 0,1';
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
						WHERE id_hc = :id';
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
							limit_hc = :limit_hc
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
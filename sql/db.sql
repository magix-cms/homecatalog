CREATE TABLE IF NOT EXISTS `mc_homecatalog_p` (
  `id_hc` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL,
  `order_hc` smallint(3) unsigned NOT NULL default 0,
  PRIMARY KEY (`id_hc`),
  KEY `id_product` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `mc_homecatalog_p`
  ADD CONSTRAINT `mc_homecatalog_p_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `mc_catalog_product` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `mc_homecatalog_c` (
  `id_hc` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `id_cat` int(7) unsigned NOT NULL,
  PRIMARY KEY (`id_hc`),
  KEY `id_cat` (`id_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `mc_homecatalog_c`
  ADD CONSTRAINT `mc_homecatalog_c_ibfk_1` FOREIGN KEY (`id_cat`) REFERENCES `mc_catalog_cat` (`id_cat`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `mc_homecatalog` (
 `id_config` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
 `type_hc` enum('products', 'category') NOT NULL DEFAULT 'products',
 `limit_hc` smallint(2) unsigned NOT NULL DEFAULT 12,
 `sort_hc` smallint(1) unsigned NOT NULL DEFAULT 0,
 PRIMARY KEY (`id_config`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `mc_homecatalog` (`id_config`, `type_hc`, `limit_hc`) VALUES
(NULL, 'products', 12);

INSERT INTO `mc_admin_access` (`id_role`, `id_module`, `view`, `append`, `edit`, `del`, `action`)
  SELECT 1, m.id_module, 1, 1, 1, 1, 1 FROM mc_module as m WHERE name = 'homecatalog';
TRUNCATE TABLE `mc_homecatalog_p`;
DROP TABLE `mc_homecatalog_p`;
TRUNCATE TABLE `mc_homecatalog_c`;
DROP TABLE `mc_homecatalog_c`;
TRUNCATE TABLE `mc_homecatalog`;
DROP TABLE `mc_homecatalog`;

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'homecatalog'
);
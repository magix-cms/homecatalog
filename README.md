# Homecatalog
Plugin Homecatalog for Magix CMS 3

Ajouter un ou plusieurs produits sur la page d'accueil de votre site internet.

## Installation
 * Décompresser l'archive dans le dossier "plugins" de magix cms
 * Connectez-vous dans l'administration de votre site internet
 * Cliquer sur l'onglet plugins du menu déroulant pour sélectionner homecatalog (Produits sur la page d'accueil).
 * Une fois dans le plugin, laisser faire l'auto installation
 * Il ne reste que la configuration du plugin pour correspondre avec vos données.
 * Copier le contenu du dossier **skin/public** dans le dossier de votre skin.
 * Copier le contenu du fichier **public.js** à la fin du fichier **global.js** de votre skin

### Ajouter dans home.tpl la ligne suivante

```smarty
{block name="main:after"}
    {include file="homecatalog/brick/catalog.tpl"}
{/block}
````
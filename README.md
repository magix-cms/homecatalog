# Homecatalog
Plugin Homecatalog for Magix CMS 3

Ajouter un ou plusieurs produits sur la page d'accueil de votre site internet.

Utilise actuellement Owl Carousel pour l'affichage des produits.

## Installation
 * Décompresser l'archive dans le dossier "plugins" de magix cms
 * Connectez-vous dans l'administration de votre site internet
 * Cliquer sur l'onglet plugins du menu déroulant pour sélectionner advantage (Produits sur la page d'accueil).
 * Une fois dans le plugin, laisser faire l'auto installation
 * Il ne reste que la configuration du plugin pour correspondre avec vos données.
 * Copier le contenu du dossier skin/public dans le dossier de votre skin.

### Ajouter dans home.tpl la ligne suivante

```smarty
{block name="main:after"}
    {include file="homecatalog/brick/catalog.tpl"}
{/block}
````

### Ajouter dans layout.tpl, dans le capture vendors, la ligne suivante

<pre>
{capture name="vendors"}
    <small>/min/?f=skin/{$theme}/js/vendor/bootstrap.min.js,</small>
    <small>{if $touch}skin/{$theme}/js/vendor/jquery.detect_swipe.min.js,{/if}</small>
    <small>skin/{$theme}/js/vendor/featherlight.min.js,</small>
    <small>skin/{$theme}/js/vendor/featherlight.gallery.min.js,</small>
    <em><b>skin/{$theme}/js/vendor/owl.carousel.min.js,</b></em>
    <small>{if $viewport !== 'mobile'}skin/{$theme}/js/affixhead.min.js,{/if}</small>
    <small>skin/{$theme}/js/global.min.js,</small>
    <small>skin/{$theme}/js/vendor/lazysizes.min.js,</small>
    <small>skin/{$theme}/js/lazyload.min.js</small>
{/capture}
</pre>
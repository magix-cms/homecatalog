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
Si vous utilisez le mode ow carousel il faut ajouter dans le global.js
```javascript
if($(".owl-products").length > 0 && $.fn.owlCarousel !== undefined) {
            $(".owl-products").owlCarousel(Object.assign({},owlOptions,{
                loop: false,
                responsive:{
                    0:{
                        items:1,
                        margin: 0
                    },
                    480:{
                        items:2,
                        margin: 0
                    },
                    768:{
                        items:2,
                        margin: 30
                    },
                    992:{
                        items:3,
                        margin: 30
                    },
                    1200:{
                        items:4,
                        // slideBy:2,
                        margin: 30
                    }
                }
            }));
        }
````
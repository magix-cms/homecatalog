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

 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------

 # DISCLAIMER

 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
/**
 * Author: Salvatore Di Salvo
 * Copyright: MAGIX CMS
 * Date: 05-11-15
 * Time: 13:51
 * License: Dual licensed under the MIT or GPL Version
 */
var homecatalog = (function ($, undefined) {
    'use strict';

    function initSortable() {
        $( ".sortable" ).sortable({
            items: "> li",
            handle: "header .fa-arrows-alt",
            cursor: "move",
            placeholder: "list-group-item list-group-item-default",
            update: function(){
                var serial = $( ".sortable" ).sortable('serialize', { key: "order[]" });
                $.jmRequest({
                    handler: "ajax",
                    url: '/admin/index.php?controller=homecatalog&action=order',
                    method: 'POST',
                    data : serial,
                    success:function(e){
                        $.jmRequest.initbox(e,{
                                display: false
                            }
                        );
                    }
                });
            }
        }).disableSelection();
    }

    function initButtons() {
        $('#hc_type input[type="radio"]').on('click change input',function(){
            $('#hc_type input[type="radio"]').parent().attr('class','btn btn-default');
            $(this).parent().attr('class','btn btn-main-theme');
        });
    }

    return {
        run: function (controller) {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if($(e.target).attr('href') === '#images') {
                    initSortable();
                    initButtons();
                }
            });

            initSortable();
            initButtons();
        }
    }
})(jQuery);
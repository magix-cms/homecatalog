<li id="products_{$hc.id_hc}" class="panel list-group-item">
    <header>
    <span class="fas fa-arrows-alt"></span> {$hc.id_product} - {$hc.name_p}
    <div class="actions">
        <a href="#" class="btn btn-link action_on_record modal_action" data-id="{$hc.id_hc}" data-target="#delete_modal" data-controller="homecatalog" data-sub="products">
            <span class="fas fa-trash"></span>
        </a>
    </div>
    </header>
</li>
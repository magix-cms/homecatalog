<li id="category_{$hc.id_hc}" class="panel list-group-item">
    <header>
    <span class="fas fa-arrows-alt"></span> {$hc.id_cat} - {$hc.name_cat}
    <div class="actions">
        <a href="#" class="btn btn-link action_on_record modal_action" data-id="{$hc.id_hc}" data-target="#delete_modal" data-controller="homecatalog" data-sub="category">
            <span class="fas fa-trash"></span>
        </a>
    </div>
    </header>
</li>
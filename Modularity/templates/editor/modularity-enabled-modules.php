<div class="modularity-modules">
    <?php foreach ($modules as $moduleId => $module) : ?>
    <div class="modularity-module modularity-js-draggable"
         data-module-id="<?php echo $moduleId; ?>"
         data-sidebar-incompability='<?php echo (isset($module['sidebar_incompability']) && is_array($module['sidebar_incompability'])) ? json_encode($module['sidebar_incompability']) : ''; ?>'>
        <span class="modularity-module-icon">
            <?php echo modularity_decode_icon($module); ?>
        </span>
        <span class="modularity-module-name"><?php echo $module['labels']['name']; ?></span>
    </div>
    <?php endforeach; ?>
</div>

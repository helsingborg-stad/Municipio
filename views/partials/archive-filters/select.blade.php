<?php
    wp_dropdown_categories(array(
        'taxonomy' => $taxKey,
        'hide_empty' => false,
        'hierarchical' => true,
        'name' => 'term[]',
        'show_option_none' => sprintf(__('Select') . ' %s…', $tax->label),
        'selected' => ''
    ));
?>

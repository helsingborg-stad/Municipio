<?php
    $selected = isset($_GET[$taxKey]) && $_GET[$taxKey] !== '-1' ? $_GET[$taxKey] : null;

    wp_dropdown_categories(array(
        'taxonomy' => $taxKey,
        'hide_empty' => false,
        'hierarchical' => true,
        'name' => $taxKey,
        'show_option_none' => sprintf(__('Select') . ' %s…', $tax->label),
        'value_field' => 'slug',
        'selected' => $selected
    ));

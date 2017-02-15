<?php $tax->slug = $taxKey; $dropdown = \Municipio\Content\PostFilters::getMultiTaxDropdown($tax); ?>
<div class="pos-relative">
    <button type="button" class="btn" data-dropdown=".dropdown-{{ $taxKey }}"><?php printf(__('Select') . ' %s…', $tax->label); ?></button>
    <div class="dropdown-menu dropdown-menu-hierarchical dropdown-menu-arrow dropdown-menu-arrow-right dropdown-{{ $taxKey }}" style="right:0;">
        {!! $dropdown !!}
    </div>
</div>

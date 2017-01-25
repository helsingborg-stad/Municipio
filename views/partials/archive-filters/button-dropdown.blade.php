<div class="pos-relative">
    <button type="button" class="btn" data-dropdown=".dropdown-{{ $taxKey }}"><?php printf(__('Select') . ' %s…', $tax->label); ?></button>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-right dropdown-{{ $taxKey }}" style="right:0;">
        <ul>
            @foreach ($tax->values as $term)
            <li>
                <label class="checkbox">
                    <input type="{{ $tax->type === 'single' ? 'radio' : 'checkbox' }}" name="term[]" value="{{ $taxKey }}|{{ $term->slug }}" {{ checked(true, isset($_GET['term']) && is_array($_GET['term']) && in_array($taxKey . '|' . $term->slug, $_GET['term'])) }}> {{ $term->name }}
                </label>
            </li>
            @endforeach
        </ul>
    </div>
</div>

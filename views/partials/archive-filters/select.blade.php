<select name="term[]">
    <option value=""><?php printf(__('Select') . ' %s…', $tax->label); ?></option>

    @foreach ($tax->values as $term)
        <option value="{{ $taxKey }}|{{ $term->slug }}" {{ selected(true, isset($_GET['term']) && is_array($_GET['term']) && in_array($taxKey . '|' . $term->slug, $_GET['term'])) }}>{{ $term->name }}</option>
    @endforeach
</select>

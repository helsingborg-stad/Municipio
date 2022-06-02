<div class="o-grid-12 u-order--4">
  @button([
      'id' => '%2$s',
      'text' => '%4$s',
      'color' => 'primary',
      'style' => 'filled',
      'classList' => ['%3$s'],
      'attributeList' => [
        'value' => '%4$s',
        'name' => '%1$s',
        'onclick' => 'this.parentNode.submit();',
      ],
      'type' => 'submit',
  ])
  @endbutton
</div>
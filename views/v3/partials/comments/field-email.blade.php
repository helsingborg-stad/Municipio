<div class="o-grid-12 o-grid-6@md u-order--3">
  @field([
      'type' => 'email',
      'name' => 'email',
      'placeholder' => 'email@email.com',
      'autocomplete' => 'email',
      'attributeList' => [
          'pattern' => '^[^@]+@[^@]+\.[^@]+$',
          'data-invalid-message' => "You need to add a valid E-mail!"
      ],
      'label' => $lang->email,
      'required' => true,
  ])
  @endfield
</div>
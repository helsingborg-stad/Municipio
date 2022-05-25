<div class="o-grid-12 o-grid-6@md u-order--3">
  @field([
      'type' => 'text',
      'placeholder' => 'email@email.com',
      'attributeList' => [
          'type' => 'email',
          'name' => 'email',
          'pattern' => '^[^@]+@[^@]+\.[^@]+$',
          'autocomplete' => 'e-mail',
          'data-invalid-message' => "You need to add a valid E-mail!"
      ],
      'label' => $lang->email,
      'required' => true,
  ])
  @endfield
</div>
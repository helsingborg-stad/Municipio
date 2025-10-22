@form([
    'id' => $uid,
    'method' => 'POST',
    'action' => '#' . $uid,
    'attributeList' => [
      'data-js-ungpd-id' => $formID,
      'data-js-ungpd-list-ids' => $listIDs,
      'data-js-ungpd-double-opt-in-issue-id' => $doubleOptInIssueId,
      'data-js-ungpd-confirmation-issue-id' => $confirmationIssueId,
      'data-js-ungpd-subscription-confirmed-url' => $subscriptionConfirmedUrl,
      'data-js-ungpd-subscription-failed-url' => $subscriptionFailedUrl
		]
])
  @group([
    'alignItems' => 'end',
    'classList' => ['u-margin__bottom--1']
  ])

    @field([
      'type' => 'email',
      'placeholder' => $lang->email->placeholder,
      'name' => 'email',
      'autocomplete' => 'email',
      'invalidMessage' => $lang->email->error,
      'label' => $lang->email->label,
      'required' => true,
      'attributeList' => [
        'data-js-ungpd-email' => ''
      ],
    ])
    @endfield

    @button([
      'text' => $lang->submit->label,
      'color' => 'primary',
      'type' => 'submit',
      'icon' => 'arrow_forward'
    ])
    @endbutton

  @endgroup

  @option([
      'type' => 'checkbox',
      'attributeList' => [
        'name' => 'user_consent',
        'data-js-ungpd-consent'
      ],
      'label' => $consentMessage,
			'value' => $consentMessage,
			'required' => true
  ])
  @endoption
  
@endform

<template id="{!!$uid!!}-success">
	@notice([
		'type' => 'success',
			'message' => [
			'title' => $lang->submitted->title,
			'text' => $lang->submitted->text,
			'size' => 'sm'
		],
		'icon' => [
			'name' => 'check',
			'size' => 'md',
			'color' => 'white'
    ],
    'classList' => [
      'u-margin__top--2',
    ],
	])
	@endnotice
</template>

<template id="{!!$uid!!}-error">
	@notice([
		'type' => 'danger',
			'message' => [
			'title' => $lang->error->title,
			'text' => $lang->error->text . '<br><span class="message"></span>',
			'size' => 'sm'
		],
		'icon' => [
			'name' => 'warning',
			'size' => 'md',
			'color' => 'white'
    ],
    'classList' => [
      'u-margin__top--2',
    ],

	])
	@endnotice
</template>

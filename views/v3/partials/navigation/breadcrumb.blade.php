@if (count($breadcrumbItems) > 1)
	@breadcrumb(['list' => $breadcrumbItems])
	@endbreadcrumb
@endif
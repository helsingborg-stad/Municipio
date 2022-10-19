@php $accessabilityItems = apply_filters('accessibility_items', array()); @endphp
@if ((count($breadcrumbItems) > 1) || (!empty($accessabilityItems) && is_array($accessabilityItems)))
	<div class="o-grid o-grid--no-margin u-print-display--none">
	    <div class="o-grid-12">
			<div class="nav-helper">
			  @includeIf('partials.navigation.breadcrumb')
			  @includeIf('partials.navigation.accessibility')
			</div>
		</div>
	</div>
@endif
function getTemplateIcon(container: HTMLElement, icon: string): SVGElement | null {
	const templates = container.querySelectorAll<HTMLTemplateElement>(
		'[data-js-interactive-map-icon-template]',
	);

	for (const template of templates) {
		if (template.dataset.jsInteractiveMapIconTemplate === icon) {
			return template.content.querySelector('svg');
		}
	}

	return null;
}

function getIcon(container: HTMLElement, icon: string): SVGElement | null {
	return getTemplateIcon(container, icon) ?? getTemplateIcon(container, 'location_on');
}

export function setIcon(container: HTMLElement, element: HTMLElement | null, icon: string): void {
	const currentIcon = element?.querySelector('svg');
	const templateIcon = getIcon(container, icon);

	if (currentIcon && templateIcon) {
		currentIcon.replaceWith(templateIcon.cloneNode(true));
	}
}

export function createIcon(container: HTMLElement, icon: string): string {
	return getIcon(container, icon)?.outerHTML ?? '';
}

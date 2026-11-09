type PaletteChoice = {
    value: string;
    label: string;
    background: string;
    contrast: string;
};

declare global {
    interface Window {
        MunicipioPaletteColorChoices?: PaletteChoice[];
    }
}

const selector = '.municipio-palette-color-selector';

function enhanceField(field: Element): void {
    if (field.classList.contains('is-enhanced')) {
        return;
    }

    const select = field.querySelector<HTMLSelectElement>('select');
    const choices = window.MunicipioPaletteColorChoices;
    if (!select || !choices?.length) {
        return;
    }

    const grid = document.createElement('div');
    grid.className = 'municipio-palette-color-grid';
    grid.setAttribute('role', 'radiogroup');
    grid.setAttribute('aria-label', field.querySelector('label')?.textContent?.trim() || 'Colour palette');

    const updateSelection = (): void => {
        grid.querySelectorAll<HTMLButtonElement>('[role="radio"]').forEach((button) => {
            button.setAttribute('aria-checked', String(button.dataset.value === select.value));
        });
    };

    const visualChoices = [...choices];
    const defaultOption = Array.from(select.options).find((option) => option.value === '');
    if (defaultOption) {
        visualChoices.unshift({
            value: '',
            label: defaultOption.textContent?.trim() || 'Use default',
            background: '#f0f0f1',
            contrast: '#1d2327',
        });
    }

    visualChoices.forEach((choice) => {
        const option = document.createElement('button');
        option.type = 'button';
        option.className = 'municipio-palette-color-option';
        option.dataset.value = choice.value;
        option.setAttribute('role', 'radio');
        option.setAttribute('aria-label', choice.label);

        const preview = document.createElement('span');
        preview.className = 'municipio-palette-color-option__preview';
        preview.style.backgroundColor = choice.background;
        preview.style.color = choice.contrast;
        preview.textContent = 'Aa';

        const label = document.createElement('span');
        label.className = 'municipio-palette-color-option__label';
        label.textContent = choice.label;

        option.append(preview, label);
        option.addEventListener('click', () => {
            select.value = choice.value;
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
            updateSelection();
        });
        grid.append(option);
    });

    select.after(grid);
    select.tabIndex = -1;
    select.addEventListener('change', updateSelection);
    field.classList.add('is-enhanced');
    updateSelection();
}

function enhanceAll(root: ParentNode = document): void {
    root.querySelectorAll(selector).forEach(enhanceField);
}

function initialize(): void {
    enhanceAll();
    new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (!(node instanceof Element)) {
                    return;
                }

                if (node.matches(selector)) {
                    enhanceField(node);
                }
                enhanceAll(node);
            });
        });
    }).observe(document.body, { childList: true, subtree: true });
}

document.addEventListener('municipioPaletteColorChoicesReady', initialize, { once: true });
if (window.MunicipioPaletteColorChoices) {
    initialize();
}

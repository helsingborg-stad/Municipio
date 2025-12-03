import { findByRole, render } from '@testing-library/react';
import { userEvent } from '@testing-library/user-event';
import { PostsListContext } from '../../../PostsListContext';
import { OrderByControl } from './OrderByControl';

Object.defineProperty(window, 'matchMedia', {
	writable: true,
	value: jest.fn().mockImplementation((query) => ({
		matches: false,
		media: query,
		onchange: null,
		addListener: jest.fn(),
		removeListener: jest.fn(),
		addEventListener: jest.fn(),
		removeEventListener: jest.fn(),
		dispatchEvent: jest.fn(),
	})),
});

describe('OrderByControl', () => {
	it.each(['date', 'title', 'modified'])('allows selecting %s from options', async (targetValue) => {
		let value = '';
		const { baseElement } = render(
			<PostsListContext.Provider value={{ postTypeMetaKeys: async () => [] }}>
				<OrderByControl
					postType="post"
					orderBy={value}
					onChange={(selectedValue) => {
						value = selectedValue;
					}}
				/>
			</PostsListContext.Provider>,
		);

		await userEvent.selectOptions(baseElement.querySelector('select') as HTMLSelectElement, targetValue);
		expect(value).toBe(targetValue);
	});

	it('allows selecting meta keys belonging to post type', async () => {
		let value = '';

		const { baseElement } = render(
			<PostsListContext.Provider value={{ postTypeMetaKeys: async () => ['meta_key_example'] }}>
				<OrderByControl
					postType="custom_post"
					orderBy={value}
					onChange={(selectedValue) => {
						value = selectedValue;
					}}
				/>
			</PostsListContext.Provider>,
		);

		await findByRole(document.body, 'option', { name: /meta_key_example/ });
		await userEvent.selectOptions(baseElement.querySelector('select') as Element, 'meta_key_example');

		expect(value).toBe('meta_key_example');
	});
});

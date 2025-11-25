import React from 'react';
import {findByRole, findByText, render} from '@testing-library/react'
import {userEvent} from '@testing-library/user-event';
import { OrderByControl } from './OrderByControl';
import { PostsListContext } from '../../../PostsListContext';

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
        render(
            <PostsListContext.Provider value={{ postTypeMetaKeys: async (postType: string) => { return []; } }}> 
                <OrderByControl postType='post' orderBy={value} onChange={(selectedValue) => { value = selectedValue; }} />
        </PostsListContext.Provider>);
        await userEvent.selectOptions(document.querySelector('select')!, targetValue);
        expect(value).toBe(targetValue);
    });
    
    it('allows selecting meta keys belonging to post type', async () => {
        let value = '';
        render(
            <PostsListContext.Provider value={{ postTypeMetaKeys: async (postType: string) => { return ['meta_key_example']; } }}>
                <OrderByControl postType='custom_post' orderBy={value} onChange={(selectedValue) => { value = selectedValue; }} />
            </PostsListContext.Provider>
        );
        
        await findByRole(document.body, 'option', { name: /meta_key_example/ });
        await userEvent.selectOptions(document.querySelector('select')!, 'meta_key_example');
        
        expect(value).toBe('meta_key_example');
    });
});
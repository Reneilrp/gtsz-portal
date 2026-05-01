import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import api from './api.js';

describe('API Utility', () => {
    beforeEach(() => {
        vi.stubGlobal('localStorage', {
            getItem: vi.fn(),
        });
    });

    afterEach(() => {
        vi.unstubAllGlobals();
    });

    it('should have correct default headers configured', () => {
        expect(api.defaults.headers['Accept']).toBe('application/json');
        expect(api.defaults.headers['Content-Type']).toBe('application/json');
    });

    it('should add Authorization header when token exists in localStorage', async () => {
        localStorage.getItem.mockReturnValue('mocked-token');

        let interceptedConfig;
        await api.get('/test-endpoint', {
            adapter: async (config) => {
                interceptedConfig = config;
                return { data: {}, status: 200, statusText: 'OK', headers: {}, config };
            }
        });

        expect(localStorage.getItem).toHaveBeenCalledWith('token');
        expect(interceptedConfig.headers.get('Authorization')).toBe('Bearer mocked-token');
    });

    it('should not add Authorization header when token does not exist', async () => {
        localStorage.getItem.mockReturnValue(null);

        let interceptedConfig;
        await api.get('/test-endpoint', {
            adapter: async (config) => {
                interceptedConfig = config;
                return { data: {}, status: 200, statusText: 'OK', headers: {}, config };
            }
        });

        expect(localStorage.getItem).toHaveBeenCalledWith('token');
        expect(interceptedConfig.headers.get('Authorization')).toBeUndefined();
    });
});

import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import api from './api';
import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';

describe('API Interceptor', () => {
    let mock;

    beforeEach(() => {
        // Create a new mock adapter for the axios instance
        mock = new MockAdapter(api);
    });

    afterEach(() => {
        // Restore original methods and clean up
        mock.restore();
        vi.restoreAllMocks();
    });

    it('should add Authorization header with token if token exists in localStorage', async () => {
        // Arrange
        const token = 'fake-jwt-token';
        vi.spyOn(Storage.prototype, 'getItem').mockImplementation((key) => {
            if (key === 'token') {
                return token;
            }
            return null;
        });

        // Set up the mock to return 200 OK and to inspect the request
        mock.onGet('/test-endpoint').reply(config => {
            return [200, { data: 'success', headers: config.headers }];
        });

        // Act
        const response = await api.get('/test-endpoint');

        // Assert
        expect(localStorage.getItem).toHaveBeenCalledWith('token');
        expect(response.data.headers.Authorization).toBe(`Bearer ${token}`);
    });

    it('should not add Authorization header if no token exists in localStorage', async () => {
        // Arrange
        vi.spyOn(Storage.prototype, 'getItem').mockImplementation(() => null);

        // Set up the mock to return 200 OK and to inspect the request
        mock.onGet('/test-endpoint').reply(config => {
            return [200, { data: 'success', headers: config.headers }];
        });

        // Act
        const response = await api.get('/test-endpoint');

        // Assert
        expect(localStorage.getItem).toHaveBeenCalledWith('token');
        expect(response.data.headers.Authorization).toBeUndefined();
    });
});

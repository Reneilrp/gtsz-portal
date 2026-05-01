import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { authService } from './authService';
import api from '../utils/api';

vi.mock('../utils/api', () => {
    return {
        default: {
            post: vi.fn(),
            defaults: {
                headers: {
                    common: {}
                }
            }
        }
    };
});

describe('authService', () => {
    beforeEach(() => {
        localStorage.clear();
        vi.clearAllMocks();
        api.defaults.headers.common = {};
    });

    describe('register', () => {
        it('should successfully register a user and set localStorage and api headers', async () => {
            const mockResponse = {
                data: {
                    user: { id: 1, name: 'Test User', email: 'test@example.com' },
                    token: 'fake-jwt-token'
                }
            };
            api.post.mockResolvedValueOnce(mockResponse);

            const result = await authService.register('Test User', 'test@example.com', 'password123', 'password123');

            expect(api.post).toHaveBeenCalledWith('/register', {
                name: 'Test User',
                email: 'test@example.com',
                password: 'password123',
                password_confirmation: 'password123'
            });
            expect(localStorage.getItem('userData')).toEqual(JSON.stringify(mockResponse.data.user));
            expect(localStorage.getItem('authToken')).toEqual(mockResponse.data.token);
            expect(api.defaults.headers.common['Authorization']).toEqual(`Bearer ${mockResponse.data.token}`);
            expect(result).toEqual(mockResponse.data);
        });

        it('should handle registration where only user is returned (no token)', async () => {
            const mockResponse = {
                data: {
                    user: { id: 1, name: 'Test User', email: 'test@example.com' }
                }
            };
            api.post.mockResolvedValueOnce(mockResponse);

            const result = await authService.register('Test User', 'test@example.com', 'password123', 'password123');

            expect(localStorage.getItem('userData')).toEqual(JSON.stringify(mockResponse.data.user));
            expect(localStorage.getItem('authToken')).toBeNull();
            expect(api.defaults.headers.common['Authorization']).toBeUndefined();
            expect(result).toEqual(mockResponse.data);
        });
    });

    describe('login', () => {
        it('should successfully login a user and set localStorage and api headers', async () => {
            const mockResponse = {
                data: {
                    user: { id: 1, name: 'Test User', email: 'test@example.com' },
                    token: 'fake-jwt-token'
                }
            };
            api.post.mockResolvedValueOnce(mockResponse);

            const result = await authService.login('test@example.com', 'password123');

            expect(api.post).toHaveBeenCalledWith('/login', {
                email: 'test@example.com',
                password: 'password123'
            });
            expect(localStorage.getItem('userData')).toEqual(JSON.stringify(mockResponse.data.user));
            expect(localStorage.getItem('authToken')).toEqual(mockResponse.data.token);
            expect(api.defaults.headers.common['Authorization']).toEqual(`Bearer ${mockResponse.data.token}`);
            expect(result).toEqual(mockResponse.data);
        });
    });

    describe('logout', () => {
        it('should call logout api and clear localStorage and api headers', async () => {
            localStorage.setItem('userData', '{"id": 1}');
            localStorage.setItem('authToken', 'fake-token');
            api.defaults.headers.common['Authorization'] = 'Bearer fake-token';
            api.post.mockResolvedValueOnce({});

            await authService.logout();

            expect(api.post).toHaveBeenCalledWith('/logout');
            expect(localStorage.getItem('userData')).toBeNull();
            expect(localStorage.getItem('authToken')).toBeNull();
            expect(api.defaults.headers.common['Authorization']).toBeUndefined();
        });

        it('should clear localStorage and api headers even if logout api fails', async () => {
            localStorage.setItem('userData', '{"id": 1}');
            localStorage.setItem('authToken', 'fake-token');
            api.defaults.headers.common['Authorization'] = 'Bearer fake-token';
            api.post.mockRejectedValueOnce(new Error('Network error'));

            try {
                await authService.logout();
            } catch (error) {
                // Ignore error for this test
            }

            expect(api.post).toHaveBeenCalledWith('/logout');
            expect(localStorage.getItem('userData')).toBeNull();
            expect(localStorage.getItem('authToken')).toBeNull();
            expect(api.defaults.headers.common['Authorization']).toBeUndefined();
        });
    });

    describe('getCurrentUser', () => {
        it('should return parsed user data if present in localStorage', () => {
            const user = { id: 1, name: 'Test User' };
            localStorage.setItem('userData', JSON.stringify(user));

            const result = authService.getCurrentUser();

            expect(result).toEqual(user);
        });

        it('should return null if user data is not in localStorage', () => {
            const result = authService.getCurrentUser();
            expect(result).toBeNull();
        });
    });

    describe('isAuthenticated', () => {
        it('should return true if user data is present in localStorage', () => {
            localStorage.setItem('userData', '{"id": 1}');
            expect(authService.isAuthenticated()).toBe(true);
        });

        it('should return false if user data is not in localStorage', () => {
            expect(authService.isAuthenticated()).toBe(false);
        });
    });

    describe('forgotPassword', () => {
        it('should call forgot-password api with email', async () => {
            const mockResponse = { data: { message: 'Reset link sent' } };
            api.post.mockResolvedValueOnce(mockResponse);

            const result = await authService.forgotPassword('test@example.com');

            expect(api.post).toHaveBeenCalledWith('/forgot-password', { email: 'test@example.com' });
            expect(result).toEqual(mockResponse.data);
        });
    });

    describe('verifyCode', () => {
        it('should call verify-code api with email and code', async () => {
            const mockResponse = { data: { valid: true } };
            api.post.mockResolvedValueOnce(mockResponse);

            const result = await authService.verifyCode('test@example.com', '123456');

            expect(api.post).toHaveBeenCalledWith('/verify-code', { email: 'test@example.com', code: '123456' });
            expect(result).toEqual(mockResponse.data);
        });
    });

    describe('resetPassword', () => {
        it('should call reset-password api with email, code, and new password', async () => {
            const mockResponse = { data: { message: 'Password reset successfully' } };
            api.post.mockResolvedValueOnce(mockResponse);

            const result = await authService.resetPassword('test@example.com', '123456', 'newpass', 'newpass');

            expect(api.post).toHaveBeenCalledWith('/reset-password', {
                email: 'test@example.com',
                code: '123456',
                password: 'newpass',
                password_confirmation: 'newpass'
            });
            expect(result).toEqual(mockResponse.data);
        });
    });
});

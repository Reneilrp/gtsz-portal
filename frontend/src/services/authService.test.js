import { describe, it, expect, vi, beforeEach } from 'vitest';
import { authService } from './authService';
import api from '../utils/api';

vi.mock('../utils/api', () => ({
  default: {
    post: vi.fn(),
    defaults: {
      headers: {
        common: {}
      }
    }
  }
}));

describe('authService', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    localStorage.clear();
    api.defaults.headers.common = {};
  });

  describe('logout', () => {
    it('should clear localStorage and auth header even if api.post("/logout") fails', async () => {
      // Setup
      localStorage.setItem('userData', '{"id": 1}');
      localStorage.setItem('authToken', 'test-token');
      api.defaults.headers.common['Authorization'] = 'Bearer test-token';

      const error = new Error('Network error');
      api.post.mockRejectedValue(error);

      // Execute
      await expect(authService.logout()).rejects.toThrow('Network error');

      // Verify
      expect(api.post).toHaveBeenCalledWith('/logout');
      expect(localStorage.getItem('userData')).toBeNull();
      expect(localStorage.getItem('authToken')).toBeNull();
      expect(api.defaults.headers.common['Authorization']).toBeUndefined();
    });

    it('should clear localStorage and auth header when api.post("/logout") succeeds', async () => {
      // Setup
      localStorage.setItem('userData', '{"id": 1}');
      localStorage.setItem('authToken', 'test-token');
      api.defaults.headers.common['Authorization'] = 'Bearer test-token';

      api.post.mockResolvedValue({ data: { message: 'Logged out' } });

      // Execute
      await authService.logout();

      // Verify
      expect(api.post).toHaveBeenCalledWith('/logout');
      expect(localStorage.getItem('userData')).toBeNull();
      expect(localStorage.getItem('authToken')).toBeNull();
      expect(api.defaults.headers.common['Authorization']).toBeUndefined();
    });
  });
});

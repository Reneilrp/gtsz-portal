import api from '../utils/api';

export const announcementService = {
    async getAll() {
        const response = await api.get('/announcements');
        return response.data;
    },
    async getById(id) {
        const response = await api.get(`/announcements/${id}`);
        return response.data;
    },
    async create(data) {
        const response = await api.post('/announcements', data);
        return response.data;
    },
    async update(id, data) {
        const response = await api.put(`/announcements/${id}`, data);
        return response.data;
    },
    async delete(id) {
        const response = await api.delete(`/announcements/${id}`);
        return response.data;
    }
};

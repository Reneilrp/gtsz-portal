import api from '../utils/api';

export const sectionService = {
    async getAll() {
        const response = await api.get('/sections');
        return response.data;
    },
    async getById(id) {
        const response = await api.get(`/sections/${id}`);
        return response.data;
    },
    async create(data) {
        const response = await api.post('/sections', data);
        return response.data;
    },
    async update(id, data) {
        const response = await api.put(`/sections/${id}`, data);
        return response.data;
    },
    async delete(id) {
        const response = await api.delete(`/sections/${id}`);
        return response.data;
    }
};

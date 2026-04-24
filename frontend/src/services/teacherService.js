import api from '../utils/api';

export const teacherService = {
    async getAll() {
        const response = await api.get('/teachers');
        return response.data;
    },
    async getById(id) {
        const response = await api.get(`/teachers/${id}`);
        return response.data;
    },
    async create(data) {
        const response = await api.post('/teachers', data);
        return response.data;
    },
    async update(id, data) {
        const response = await api.put(`/teachers/${id}`, data);
        return response.data;
    },
    async delete(id) {
        const response = await api.delete(`/teachers/${id}`);
        return response.data;
    }
};

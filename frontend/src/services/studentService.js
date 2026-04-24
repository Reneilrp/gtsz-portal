import api from '../utils/api';

export const studentService = {
    async getAll() {
        const response = await api.get('/students');
        return response.data;
    },
    async getById(id) {
        const response = await api.get(`/students/${id}`);
        return response.data;
    },
    async create(data) {
        const response = await api.post('/students', data);
        return response.data;
    },
    async update(id, data) {
        const response = await api.put(`/students/${id}`, data);
        return response.data;
    },
    async delete(id) {
        const response = await api.delete(`/students/${id}`);
        return response.data;
    }
};

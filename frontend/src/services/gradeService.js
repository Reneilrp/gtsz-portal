import api from '../utils/api';

export const gradeService = {
    async getAll() {
        const response = await api.get('/grades');
        return response.data;
    },
    async getById(id) {
        const response = await api.get(`/grades/${id}`);
        return response.data;
    },
    async create(data) {
        const response = await api.post('/grades', data);
        return response.data;
    },
    async update(id, data) {
        const response = await api.put(`/grades/${id}`, data);
        return response.data;
    },
    async delete(id) {
        const response = await api.delete(`/grades/${id}`);
        return response.data;
    }
};

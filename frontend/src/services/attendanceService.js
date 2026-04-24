import api from '../utils/api';

export const attendanceService = {
    async getAll() {
        const response = await api.get('/attendances');
        return response.data;
    },
    async getById(id) {
        const response = await api.get(`/attendances/${id}`);
        return response.data;
    },
    async create(data) {
        const response = await api.post('/attendances', data);
        return response.data;
    },
    async update(id, data) {
        const response = await api.put(`/attendances/${id}`, data);
        return response.data;
    },
    async delete(id) {
        const response = await api.delete(`/attendances/${id}`);
        return response.data;
    }
};

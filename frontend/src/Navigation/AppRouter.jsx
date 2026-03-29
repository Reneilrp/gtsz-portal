import { Routes, Route, Navigate } from 'react-router-dom';
import ProtectedRoute from '../components/ProtectedRoute';
import AuthScreen from '../screens/Auth/WebAuth';
import SuperAdminDashboard from '../screens/SuperAdmin/DashboardOverview';
import SuperAdminUserManagement from '../screens/SuperAdmin/UserManagement';

export default function AppRouter() {
    return (
        <Routes>
            <Route path="/login" element={<AuthScreen />} />
            <Route path="/register" element={<AuthScreen isRegister={true} />} />

            {/* Super Admin Routes */}
            <Route element={<ProtectedRoute allowedRoles={['Super Admin']} />}>
                <Route path="/admin/dashboard" element={<SuperAdminDashboard />} />
                <Route path="/admin/users" element={<SuperAdminUserManagement />} />
            </Route>

            {/* Student Only Routes */}
            <Route element={<ProtectedRoute allowedRoles={['Student']} />}>
                <Route path="/student/dashboard" element={<StudentDashboard />} />
            </Route>

            {/* Fallback */}
            <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
    );
}
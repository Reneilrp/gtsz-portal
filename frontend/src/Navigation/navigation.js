import {
    LayoutDashboard, UserCog, Users, BookOpen,
    GraduationCap, ClipboardCheck, MessageSquare, Settings
} from 'lucide-react';

export const navLinks = {
    'Super Admin': [
        { name: 'Dashboard', path: '/admin/dashboard', icon: LayoutDashboard },
        { name: 'User Management', path: '/admin/users', icon: UserCog },
        { name: 'System Health', path: '/admin/health', icon: Settings },
    ],
    'Admin': [
        { name: 'Dashboard', path: '/admin/dashboard', icon: LayoutDashboard },
        { name: 'Teachers', path: '/admin/teachers', icon: Users },
        { name: 'Students', path: '/admin/students', icon: GraduationCap },
        { name: 'Classes', path: '/admin/sections', icon: BookOpen },
    ],
    'Teacher': [
        { name: 'My Dashboard', path: '/teacher/dashboard', icon: LayoutDashboard },
        { name: 'My Classes', path: '/teacher/classes', icon: BookOpen },
        { name: 'Gradebook', path: '/teacher/grades', icon: ClipboardCheck },
    ],
    'Student': [
        { name: 'Dashboard', path: '/student/dashboard', icon: LayoutDashboard },
        { name: 'My Grades', path: '/student/grades', icon: GraduationCap },
        { name: 'Assignments', path: '/student/assignments', icon: BookOpen },
    ],
};
import { useState, useEffect } from 'react';
import { 
    Users, ShieldCheck, GraduationCap, Briefcase, 
    TrendingUp, AlertCircle, Clock, Search 
} from 'lucide-react';
import api from '../../utils/api';

export default function DashboardOverview() {
    const [stats, setStats] = useState({
        totalUsers: 0,
        admins: 0,
        teachers: 0,
        students: 0
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        // Fetch stats from backend
        const fetchStats = async () => {
            try {
                const res = await api.get('/admin/stats');
                setStats(res.data);
            } catch (err) {
                console.error(err);
            } finally {
                setLoading(false);
            }
        };
        fetchStats();
    }, []);

    const statCards = [
        { title: 'Total Users', value: stats.totalUsers, icon: Users, color: 'text-blue-600', bg: 'bg-blue-50' },
        { title: 'Admins', value: stats.admins, icon: ShieldCheck, color: 'text-purple-600', bg: 'bg-purple-50' },
        { title: 'Teachers', value: stats.teachers, icon: Briefcase, color: 'text-emerald-600', bg: 'bg-emerald-50' },
        { title: 'Students', value: stats.students, icon: GraduationCap, color: 'text-amber-600', bg: 'bg-amber-50' },
    ];

    return (
        <div className="space-y-8">
            {/* Header Section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">Super Admin Dashboard</h1>
                    <p className="text-slate-500">System overview and management at a glance.</p>
                </div>
                <div className="flex items-center gap-3">
                    <span className="flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                        <TrendingUp size={14} /> System Online
                    </span>
                    <span className="text-slate-400 text-sm flex items-center gap-1">
                        <Clock size={14} /> Last Update: Just now
                    </span>
                </div>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {statCards.map((card, idx) => {
                    const Icon = card.icon;
                    return (
                        <div key={idx} className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
                            <div className="flex items-center justify-between mb-4">
                                <div className={`p-3 rounded-xl ${card.bg}`}>
                                    <Icon className={card.color} size={24} />
                                </div>
                                <span className="text-emerald-500 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-lg">+0%</span>
                            </div>
                            <h3 className="text-slate-500 text-sm font-medium">{card.title}</h3>
                            <p className="text-2xl font-bold text-slate-900 mt-1">
                                {loading ? '...' : card.value}
                            </p>
                        </div>
                    );
                })}
            </div>

            {/* Main Content Area */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Recent Activity */}
                <div className="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div className="p-6 border-b border-slate-50 flex items-center justify-between">
                        <h2 className="font-bold text-slate-900">System Activity</h2>
                        <button className="text-blue-600 text-sm font-bold hover:underline">View All</button>
                    </div>
                    <div className="p-6">
                        <div className="space-y-6">
                            {[1, 2, 3].map((_, i) => (
                                <div key={i} className="flex items-start gap-4">
                                    <div className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <Clock size={18} className="text-slate-400" />
                                    </div>
                                    <div className="flex-1">
                                        <p className="text-sm text-slate-900 font-medium">System Update</p>
                                        <p className="text-xs text-slate-500">School year 2025-2026 successfully initialized.</p>
                                    </div>
                                    <span className="text-[10px] text-slate-400 font-medium italic">2h ago</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Quick Actions / Alerts */}
                <div className="space-y-6">
                    <div className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <h2 className="font-bold text-slate-900 mb-4">Quick Links</h2>
                        <div className="grid grid-cols-1 gap-3">
                            <button className="w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors flex items-center gap-3 group">
                                <ShieldCheck size={18} className="text-blue-600" />
                                <span className="text-sm font-medium text-slate-700">Create New Admin</span>
                            </button>
                            <button className="w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors flex items-center gap-3 group">
                                <AlertCircle size={18} className="text-amber-600" />
                                <span className="text-sm font-medium text-slate-700">Audit Logs</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

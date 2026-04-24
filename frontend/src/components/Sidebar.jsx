import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';
import { navLinks } from '../Navigation/navigation';
import { LogOut } from 'lucide-react';

export default function Sidebar() {
    const { user, logout } = useAuth();
    const location = useLocation();

    // Get links based on the user's role name from your DB
    const links = navLinks[user?.role?.name] || [];

    return (
        <div className="w-64 bg-slate-900 text-white min-h-screen p-4 flex flex-col">
            <div className="mb-8 px-2">
                <h1 className="text-xl font-bold text-blue-400">GTSZ Portal</h1>
                <p className="text-xs text-slate-400">{user?.role?.name}</p>
            </div>

            <nav className="flex-1 space-y-2">
                {links.map((link) => {
                    const Icon = link.icon;
                    const isActive = location.pathname === link.path;
                    return (
                        <Link
                            key={link.path}
                            to={link.path}
                            className={`flex items-center space-x-3 p-3 rounded-lg transition ${isActive ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800'
                                }`}
                        >
                            <Icon size={20} />
                            <span>{link.name}</span>
                        </Link>
                    );
                })}
            </nav>

            <button
                onClick={logout}
                className="flex items-center space-x-3 p-3 text-red-400 hover:bg-red-900/20 rounded-lg mt-auto"
            >
                <LogOut size={20} />
                <span>Logout</span>
            </button>
        </div>
    );
}
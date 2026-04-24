import { useState } from 'react';
import { useAuth } from '../hooks/useAuth';
import { useNavigate } from 'react-router-dom';

export default function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const { login } = useAuth();
    const navigate = useNavigate();

    const handleLogin = async (e) => {
        e.preventDefault();
        try {
            await login({ email, password });
            // Redirect based on role? (Assuming your AuthContext sets the user)
            navigate('/'); // Navigate to home or dashboard
        } catch (error) {
            console.error('Login failed', error);
            alert('Login failed');
        }
    };

    return (
        <div className="flex items-center justify-center min-h-screen bg-gray-100">
            <form onSubmit={handleLogin} className="p-10 space-y-4 bg-white shadow-md rounded-lg w-96">
                <h1 className="text-2xl font-bold text-center">Login</h1>
                <input
                    type="email"
                    placeholder="Email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="border p-2 block w-full rounded"
                />
                <input
                    type="password"
                    placeholder="Password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="border p-2 block w-full rounded"
                />
                <button type="submit" className="bg-blue-600 text-white p-2 w-full rounded hover:bg-blue-700">
                    Log In
                </button>
            </form>
        </div>
    );
}
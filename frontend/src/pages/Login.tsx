import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ApiError } from '../api';
import { useAuth } from '../auth';

export default function Login() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState('staff');
  const [showPass, setShowPass] = useState(false);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setBusy(true);
    try {
      await login(username, password, role);
      navigate('/pos');
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Login failed. Try again.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div className="text-center mb-6">
          <img src="/logo.png" alt="Logo" className="h-16 w-auto mx-auto mb-4" />
          <h2 className="text-2xl font-extrabold text-slate-900">GLORYSTOCK</h2>
          <p className="text-slate-500 text-xs font-bold uppercase tracking-wide mt-1">
            Sign in to your account
          </p>
          <div className="mx-auto mt-2 h-1 w-10 rounded bg-blue-500" />
        </div>

        {error && (
          <div className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-center text-sm text-red-700">
            {error}
          </div>
        )}

        <form onSubmit={submit}>
          <div className="mb-4">
            <label htmlFor="username" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">
              Username
            </label>
            <input
              id="username"
              type="text"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
              placeholder="Enter username"
              required
            />
          </div>

          <div className="mb-4">
            <label htmlFor="password" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">
              Password
            </label>
            <div className="relative flex items-center border border-slate-300 rounded-lg focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-200">
              <input
                id="password"
                type={showPass ? 'text' : 'password'}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full rounded-lg px-3 py-2 focus:outline-none"
                placeholder="••••••••"
                required
              />
              <button type="button" onClick={() => setShowPass((v) => !v)} className="px-3 text-slate-400 hover:text-slate-600">
                {showPass ? '🙈' : '👁'}
              </button>
            </div>
          </div>

          <div className="mb-5">
            <label htmlFor="role" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">
              Login As
            </label>
            <select
              id="role"
              value={role}
              onChange={(e) => setRole(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
            >
              <option value="staff">Staff / Cashier</option>
              <option value="admin">Administrator</option>
            </select>
          </div>

          <button
            type="submit"
            disabled={busy}
            className="w-full mb-4 rounded-lg bg-slate-900 py-2.5 font-bold uppercase text-white tracking-wide hover:bg-slate-800 transition disabled:opacity-50"
          >
            {busy ? 'Signing in…' : 'Sign In'}
          </button>
        </form>

        <div className="text-center border-t border-slate-200 pt-4">
          <p className="text-slate-500 text-sm">
            Don&apos;t have an account?{' '}
            <Link to="/register" className="font-bold text-blue-600 hover:underline">
              Register Here
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}

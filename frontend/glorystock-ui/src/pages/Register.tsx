import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ApiError } from '../api';
import { useAuth } from '../auth';

export default function Register() {
  const { register } = useAuth();
  const navigate = useNavigate();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState('staff');
  const [adminKey, setAdminKey] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setBusy(true);
    try {
      await register(username, password, role, role === 'admin' ? adminKey : undefined);
      navigate('/pos');
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Registration failed. Try again.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
      <div className="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div className="text-center mb-6">
          <h2 className="text-2xl font-extrabold text-slate-900">Create Account</h2>
          <p className="text-slate-500 text-sm">Join the GloryStock team</p>
        </div>

        {error && (
          <div className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
            {error}
          </div>
        )}

        <form onSubmit={submit}>
          <div className="mb-4">
            <label htmlFor="username" className="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
              Choose Username
            </label>
            <input
              id="username"
              type="text"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
              required
            />
          </div>

          <div className="mb-4">
            <label htmlFor="password" className="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
              Set Password
            </label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
              required
            />
          </div>

          <div className="mb-4">
            <label htmlFor="role" className="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1">
              Your Role
            </label>
            <select
              id="role"
              value={role}
              onChange={(e) => setRole(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
            >
              <option value="staff">Staff (Sales Only)</option>
              <option value="admin">Administrator (Full Access)</option>
            </select>
          </div>

          {role === 'admin' && (
            <div className="mb-5">
              <label htmlFor="admin_key" className="block text-xs font-bold text-red-600 uppercase tracking-wide mb-1">
                Admin Secret Key
              </label>
              <input
                id="admin_key"
                type="password"
                value={adminKey}
                onChange={(e) => setAdminKey(e.target.value)}
                className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                placeholder="Enter security code"
              />
            </div>
          )}

          <button
            type="submit"
            disabled={busy}
            className="w-full mb-3 rounded-lg bg-slate-900 py-2.5 font-bold text-white hover:bg-slate-800 transition disabled:opacity-50"
          >
            {busy ? 'Creating…' : 'Register Account'}
          </button>

          <p className="text-center text-sm text-slate-500">
            Already have an account?{' '}
            <Link to="/login" className="font-bold text-slate-900 hover:underline">
              Login
            </Link>
          </p>
        </form>
      </div>
    </div>
  );
}

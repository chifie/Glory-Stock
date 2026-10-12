import { useCallback, useEffect, useState, type FormEvent } from 'react';
import { api, ApiError } from '../api';
import { useAuth } from '../auth';
import type { UserRow } from '../types';

export default function Users() {
  const { user: me } = useAuth();
  const [users, setUsers] = useState<UserRow[] | null>(null);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState('staff');
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  const load = useCallback(() => {
    api.get<{ users: UserRow[] }>('/users').then((d) => setUsers(d.users)).catch(() => {});
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      const res = await api.post<{ message: string }>('/users', {
        username,
        password,
        role,
      });
      setNotice(res.message);
      setUsername('');
      setPassword('');
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Create failed.');
    }
  };

  const destroy = async (u: UserRow) => {
    if (!window.confirm('Revoke all access for this staff member?')) return;
    setError('');
    try {
      const res = await api.delete<{ message: string }>(`/users/${u.id}`);
      setNotice(res.message);
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Delete failed.');
    }
  };

  if (!users) return <p className="text-slate-500">Loading…</p>;

  return (
    <div>
      {notice && (
        <div className="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
          {notice}
        </div>
      )}
      {error && (
        <div className="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
          {error}
        </div>
      )}

      <div className="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
        <div>
          <h3 className="text-xl font-extrabold">Staff Management</h3>
          <p className="text-slate-500 text-sm">Control system access and authorized personnel</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div className="lg:col-span-4">
          <div className="rounded-xl bg-white shadow-md p-5">
            <h5 className="font-bold mb-5">Register New Staff</h5>

            <form onSubmit={submit}>
              <div className="mb-4">
                <label htmlFor="u" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Username</label>
                <input
                  id="u"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                  placeholder="e.g. bakari_m"
                  required
                />
              </div>
              <div className="mb-4">
                <label htmlFor="p" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Password</label>
                <input
                  id="p"
                  type="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                  placeholder="••••••••"
                  required
                />
              </div>
              <div className="mb-5">
                <label htmlFor="r" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Access Level</label>
                <select
                  id="r"
                  value={role}
                  onChange={(e) => setRole(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                >
                  <option value="staff">Staff (Sales Only)</option>
                  <option value="admin">Administrator (Full Access)</option>
                </select>
              </div>
              <button type="submit" className="w-full rounded-lg bg-slate-900 py-3 shadow-sm font-bold text-white hover:bg-slate-800 transition">
                ➕ Create Account
              </button>
            </form>
          </div>
        </div>

        <div className="lg:col-span-8">
          <div className="rounded-xl bg-white shadow-md overflow-hidden h-full">
            <div className="flex justify-between items-center border-b border-slate-200 px-5 py-3">
              <h5 className="font-bold">Authorized Personnel</h5>
              <span className="rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold">
                {users.length} Users
              </span>
            </div>
            <table className="w-full text-left">
              <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="pl-5 py-3">ID</th>
                  <th className="py-3">Username</th>
                  <th className="py-3">Role</th>
                  <th className="pr-5 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {users.map((u) => (
                  <tr key={u.id} className="hover:bg-slate-50">
                    <td className="pl-5 py-3 text-slate-500 text-sm">#{u.id}</td>
                    <td className="py-3 font-bold text-slate-900">
                      <div className="flex items-center gap-2">
                        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400">👤</div>
                        {u.username}
                      </div>
                    </td>
                    <td className="py-3">
                      {u.role === 'admin' ? (
                        <span className="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Admin</span>
                      ) : (
                        <span className="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs">Staff</span>
                      )}
                    </td>
                    <td className="pr-5 py-3 text-right">
                      {u.id === me?.id ? (
                        <span className="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">✅ You (Active)</span>
                      ) : (
                        <button
                          onClick={() => destroy(u)}
                          className="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition"
                        >
                          🚫 Revoke Access
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}

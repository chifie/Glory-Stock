import { useState, type FormEvent } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { ArrowLeft, Eye, EyeOff, LogIn } from 'lucide-react';
import { ApiError } from '../api';
import { useAuth } from '../auth';
import { homeForRole } from '../roles';
import { Alert, Button, Field, inputCls } from '../components/ui';

export default function Login() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const from = (location.state as { from?: { pathname?: string } } | null)?.from?.pathname;
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
      navigate(from ?? homeForRole(role), { replace: true });
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Login failed. Try again.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="flex min-h-screen flex-col items-center justify-center bg-surface px-4 py-10">
      <div className="w-full max-w-md">
        <Link
          to="/"
          className="mb-6 inline-flex items-center gap-1.5 text-sm font-semibold text-ink-500 transition hover:text-brand-700"
        >
          <ArrowLeft className="h-4 w-4" aria-hidden />
          Back to home
        </Link>

        <div className="rounded-card border border-slate-200 bg-white p-8 shadow-card">
          <div className="mb-7 text-center">
            <img src="/logo.png" alt="" className="mx-auto h-14 w-auto" />
            <h1 className="mt-4 text-page font-extrabold tracking-tight text-ink-950">
              Welcome back
            </h1>
            <p className="mt-1 text-sm text-ink-500">Sign in to your GloryStock account</p>
            <div className="mx-auto mt-3 h-1 w-12 rounded-pill bg-brand-600" />
          </div>

          {error && <Alert tone="error">{error}</Alert>}

          <form onSubmit={submit} className="space-y-4">
            <Field label="Username" htmlFor="username">
              <input
                id="username"
                type="text"
                autoComplete="username"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                className={inputCls}
                placeholder="e.g. john"
                required
              />
            </Field>

            <Field label="Password" htmlFor="password">
              <div className="relative">
                <input
                  id="password"
                  type={showPass ? 'text' : 'password'}
                  autoComplete="current-password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className={`${inputCls} pr-11`}
                  placeholder="••••••••"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPass((v) => !v)}
                  aria-label={showPass ? 'Hide password' : 'Show password'}
                  className="absolute right-1 top-1/2 -translate-y-1/2 rounded-lg p-2 text-ink-400 transition hover:text-ink-800"
                >
                  {showPass ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </Field>

            <Field label="Sign in as" htmlFor="role">
              <select
                id="role"
                value={role}
                onChange={(e) => setRole(e.target.value)}
                className={inputCls}
              >
                <option value="staff">Staff / Cashier</option>
                <option value="admin">Administrator</option>
              </select>
            </Field>

            <Button type="submit" className="w-full" loading={busy} icon={LogIn}>
              {busy ? 'Signing in…' : 'Sign in'}
            </Button>
          </form>

          <p className="mt-6 border-t border-slate-200 pt-4 text-center text-sm text-ink-500">
            Don&apos;t have an account?{' '}
            <Link to="/register" className="font-bold text-brand-700 hover:underline">
              Register here
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}

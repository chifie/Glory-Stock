import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ArrowLeft, UserPlus } from 'lucide-react';
import { ApiError } from '../api';
import { useAuth } from '../auth';
import { homeForRole } from '../roles';
import { Alert, Button, Field, inputCls } from '../components/ui';

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
      navigate(homeForRole(role));
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Registration failed. Try again.');
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
              Create your account
            </h1>
            <p className="mt-1 text-sm text-ink-500">Join the GloryStock team</p>
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
                placeholder="Choose a username"
                required
              />
            </Field>

            <Field
              label="Password"
              htmlFor="password"
              hint="At least 6 characters."
            >
              <input
                id="password"
                type="password"
                autoComplete="new-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className={inputCls}
                placeholder="Set a password"
                minLength={6}
                required
              />
            </Field>

            <Field label="Role" htmlFor="role">
              <select
                id="role"
                value={role}
                onChange={(e) => setRole(e.target.value)}
                className={inputCls}
              >
                <option value="staff">Staff (sales &amp; stock view)</option>
                <option value="admin">Administrator (full access)</option>
              </select>
            </Field>

            {role === 'admin' && (
              <Field
                label="Admin secret key"
                htmlFor="admin_key"
                hint="Required to create an administrator account."
              >
                <input
                  id="admin_key"
                  type="password"
                  autoComplete="off"
                  value={adminKey}
                  onChange={(e) => setAdminKey(e.target.value)}
                  className={inputCls}
                  placeholder="Enter the admin security key"
                  required
                />
              </Field>
            )}

            <Button type="submit" className="w-full" loading={busy} icon={UserPlus}>
              {busy ? 'Creating account…' : 'Create account'}
            </Button>
          </form>

          <p className="mt-6 border-t border-slate-200 pt-4 text-center text-sm text-ink-500">
            Already have an account?{' '}
            <Link to="/login" className="font-bold text-brand-700 hover:underline">
              Sign in
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}

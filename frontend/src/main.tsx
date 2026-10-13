import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Link, Navigate, Outlet, Route, Routes, useLocation } from 'react-router-dom';
import { ShieldAlert } from 'lucide-react';
import { AuthProvider, useAuth } from './auth';
import { homeForRole } from './roles';
import Layout from './components/Layout';
import Categories from './pages/Categories';
import DailyClose from './pages/DailyClose';
import Dashboard from './pages/Dashboard';
import Expenses from './pages/Expenses';
import Landing from './pages/Landing';
import Login from './pages/Login';
import Pos from './pages/Pos';
import Receipt from './pages/Receipt';
import Register from './pages/Register';
import Reorder from './pages/Reorder';
import Reports from './pages/Reports';
import SalesHistory from './pages/SalesHistory';
import StockLog from './pages/StockLog';
import Users from './pages/Users';
import './index.css';

function RequireAuth() {
  const { user } = useAuth();
  const location = useLocation();

  if (!user) return <Navigate to="/login" state={{ from: location }} replace />;

  return <Outlet />;
}

function RequireAdmin({ children }: { children: React.ReactNode }) {
  const { user } = useAuth();

  if (!user) return <Navigate to="/login" replace />;
  if (user.role !== 'admin') return <Navigate to="/access-denied" replace />;

  return <>{children}</>;
}

function AppRoutes() {
  const { user } = useAuth();

  return (
    <Routes>
      {/* Public marketing landing page */}
      <Route
        path="/"
        element={user ? <Navigate to={homeForRole(user.role)} replace /> : <Landing />}
      />

      {/* Public */}
      <Route path="/login" element={user ? <Navigate to={homeForRole(user.role)} replace /> : <Login />} />
      <Route
        path="/register"
        element={user ? <Navigate to={homeForRole(user.role)} replace /> : <Register />}
      />

      {/* Authenticated pages share the navbar layout */}
      <Route element={<RequireAuth />}>
        <Route element={<Layout />}>
          <Route path="/pos" element={<Pos />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/sales-history" element={<SalesHistory />} />
          <Route path="/stock-log" element={<StockLog />} />
          <Route path="/receipt/:id" element={<Receipt />} />
          <Route path="/daily-close" element={<DailyClose />} />
          <Route path="/reorder" element={<Reorder />} />

          {/* Admin-only */}
          <Route
            path="/reports"
            element={
              <RequireAdmin>
                <Reports />
              </RequireAdmin>
            }
          />
          <Route
            path="/expenses"
            element={
              <RequireAdmin>
                <Expenses />
              </RequireAdmin>
            }
          />
          <Route
            path="/categories"
            element={
              <RequireAdmin>
                <Categories />
              </RequireAdmin>
            }
          />
          <Route
            path="/users"
            element={
              <RequireAdmin>
                <Users />
              </RequireAdmin>
            }
          />
        </Route>
      </Route>

      <Route path="/access-denied" element={<AccessDenied />} />
      <Route
        path="*"
        element={<Navigate to={user ? homeForRole(user.role) : '/'} replace />}
      />
    </Routes>
  );
}

function AccessDenied() {
  const { user } = useAuth();
  return (
    <div className="flex min-h-screen items-center justify-center bg-surface p-4">
      <div className="w-full max-w-lg rounded-card border border-slate-200 bg-white p-8 text-center shadow-card">
        <span className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-600">
          <ShieldAlert className="h-7 w-7" aria-hidden />
        </span>
        <h1 className="mt-4 text-page font-extrabold tracking-tight text-ink-950">Access denied</h1>
        <p className="mt-2 text-sm text-ink-500">
          {user ? (
            <>
              Signed in as <strong className="text-ink-800">{user.username}</strong> ({user.role}).
              This area requires an admin account.
            </>
          ) : (
            'This area requires an admin account.'
          )}
        </p>
        <Link
          to={user ? homeForRole(user.role) : '/'}
          className="mt-6 inline-block rounded-control bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700"
        >
          {user ? 'Back to my dashboard' : 'Go to homepage'}
        </Link>
      </div>
    </div>
  );
}

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <BrowserRouter>
      <AuthProvider>
        <AppRoutes />
      </AuthProvider>
    </BrowserRouter>
  </StrictMode>
);

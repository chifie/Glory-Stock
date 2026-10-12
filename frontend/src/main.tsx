import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Navigate, Outlet, Route, Routes, useLocation } from 'react-router-dom';
import { AuthProvider, useAuth } from './auth';
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
      <Route path="/" element={user ? <Navigate to="/pos" replace /> : <Landing />} />

      {/* Public */}
      <Route path="/login" element={user ? <Navigate to="/pos" replace /> : <Login />} />
      <Route path="/register" element={user ? <Navigate to="/pos" replace /> : <Register />} />

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
      <Route path="*" element={<Navigate to={user ? '/pos' : '/'} replace />} />
    </Routes>
  );
}

function AccessDenied() {
  const { user } = useAuth();
  return (
    <div className="min-h-screen bg-slate-100 flex items-center justify-center p-4">
      <div className="w-full max-w-lg bg-white rounded-2xl p-8 text-center shadow-md">
        <h1 className="text-6xl mb-3">🚫</h1>
        <h3 className="text-xl font-bold mb-2">Access Denied</h3>
        <p className="text-slate-500 mb-6">Hello {user?.username}, you don&apos;t have Admin rights.</p>
        <a href="/pos" className="inline-block rounded-lg bg-slate-900 px-5 py-2 font-semibold text-white hover:bg-slate-800 transition">
          Back to POS
        </a>
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

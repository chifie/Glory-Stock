import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth';

const linkCls = ({ isActive }: { isActive: boolean }) =>
  `px-3 py-2 rounded-lg text-sm font-medium transition ${
    isActive
      ? 'bg-slate-800 text-white'
      : 'text-slate-300 hover:text-white hover:bg-slate-800'
  }`;

export default function Layout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  if (!user) return null;

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="min-h-screen bg-slate-100 text-slate-900">
      <nav className="bg-slate-900 shadow-md mb-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16 overflow-x-auto">
            <NavLink to="/pos" className="flex items-center gap-3 font-extrabold tracking-wide text-white">
              <img src="/logo.png" alt="GloryStock" className="h-9 w-auto object-contain" />
              <span className="tracking-wider">GLORYSTOCK</span>
            </NavLink>

            <div className="flex items-center gap-1">
              <NavLink to="/pos" className={linkCls}>Point of Sale</NavLink>
              <NavLink to="/dashboard" className={linkCls}>Inventory</NavLink>
              <NavLink to="/sales-history" className={linkCls}>Sales History</NavLink>

              {user.role === 'admin' && (
                <>
                  <NavLink to="/reports" className={linkCls}>Reports</NavLink>
                  <NavLink to="/expenses" className={linkCls}>Expenses</NavLink>
                  <NavLink to="/users" className={`${linkCls({ isActive: false })} font-semibold text-amber-400`}>
                    Manage Staff
                  </NavLink>
                </>
              )}

              <button
                onClick={handleLogout}
                className="ml-2 px-3 py-2 rounded-lg text-sm font-bold text-red-400 hover:text-red-300 hover:bg-slate-800 transition"
              >
                ← Logout ({user.username})
              </button>
            </div>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4 py-6">
        <Outlet />
      </main>
    </div>
  );
}

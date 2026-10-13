import { useEffect, useState } from 'react';
import { NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import {
  BarChart3,
  CalendarCheck,
  ClipboardList,
  FolderTree,
  History,
  LayoutDashboard,
  LogOut,
  Menu,
  Package,
  Receipt as ReceiptIcon,
  ScrollText,
  ShoppingCart,
  Users as UsersIcon,
  X,
  type LucideIcon,
} from 'lucide-react';
import { useAuth } from '../auth';

interface NavItem {
  to: string;
  label: string;
  icon: LucideIcon;
  admin?: boolean;
  exact?: boolean;
}

const navSections: { heading: string; items: NavItem[] }[] = [
  {
    heading: 'Operate',
    items: [
      { to: '/pos', label: 'Point of Sale', icon: ShoppingCart, exact: true },
      { to: '/sales-history', label: 'Sales History', icon: History },
      { to: '/daily-close', label: 'Daily Close', icon: CalendarCheck },
    ],
  },
  {
    heading: 'Inventory',
    items: [
      { to: '/dashboard', label: 'Inventory', icon: LayoutDashboard, exact: true },
      { to: '/stock-log', label: 'Stock Audit Trail', icon: ScrollText },
      { to: '/reorder', label: 'Reorder Sheet', icon: ClipboardList },
    ],
  },
  {
    heading: 'Manage',
    items: [
      { to: '/reports', label: 'Reports', icon: BarChart3, admin: true },
      { to: '/expenses', label: 'Expenses', icon: ReceiptIcon, admin: true },
      { to: '/categories', label: 'Categories', icon: FolderTree, admin: true },
      { to: '/users', label: 'Staff & Users', icon: UsersIcon, admin: true },
    ],
  },
];

/** Page title + description shown in the top bar, keyed by route path. */
const pageMeta: Record<string, { title: string; description: string; icon: LucideIcon }> = {
  '/pos': {
    title: 'Point of Sale',
    description: 'Ring up a sale — pick a product and quantity, stock updates instantly.',
    icon: ShoppingCart,
  },
  '/sales-history': {
    title: 'Sales History',
    description: 'Browse past transactions and daily takings.',
    icon: History,
  },
  '/daily-close': {
    title: 'Daily Close',
    description: 'End-of-day settlement summary for the counter.',
    icon: CalendarCheck,
  },
  '/dashboard': {
    title: 'Inventory',
    description: 'Stock levels, valuation, and product management.',
    icon: LayoutDashboard,
  },
  '/stock-log': {
    title: 'Stock Audit Trail',
    description: 'Every restock, sale, correction, and void — who, what, when.',
    icon: ScrollText,
  },
  '/reorder': {
    title: 'Reorder Sheet',
    description: 'Low-stock items flagged for your supplier.',
    icon: ClipboardList,
  },
  '/reports': {
    title: 'Reports',
    description: 'Revenue, expenses, profit trends, and best sellers.',
    icon: BarChart3,
  },
  '/expenses': {
    title: 'Expenses',
    description: 'Record and review business expenses.',
    icon: ReceiptIcon,
  },
  '/categories': {
    title: 'Categories',
    description: 'Organize your catalog into categories.',
    icon: FolderTree,
  },
  '/users': {
    title: 'Staff & Users',
    description: 'Create accounts and manage who can sign in.',
    icon: UsersIcon,
  },
};

function metaFor(pathname: string) {
  if (pageMeta[pathname]) return { ...pageMeta[pathname], crumb: null as string | null };
  if (pathname.startsWith('/receipt/')) {
    return { ...pageMeta['/sales-history'], title: 'Receipt', crumb: 'Sales History' };
  }
  return { title: 'GloryStock', description: '', icon: Package, crumb: null };
}

function roleBadge(role: string) {
  return role === 'admin'
    ? 'bg-amber-400/15 text-amber-300 ring-amber-400/30'
    : 'bg-sky-400/15 text-sky-300 ring-sky-400/30';
}

export default function Layout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [drawerOpen, setDrawerOpen] = useState(false);

  const meta = metaFor(location.pathname);
  const pageTitle = meta.crumb ? `${meta.title}` : meta.title;

  // Keep the tab title in sync with the current page.
  useEffect(() => {
    document.title = `${pageTitle} · GloryStock`;
  }, [pageTitle]);

  // Close the drawer with Escape for keyboard users.
  useEffect(() => {
    if (!drawerOpen) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setDrawerOpen(false);
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [drawerOpen]);

  if (!user) return null;

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const visibleSections = navSections
    .map((s) => ({ ...s, items: s.items.filter((i) => !i.admin || user.role === 'admin') }))
    .filter((s) => s.items.length > 0);

  const navId = 'app-sidebar-nav';

  const navContent = (
    <div className="flex h-full flex-col">
      <div className="flex h-16 items-center gap-3 px-5">
        <img src="/logo.png" alt="" className="h-8 w-auto object-contain" />
        <span className="text-sm font-extrabold tracking-widest text-white">GLORYSTOCK</span>
      </div>

      <nav id={navId} className="flex-1 space-y-6 overflow-y-auto px-3 pb-6 pt-2">
        {visibleSections.map((section) => (
          <div key={section.heading}>
            <p className="px-3 pb-2 text-[0.65rem] font-bold uppercase tracking-widest text-slate-500">
              {section.heading}
            </p>
            <ul className="space-y-1">
              {section.items.map((item) => (
                <li key={item.to}>
                  <NavLink
                    to={item.to}
                    end={item.exact}
                    onClick={() => setDrawerOpen(false)}
                    className={({ isActive }) =>
                      `group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition ${
                        isActive
                          ? 'bg-brand-600 text-white shadow-sm'
                          : 'text-slate-300 hover:bg-white/5 hover:text-white'
                      }`
                    }
                  >
                    <item.icon className="h-4 w-4 shrink-0" aria-hidden />
                    <span>{item.label}</span>
                  </NavLink>
                </li>
              ))}
            </ul>
          </div>
        ))}
      </nav>

      <div className="border-t border-slate-800 p-4">
        <div className="flex items-center gap-3">
          <span
            aria-hidden
            className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-bold text-white"
          >
            {user.username.slice(0, 1).toUpperCase()}
          </span>
          <div className="min-w-0">
            <p className="truncate text-sm font-semibold text-white">{user.username}</p>
            <span
              className={`mt-0.5 inline-block rounded-full px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider ring-1 ${roleBadge(
                user.role
              )}`}
            >
              {user.role}
            </span>
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-surface text-ink-950">
      {/* Desktop sidebar */}
      <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 bg-slate-900 lg:block">
        {navContent}
      </aside>

      {/* Mobile drawer */}
      <AnimatePresence>
        {drawerOpen && (
          <>
            <motion.div
              key="overlay"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.2 }}
              className="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden"
              onClick={() => setDrawerOpen(false)}
              aria-hidden
            />
            <motion.aside
              key="drawer"
              initial={{ x: '-100%' }}
              animate={{ x: 0 }}
              exit={{ x: '-100%' }}
              transition={{ duration: 0.25, ease: [0.22, 1, 0.36, 1] }}
              className="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 shadow-panel lg:hidden"
              role="dialog"
              aria-modal="true"
              aria-label="Navigation menu"
            >
              <button
                onClick={() => setDrawerOpen(false)}
                aria-label="Close menu"
                className="absolute right-3 top-4 rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-white"
              >
                <X className="h-5 w-5" />
              </button>
              {navContent}
            </motion.aside>
          </>
        )}
      </AnimatePresence>

      <div className="lg:pl-64">
        {/* Top bar */}
        <header className="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur">
          <div className="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
            <button
              onClick={() => setDrawerOpen(true)}
              aria-label="Open menu"
              aria-expanded={drawerOpen}
              aria-controls={navId}
              className="rounded-lg p-2 text-ink-600 transition hover:bg-slate-100 hover:text-ink-950 lg:hidden"
            >
              <Menu className="h-5 w-5" />
            </button>

            <div className="min-w-0 flex-1">
              {meta.crumb && (
                <p className="truncate text-xs font-semibold text-ink-400">
                  {meta.crumb} <span aria-hidden>›</span> {meta.title}
                </p>
              )}
              <div className="flex items-center gap-2">
                {meta.icon && (
                  <meta.icon className="hidden h-4 w-4 shrink-0 text-brand-600 sm:block" aria-hidden />
                )}
                <h1 className="truncate text-lg font-extrabold tracking-tight text-ink-950 sm:text-xl">
                  {meta.title}
                </h1>
              </div>
              {meta.description && (
                <p className="hidden truncate text-xs text-ink-500 sm:block">{meta.description}</p>
              )}
            </div>

            <div className="flex items-center gap-2 sm:gap-3">
              <div className="hidden items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 sm:flex">
                <span
                  aria-hidden
                  className="flex h-6 w-6 items-center justify-center rounded-full bg-brand-100 text-[0.7rem] font-bold text-brand-700"
                >
                  {user.username.slice(0, 1).toUpperCase()}
                </span>
                <span className="max-w-28 truncate text-xs font-semibold text-ink-800">
                  {user.username}
                </span>
                <span
                  className={`rounded-full px-1.5 py-0.5 text-[0.6rem] font-bold uppercase tracking-wider ring-1 ${roleBadge(
                    user.role
                  )}`}
                >
                  {user.role}
                </span>
              </div>

              <button
                onClick={handleLogout}
                className="flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-semibold text-ink-500 transition hover:bg-red-50 hover:text-red-600 sm:px-3"
                title="Sign out"
              >
                <LogOut className="h-4 w-4" aria-hidden />
                <span className="hidden sm:inline">Logout</span>
              </button>
            </div>
          </div>
        </header>

        <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <motion.div
            key={location.pathname}
            initial={{ opacity: 0, y: 6 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.25, ease: 'easeOut' }}
          >
            <Outlet />
          </motion.div>
        </main>
      </div>
    </div>
  );
}

import { useState } from 'react';
import { Link } from 'react-router-dom';

const navLinks = [
  { label: 'Home', href: '#home' },
  { label: 'Features', href: '#features' },
  { label: 'How it works', href: '#how-it-works' },
  { label: 'Contact', href: '#contact' },
];

export default function LandingNavbar() {
  const [open, setOpen] = useState(false);

  return (
    <header id="home" className="sticky top-0 z-50 border-b border-slate-200 bg-slate-900">
      <nav className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <Link to="/" className="flex items-center gap-3 font-extrabold tracking-wide text-white">
          <img src="/logo.png" alt="GloryStock" className="h-9 w-auto" />
          <span className="tracking-wider">GLORYSTOCK</span>
        </Link>

        {/* Desktop links */}
        <div className="hidden items-center gap-1 md:flex">
          {navLinks.map((l) => (
            <a
              key={l.href}
              href={l.href}
              className="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white"
            >
              {l.label}
            </a>
          ))}
        </div>

        <div className="hidden items-center gap-2 md:flex">
          <Link
            to="/login"
            className="rounded-lg px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800 hover:text-white"
          >
            Login
          </Link>
          <Link
            to="/register"
            className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-700"
          >
            Get Started
          </Link>
        </div>

        {/* Mobile hamburger */}
        <button
          onClick={() => setOpen((v) => !v)}
          aria-expanded={open}
          aria-controls="mobile-menu"
          aria-label="Toggle navigation"
          className="rounded-lg p-2 text-slate-300 transition hover:bg-slate-800 hover:text-white md:hidden"
        >
          <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            {open ? (
              <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
            ) : (
              <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            )}
          </svg>
        </button>
      </nav>

      {/* Mobile menu */}
      {open && (
        <div id="mobile-menu" className="border-t border-slate-800 bg-slate-900 md:hidden">
          <div className="space-y-1 px-4 py-3">
            {navLinks.map((l) => (
              <a
                key={l.href}
                href={l.href}
                onClick={() => setOpen(false)}
                className="block rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white"
              >
                {l.label}
              </a>
            ))}
            <div className="flex gap-2 pt-3">
              <Link
                to="/login"
                onClick={() => setOpen(false)}
                className="flex-1 rounded-lg border border-slate-700 px-4 py-2 text-center text-sm font-semibold text-slate-200 hover:bg-slate-800 transition"
              >
                Login
              </Link>
              <Link
                to="/register"
                onClick={() => setOpen(false)}
                className="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-bold text-white hover:bg-blue-700 transition"
              >
                Get Started
              </Link>
            </div>
          </div>
        </div>
      )}
    </header>
  );
}

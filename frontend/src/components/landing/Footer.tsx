import { Link } from 'react-router-dom';

export default function Footer() {
  return (
    <footer id="contact" className="scroll-mt-20 border-t border-slate-800 bg-slate-900">
      <div className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
          <div className="sm:col-span-2 lg:col-span-1">
            <div className="flex items-center gap-3">
              <img src="/logo.png" alt="GloryStock" className="h-9 w-auto" />
              <span className="font-extrabold tracking-wider text-white">GLORYSTOCK</span>
            </div>
            <p className="mt-4 text-sm leading-relaxed text-slate-400">
              Inventory and point-of-sale management for small businesses.
            </p>
          </div>

          <div>
            <h3 className="text-sm font-bold uppercase tracking-wide text-white">Product</h3>
            <ul className="mt-4 space-y-2 text-sm">
              <li><a href="#features" className="text-slate-400 transition hover:text-white">Features</a></li>
              <li><a href="#about" className="text-slate-400 transition hover:text-white">About</a></li>
              <li><Link to="/register" className="text-slate-400 transition hover:text-white">Get Started</Link></li>
            </ul>
          </div>

          <div>
            <h3 className="text-sm font-bold uppercase tracking-wide text-white">Account</h3>
            <ul className="mt-4 space-y-2 text-sm">
              <li><Link to="/login" className="text-slate-400 transition hover:text-white">Login</Link></li>
              <li><Link to="/register" className="text-slate-400 transition hover:text-white">Register</Link></li>
            </ul>
          </div>

          <div>
            <h3 className="text-sm font-bold uppercase tracking-wide text-white">Contact</h3>
            <ul className="mt-4 space-y-2 text-sm text-slate-400">
              <li>📞 0617 008 046</li>
              <li>📍 Dar es Salaam, Tanzania</li>
            </ul>
          </div>
        </div>

        <div className="mt-12 border-t border-slate-800 pt-8 text-center text-xs text-slate-500">
          © {new Date().getFullYear()} GloryStock. All rights reserved.
        </div>
      </div>
    </footer>
  );
}

import ScrollReveal from './ScrollReveal';
import { motion } from 'framer-motion';
import { Package, BarChart3, AlertTriangle, FileText, TrendingUp, Shield } from 'lucide-react';

const iconMap: Record<string, any> = {
  '📦': Package,
  '📊': BarChart3,
  '⚠️': AlertTriangle,
  '🧾': FileText,
  '📈': TrendingUp,
  '🔐': Shield,
};

const features = [
  {
    icon: '📦',
    title: 'Product & Inventory Management',
    body: 'Add products with SKUs, prices, categories, and stock counts. Edit anything and every stock change is recorded automatically.',
  },
  {
    icon: '📊',
    title: 'Stock Level Monitoring',
    body: 'See total stock valuation, per-item quantities, and health status at a glance from a single dashboard.',
  },
  {
    icon: '⚠️',
    title: 'Low-Stock Alerts',
    body: 'Items at or below the reorder threshold are flagged REORDER automatically, with a printable reorder sheet for suppliers.',
  },
  {
    icon: '🧾',
    title: 'Sales & Stock Movement Tracking',
    body: 'Every sale, restock, correction, and void is written to a full audit trail — who, what, when, and why.',
  },
  {
    icon: '📈',
    title: 'Reports & Analytics',
    body: 'Revenue vs expenses over six months, monthly profit statements, best sellers, and end-of-day settlement reports.',
  },
  {
    icon: '🔐',
    title: 'Authentication & Access Control',
    body: 'Two roles out of the box: cashiers sell and view stock; admins manage the catalog, expenses, and staff accounts.',
  },
];

export default function Features() {
  return (
    <section id="features" className="scroll-mt-20 bg-white py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <ScrollReveal>
          <div className="mx-auto max-w-2xl text-center">
            <motion.span
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.5 }}
              className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-blue-700"
            >
              Features
            </motion.span>
            <h2 className="mt-6 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
              Everything your shop needs
            </h2>
            <p className="mt-4 text-lg leading-relaxed text-slate-600">
              Built for real counter work — fast sales, honest numbers, and a complete audit trail of every item that moves.
            </p>
          </div>
        </ScrollReveal>

        <div className="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {features.map((f, i) => {
            const Icon = iconMap[f.icon];
            return (
              <ScrollReveal key={f.title} delay={i * 0.05} y={20}>
                <motion.div
                  whileHover={{ y: -4 }}
                  transition={{ duration: 0.2 }}
                  className="group relative h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-lg"
                >
                  <div className="absolute inset-0 -z-10 rounded-2xl bg-gradient-to-br from-blue-50 to-emerald-50 opacity-0 transition group-hover:opacity-100" />
                  <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-sm ring-1 ring-white/20">
                    {Icon && <Icon className="h-5 w-5" />}
                  </div>
                  <h3 className="mt-4 text-lg font-bold text-slate-900">{f.title}</h3>
                  <p className="mt-2 text-sm leading-relaxed text-slate-600">{f.body}</p>
                </motion.div>
              </ScrollReveal>
            );
          })}
        </div>
      </div>
    </section>
  );
}

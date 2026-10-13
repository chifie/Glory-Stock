import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { ArrowRight, BarChart3, Package, TrendingUp } from 'lucide-react';

export default function Hero() {
  return (
    <section className="relative overflow-hidden bg-gradient-to-b from-slate-50 via-white to-white">
      <div className="absolute inset-x-0 top-[-10%] h-72 bg-gradient-to-b from-blue-100/40 to-transparent blur-3xl" aria-hidden />
      <div className="absolute right-[-20%] top-40 h-96 w-96 rounded-full bg-emerald-100/30 blur-3xl" aria-hidden />
      <div className="absolute left-[-20%] top-80 h-96 w-96 rounded-full bg-blue-100/30 blur-3xl" aria-hidden />

      <div className="relative mx-auto max-w-7xl px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pt-24">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="mx-auto max-w-3xl text-center"
        >
          <motion.span
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.1, duration: 0.5 }}
            className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50/80 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-blue-700 backdrop-blur"
          >
            Inventory & Point of Sale
          </motion.span>

          <motion.h1
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.15, duration: 0.6 }}
            className="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl"
          >
            Know your stock. <span className="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Grow your profit.</span>
          </motion.h1>

          <motion.p
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2, duration: 0.6 }}
            className="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-slate-600"
          >
            GloryStock helps small businesses manage inventory efficiently — track every sale, monitor stock levels in real time, catch low-stock items before they run out, and see your profit at a glance.
          </motion.p>

          <motion.div
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.25, duration: 0.6 }}
            className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row"
          >
            <Link
              to="/register"
              className="group inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3 text-center text-base font-bold text-white shadow-sm transition hover:shadow-md sm:w-auto"
            >
              Get Started Free
              <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" />
            </Link>
            <Link
              to="/login"
              className="w-full rounded-lg border border-slate-300 bg-white px-8 py-3 text-center text-base font-semibold text-slate-800 transition hover:bg-slate-50 sm:w-auto"
            >
              Sign in to your account
            </Link>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3, duration: 0.6 }}
            className="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-slate-500"
          >
            <div className="flex items-center gap-1.5">
              <BarChart3 className="h-4 w-4" />
              <span>Real-time insights</span>
            </div>
            <div className="flex items-center gap-1.5">
              <Package className="h-4 w-4" />
              <span>Full stock audit</span>
            </div>
            <div className="flex items-center gap-1.5">
              <TrendingUp className="h-4 w-4" />
              <span>Profit tracking</span>
            </div>
          </motion.div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4, duration: 0.8 }}
          className="relative mx-auto mt-16 max-w-5xl"
        >
          <div className="absolute -inset-x-4 -inset-y-4 -z-10 bg-gradient-to-r from-blue-100/40 to-emerald-100/30 blur-3xl" aria-hidden />
          <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white/80 shadow-xl backdrop-blur">
            <div className="flex items-center gap-2 border-b border-slate-200 bg-slate-50/80 px-4 py-3">
              <span className="h-3 w-3 rounded-full bg-red-400" />
              <span className="h-3 w-3 rounded-full bg-amber-400" />
              <span className="h-3 w-3 rounded-full bg-emerald-500" />
              <span className="ml-3 rounded-md bg-white px-3 py-1 text-xs text-slate-500 ring-1 ring-slate-200">glorystock.app/dashboard</span>
            </div>
            <div className="p-4 sm:p-6">
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                {[
                  { label: 'Stock Valuation', value: '3,691,000', accent: 'border-l-blue-500', delay: 0.5 },
                  { label: "Today's Revenue", value: '155,000', accent: 'border-l-emerald-500', delay: 0.55 },
                  { label: 'Monthly Expenses', value: '30,000', accent: 'border-l-red-500', delay: 0.6 },
                  { label: 'Net Profit (MTD)', value: '125,000', accent: 'border-l-amber-500', delay: 0.65 },
                ].map((s) => (
                  <motion.div
                    key={s.label}
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: s.delay, duration: 0.5 }}
                    className={`group rounded-xl border-l-4 ${s.accent} bg-white p-4 shadow-sm ring-1 ring-slate-100 transition hover:shadow-md`}
                  >
                    <p className="text-[0.65rem] font-bold uppercase tracking-widest text-slate-500">{s.label}</p>
                    <p className="mt-1 text-lg font-extrabold text-slate-900 sm:text-xl">{s.value}</p>
                  </motion.div>
                ))}
              </div>

              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.7, duration: 0.5 }}
                className="mt-4 overflow-hidden rounded-xl ring-1 ring-slate-200"
              >
                <table className="w-full text-left text-xs sm:text-sm">
                  <thead className="bg-slate-50 uppercase text-slate-500">
                    <tr>
                      <th className="px-4 py-2.5 font-bold tracking-widest">SKU</th>
                      <th className="px-4 py-2.5 font-bold tracking-widest">Product</th>
                      <th className="hidden px-4 py-2.5 font-bold tracking-widest sm:table-cell">Price</th>
                      <th className="px-4 py-2.5 font-bold tracking-widest">Stock</th>
                      <th className="px-4 py-2.5 font-bold tracking-widest">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 bg-white/80">
                    {[
                      { sku: 'COKE-001', name: 'Coca Cola 500ml', price: '5,000', stock: 17, ok: true },
                      { sku: 'AZM-WHT-02', name: 'Azam Wheat Flour 2kg', price: '80,000', stock: 7, ok: true },
                      { sku: 'RW_009', name: 'redblue', price: '4,000', stock: 4, ok: false },
                    ].map((r, i) => (
                      <motion.tr
                        key={r.sku}
                        initial={{ opacity: 0, x: -10 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: 0.75 + i * 0.08, duration: 0.4 }}
                        className="hover:bg-slate-50/80"
                      >
                        <td className="px-4 py-2.5 text-slate-500">{r.sku}</td>
                        <td className="px-4 py-2.5 font-semibold text-slate-900">{r.name}</td>
                        <td className="hidden px-4 py-2.5 font-semibold sm:table-cell">{r.price} /=</td>
                        <td className={`px-4 py-2.5 font-semibold ${r.ok ? 'text-slate-900' : 'text-red-600'}`}>{r.stock}</td>
                        <td className="px-4 py-2.5">
                          {r.ok ? (
                            <span className="rounded-full bg-emerald-500/90 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider text-white">Healthy</span>
                          ) : (
                            <span className="rounded-full border border-red-300 bg-red-50 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider text-red-700">Reorder</span>
                          )}
                        </td>
                      </motion.tr>
                    ))}
                  </tbody>
                </table>
              </motion.div>
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  );
}

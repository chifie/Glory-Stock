import ScrollReveal from './ScrollReveal';
import { motion } from 'framer-motion';

const benefits = [
  { title: 'Stop losing stock silently', body: 'Every unit in and out is logged. Discrepancies have a paper trail.' },
  { title: 'Reorder before you run out', body: 'Automatic low-stock flags and a printable supplier sheet, so shelves never sit empty.' },
  { title: 'See true profit, not just sales', body: 'Revenue minus recorded expenses, per month — no more mental math.' },
  { title: 'Run the counter in seconds', body: 'A two-field checkout that takes a product and quantity and does the rest.' },
];

export default function Benefits() {
  return (
    <section id="about" className="scroll-mt-20 bg-slate-100 py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
          <ScrollReveal>
            <div>
              <span className="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-slate-700">
                Why choose us
              </span>
              <h2 className="mt-6 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                Why shops switch to GloryStock
              </h2>
              <p className="mt-4 text-lg leading-relaxed text-slate-600">
                GloryStock started as the stock book of a real store in Dar es Salaam. It keeps that same no-nonsense discipline, powered by a proper database and a full audit trail.
              </p>

              <ul className="mt-8 space-y-5">
                {benefits.map((b, i) => (
                  <ScrollReveal key={b.title} delay={i * 0.05} y={10}>
                    <li className="flex gap-3">
                      <motion.span
                        whileHover={{ scale: 1.05 }}
                        className="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-sm font-bold text-white shadow-sm"
                      >
                        ✓
                      </motion.span>
                      <div>
                        <p className="font-bold text-slate-900">{b.title}</p>
                        <p className="mt-0.5 text-sm leading-relaxed text-slate-600">{b.body}</p>
                      </div>
                    </li>
                  </ScrollReveal>
                ))}
              </ul>
            </div>
          </ScrollReveal>

          <ScrollReveal delay={0.1} y={20}>
            <div className="grid grid-cols-2 gap-4">
              {[
                { stat: '100%', label: 'of stock changes audited' },
                { stat: '2 fields', label: 'to complete a sale' },
                { stat: '6 months', label: 'of financial analytics' },
                { stat: '2 roles', label: 'of access control' },
              ].map((s, i) => (
                <motion.div
                  key={s.label}
                  initial={{ opacity: 0, scale: 0.98 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ delay: 0.1 + i * 0.05, duration: 0.5 }}
                  whileHover={{ y: -3 }}
                  className="group relative rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition hover:shadow-md"
                >
                  <div className="absolute inset-0 -z-10 rounded-2xl bg-gradient-to-br from-blue-50 to-emerald-50 opacity-0 transition group-hover:opacity-100" />
                  <p className="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-2xl font-extrabold text-transparent sm:text-3xl">{s.stat}</p>
                  <p className="mt-1 text-sm font-medium text-slate-600">{s.label}</p>
                </motion.div>
              ))}
            </div>
          </ScrollReveal>
        </div>
      </div>
    </section>
  );
}

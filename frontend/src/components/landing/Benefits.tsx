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
          <div>
            <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
              Why shops switch to GloryStock
            </h2>
            <p className="mt-4 text-lg leading-relaxed text-slate-600">
              GloryStock started as the stock book of a real store in Dar es Salaam.
              It keeps that same no-nonsense discipline, powered by a proper database
              and a full audit trail.
            </p>

            <ul className="mt-8 space-y-5">
              {benefits.map((b) => (
                <li key={b.title} className="flex gap-3">
                  <span className="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                    ✓
                  </span>
                  <div>
                    <p className="font-bold text-slate-900">{b.title}</p>
                    <p className="mt-0.5 text-sm leading-relaxed text-slate-600">{b.body}</p>
                  </div>
                </li>
              ))}
            </ul>
          </div>

          <div className="grid grid-cols-2 gap-4">
            {[
              { stat: '100%', label: 'of stock changes audited' },
              { stat: '2 fields', label: 'to complete a sale' },
              { stat: '6 months', label: 'of financial analytics' },
              { stat: '2 roles', label: 'of access control' },
            ].map((s) => (
              <div key={s.label} className="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                <p className="text-2xl font-extrabold text-blue-600 sm:text-3xl">{s.stat}</p>
                <p className="mt-1 text-sm font-medium text-slate-600">{s.label}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

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
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            Everything your shop needs
          </h2>
          <p className="mt-4 text-lg leading-relaxed text-slate-600">
            Built for real counter work — fast sales, honest numbers, and a complete
            audit trail of every item that moves.
          </p>
        </div>

        <div className="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {features.map((f) => (
            <div
              key={f.title}
              className="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md"
            >
              <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-xl">
                {f.icon}
              </div>
              <h3 className="mt-4 text-lg font-bold text-slate-900">{f.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-slate-600">{f.body}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

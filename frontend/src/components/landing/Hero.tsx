import { Link } from 'react-router-dom';

export default function Hero() {
  return (
    <section className="bg-slate-100">
      <div className="mx-auto max-w-7xl px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pt-24">
        <div className="mx-auto max-w-3xl text-center">
          <span className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-blue-700">
            Inventory &amp; Point of Sale
          </span>

          <h1 className="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
            Know your stock. <span className="text-blue-600">Grow your profit.</span>
          </h1>

          <p className="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-slate-600">
            GloryStock helps small businesses manage inventory efficiently — track every sale,
            monitor stock levels in real time, catch low-stock items before they run out, and
            see your profit at a glance.
          </p>

          <div className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <Link
              to="/register"
              className="w-full rounded-lg bg-blue-600 px-8 py-3 text-center text-base font-bold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
              Get Started Free
            </Link>
            <Link
              to="/login"
              className="w-full rounded-lg border border-slate-300 bg-white px-8 py-3 text-center text-base font-semibold text-slate-800 transition hover:bg-slate-50 sm:w-auto"
            >
              Sign in to your account
            </Link>
          </div>
        </div>

        {/* Dashboard preview — mock of the real inventory dashboard */}
        <div className="mt-16 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
          <div className="flex items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3">
            <span className="h-3 w-3 rounded-full bg-red-400" />
            <span className="h-3 w-3 rounded-full bg-amber-400" />
            <span className="h-3 w-3 rounded-full bg-emerald-500" />
            <span className="ml-3 rounded-md bg-white px-3 py-1 text-xs text-slate-400 ring-1 ring-slate-200">
              glorystock.test/dashboard
            </span>
          </div>

          <div className="p-4 sm:p-6">
            <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
              {[
                { label: 'Stock Valuation', value: '3,691,000', accent: 'border-l-blue-500' },
                { label: "Today's Revenue", value: '155,000', accent: 'border-l-emerald-500' },
                { label: 'Monthly Expenses', value: '30,000', accent: 'border-l-red-500' },
                { label: 'Net Profit (MTD)', value: '125,000', accent: 'border-l-amber-500' },
              ].map((s) => (
                <div key={s.label} className={`rounded-xl border-l-4 ${s.accent} bg-white p-4 shadow-sm ring-1 ring-slate-100`}>
                  <p className="text-[0.65rem] font-bold uppercase tracking-wide text-slate-500">{s.label}</p>
                  <p className="mt-1 text-lg font-extrabold text-slate-900 sm:text-xl">{s.value}</p>
                </div>
              ))}
            </div>

            <div className="mt-4 overflow-hidden rounded-xl ring-1 ring-slate-200">
              <table className="w-full text-left text-xs sm:text-sm">
                <thead className="bg-slate-50 uppercase text-slate-500">
                  <tr>
                    <th className="px-4 py-2.5 font-bold">SKU</th>
                    <th className="px-4 py-2.5 font-bold">Product</th>
                    <th className="hidden px-4 py-2.5 font-bold sm:table-cell">Price</th>
                    <th className="px-4 py-2.5 font-bold">Stock</th>
                    <th className="px-4 py-2.5 font-bold">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {[
                    { sku: 'COKE-001', name: 'Coca Cola 500ml', price: '5,000', stock: 17, ok: true },
                    { sku: 'AZM-WHT-02', name: 'Azam Wheat Flour 2kg', price: '80,000', stock: 7, ok: true },
                    { sku: 'RW_009', name: 'redblue', price: '4,000', stock: 4, ok: false },
                  ].map((r) => (
                    <tr key={r.sku} className="hover:bg-slate-50">
                      <td className="px-4 py-2.5 text-slate-500">{r.sku}</td>
                      <td className="px-4 py-2.5 font-bold text-slate-900">{r.name}</td>
                      <td className="hidden px-4 py-2.5 font-bold sm:table-cell">{r.price} /=</td>
                      <td className={`px-4 py-2.5 font-bold ${r.ok ? '' : 'text-red-600'}`}>{r.stock}</td>
                      <td className="px-4 py-2.5">
                        {r.ok ? (
                          <span className="rounded-full bg-emerald-500 px-2 py-0.5 text-[0.65rem] font-bold text-white">HEALTHY</span>
                        ) : (
                          <span className="rounded-full border border-red-300 bg-red-100 px-2 py-0.5 text-[0.65rem] font-bold text-red-800">REORDER</span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

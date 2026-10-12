import { useEffect, useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { api, ApiError } from '../api';
import { TZS, type Product, type RecentSale } from '../types';

interface PosData {
  products: Product[];
  recentSales: RecentSale[];
}

interface SaleResult {
  message: string;
  receipt_id: number;
  amount: number;
}

export default function Pos() {
  const [data, setData] = useState<PosData | null>(null);
  const [result, setResult] = useState<SaleResult | null>(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const [productId, setProductId] = useState('');
  const [quantity, setQuantity] = useState(1);

  const load = () => api.get<PosData>('/pos').then(setData).catch(() => {});

  useEffect(() => {
    load();
  }, []);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setBusy(true);
    try {
      const res = await api.post<SaleResult>('/pos', {
        product_id: Number(productId),
        quantity: Number(quantity),
      });
      setResult(res);
      setProductId('');
      setQuantity(1);
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Sale failed. Try again.');
    } finally {
      setBusy(false);
    }
  };

  if (!data) return <p className="text-slate-500">Loading…</p>;

  return (
    <div>
      <div className="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
        <img src="/logo.png" alt="GloryStock" className="h-11 w-auto object-contain" />
        <div>
          <h3 className="text-xl font-extrabold">POS Terminal</h3>
          <p className="text-slate-500 text-sm">Process customer sales quickly and securely.</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div className="lg:col-span-5">
          {result && (
            <div className="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 text-center py-6 shadow-sm">
              <div className="text-4xl mb-2">✅</div>
              <h4 className="font-bold text-lg">Transaction Complete</h4>
              <p className="text-slate-600">
                Total Paid: <span className="font-bold text-slate-900">{TZS(result.amount)} TZS</span>
              </p>
              <Link
                to={`/receipt/${result.receipt_id}`}
                className="inline-block mt-2 rounded-lg bg-slate-900 px-5 py-2 font-bold text-white hover:bg-slate-800 transition"
              >
                🖨️ View Receipt
              </Link>
            </div>
          )}

          {error && (
            <div className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
              ⚠️ {error}
            </div>
          )}

          <div className="rounded-xl bg-white shadow-md mb-6 p-5">
            <div className="flex justify-between items-center mb-5">
              <h5 className="font-bold text-sm uppercase tracking-widest">Checkout</h5>
            </div>

            <form onSubmit={submit}>
              <div className="mb-4">
                <label htmlFor="product" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">
                  Select Item
                </label>
                <select
                  id="product"
                  value={productId}
                  onChange={(e) => setProductId(e.target.value)}
                  className="w-full rounded-lg border-0 bg-slate-100 px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                  required
                >
                  <option value="">-- Choose Product --</option>
                  {data.products.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.name} — {TZS(p.price)} (Stock: {p.stock})
                    </option>
                  ))}
                </select>
              </div>

              <div className="mb-5">
                <label htmlFor="qty" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">
                  Quantity
                </label>
                <input
                  id="qty"
                  type="number"
                  min={1}
                  value={quantity}
                  onChange={(e) => setQuantity(Number(e.target.value))}
                  className="w-full rounded-lg border-0 bg-slate-100 px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                  required
                />
              </div>

              <button
                type="submit"
                disabled={busy || !productId}
                className="w-full rounded-lg bg-slate-900 py-3 font-bold uppercase tracking-wide text-white hover:bg-slate-800 transition disabled:opacity-50"
              >
                {busy ? 'Processing…' : 'Process Sale'}
              </button>
            </form>
          </div>
        </div>

        <div className="lg:col-span-7">
          <div className="flex justify-between items-center mb-3">
            <h5 className="font-bold text-lg">Recent Sales</h5>
            <Link to="/sales-history" className="text-sm text-blue-600 hover:underline">
              View All
            </Link>
          </div>

          <div className="rounded-xl bg-white shadow-sm overflow-hidden">
            <table className="w-full text-left">
              <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="pl-5 py-3">Time</th>
                  <th className="py-3">Item</th>
                  <th className="py-3">Qty</th>
                  <th className="py-3">Total</th>
                  <th className="pr-5 py-3 text-right">Receipt</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {data.recentSales.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="text-center py-5 text-slate-500">
                      No sales yet today.
                    </td>
                  </tr>
                ) : (
                  data.recentSales.map((s) => (
                    <tr key={s.id} className="hover:bg-slate-50">
                      <td className="pl-5 py-3 text-sm">
                        {new Date(s.sale_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                      </td>
                      <td className="py-3 font-bold">{s.product_name}</td>
                      <td className="py-3">
                        <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs">x{s.quantity}</span>
                      </td>
                      <td className="py-3 font-bold">{TZS(s.total_price)}</td>
                      <td className="pr-5 py-3 text-right">
                        <Link
                          to={`/receipt/${s.id}`}
                          className="inline-block rounded-full border border-slate-300 px-3 py-1 text-xs hover:bg-slate-900 hover:text-white transition"
                        >
                          🖨️ View
                        </Link>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          <div className="mt-5 flex gap-3">
            <Link
              to="/stock-log"
              className="inline-block rounded-lg bg-white px-4 py-2 text-sm font-bold text-slate-500 shadow-sm hover:bg-slate-50 transition"
            >
              🛡️ View Stock Audit Trail
            </Link>
            <Link
              to="/daily-close"
              className="inline-block rounded-lg bg-white px-4 py-2 text-sm font-bold text-slate-500 shadow-sm hover:bg-slate-50 transition"
            >
              📋 Daily Close
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}

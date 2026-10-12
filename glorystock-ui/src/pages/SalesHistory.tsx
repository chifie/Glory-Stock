import { useCallback, useEffect, useState } from 'react';
import { api, ApiError } from '../api';
import { useAuth } from '../auth';
import { TZS, type SaleRow } from '../types';

interface SalesData {
  sales: SaleRow[];
  totalRevenue: number;
  topProduct: { name: string; total_qty: number } | null;
  startDate: string;
  endDate: string;
}

export default function SalesHistory() {
  const { user } = useAuth();
  const isAdmin = user?.role === 'admin';

  const [data, setData] = useState<SalesData | null>(null);
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  const load = useCallback(() => {
    const params = new URLSearchParams();
    if (startDate) params.set('start_date', startDate);
    if (endDate) params.set('end_date', endDate);

    api.get<SalesData>(`/sales-history?${params.toString()}`).then((d) => {
      setData(d);
      setStartDate(d.startDate);
      setEndDate(d.endDate);
    }).catch(() => {});
  }, [startDate, endDate]);

  useEffect(() => {
    // Initial load without date params; then keep controlled state
    api.get<SalesData>('/sales-history').then((d) => {
      setData(d);
      setStartDate(d.startDate);
      setEndDate(d.endDate);
    }).catch(() => {});
  }, []);

  const voidSale = async (id?: number) => {
    if (!id || !window.confirm('Void this sale and restore stock?')) return;
    setError('');
    try {
      const res = await api.delete<{ message: string }>(`/sales/${id}`);
      setNotice(res.message);
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Void failed.');
    }
  };

  if (!data) return <p className="text-slate-500">Loading…</p>;

  return (
    <div>
      {notice && (
        <div className="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
          {notice}
        </div>
      )}
      {error && (
        <div className="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
          {error}
        </div>
      )}

      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-extrabold">Sales Analytics</h2>
        <button
          onClick={() => window.print()}
          className="no-print rounded-lg border border-slate-400 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-100 transition"
        >
          🖨️ Export PDF/Print
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        <div className="rounded-xl bg-blue-600 p-5 text-white shadow-md">
          <h6 className="text-xs font-bold uppercase tracking-wide opacity-75">Total Period Revenue</h6>
          <h2 className="text-3xl font-extrabold">{TZS(data.totalRevenue)} TZS</h2>
        </div>
        <div className="rounded-xl bg-emerald-700 p-5 text-white shadow-md">
          <h6 className="text-xs font-bold uppercase tracking-wide opacity-75">Top Moving Product</h6>
          <h2 className="text-3xl font-extrabold">{data.topProduct?.name ?? 'N/A'}</h2>
          <small className="opacity-90">Units Sold: {data.topProduct?.total_qty ?? 0}</small>
        </div>
      </div>

      <div className="rounded-xl bg-white shadow-md mb-6 p-5">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
          <div>
            <label htmlFor="from" className="block text-xs font-bold text-slate-500 mb-1">
              From Date
            </label>
            <input
              id="from"
              type="date"
              value={startDate}
              onChange={(e) => setStartDate(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div>
            <label htmlFor="to" className="block text-xs font-bold text-slate-500 mb-1">
              To Date
            </label>
            <input
              id="to"
              type="date"
              value={endDate}
              onChange={(e) => setEndDate(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div>
            <button onClick={load} className="w-full rounded-lg bg-slate-900 py-2 font-bold text-white hover:bg-slate-800 transition">
              Update Report
            </button>
          </div>
        </div>
      </div>

      <div className="rounded-xl bg-white shadow-md overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th className="pl-5 py-3">Timestamp</th>
              <th className="py-3">Product Details</th>
              <th className="py-3">Quantity</th>
              <th className="pr-5 py-3 text-right">Total Price</th>
              {isAdmin && <th className="pr-5 py-3 text-right">Actions</th>}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data.sales.length === 0 ? (
              <tr>
                <td colSpan={isAdmin ? 5 : 4} className="text-center py-10 text-slate-500">
                  No sales recorded for this period.
                </td>
              </tr>
            ) : (
              data.sales.map((s) => (
                <tr key={s.id} className="hover:bg-slate-50">
                  <td className="pl-5 py-3 text-sm text-slate-500">{new Date(s.sale_date).toLocaleString()}</td>
                  <td className="py-3">
                    <div className="font-bold">{s.product_name}</div>
                    <div className="text-xs text-slate-500">SKU: {s.sku}</div>
                  </td>
                  <td className="py-3">
                    <span className="rounded-full border border-slate-200 bg-slate-100 px-3 py-0.5 text-xs">{s.quantity}</span>
                  </td>
                  <td className="pr-5 py-3 text-right font-extrabold">{TZS(s.total_price)}</td>
                  {isAdmin && (
                    <td className="pr-5 py-3 text-right">
                      <button
                        onClick={() => voidSale(s.id)}
                        className="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition"
                      >
                        Void
                      </button>
                    </td>
                  )}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

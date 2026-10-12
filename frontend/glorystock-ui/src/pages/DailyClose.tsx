import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api';
import { TZS, type SaleRow } from '../types';

interface DailyCloseData {
  today: string;
  summary: { total_transactions: number; total_cash: number; total_items: number };
  dailySales: SaleRow[];
}

export default function DailyClose() {
  const [data, setData] = useState<DailyCloseData | null>(null);

  useEffect(() => {
    api.get<DailyCloseData>('/daily-close').then(setData).catch(() => {});
  }, []);

  if (!data) return <p className="text-slate-500">Loading…</p>;

  return (
    <div>
      <div className="rounded-b-3xl bg-slate-800 py-10 text-center text-white shadow-md mb-8">
        <h1 className="text-2xl font-extrabold tracking-wide">DAILY CLOSE REPORT</h1>
        <p className="opacity-75">Date: {new Date(data.today).toDateString()}</p>
        <div className="mt-3 flex items-center justify-center gap-3">
          <button onClick={() => window.print()} className="rounded-lg bg-white px-5 py-2 font-bold text-slate-800 hover:bg-slate-100 transition">
            🖨 Print Summary
          </button>
          <Link to="/dashboard" className="rounded-lg border border-white/50 px-5 py-2 font-semibold text-white hover:bg-white/10 transition">
            Back to Dashboard
          </Link>
        </div>
      </div>

      <div className="max-w-5xl mx-auto px-4 pb-10">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
          <div className="rounded-2xl bg-white p-6 text-center shadow-sm">
            <div className="mb-1 text-xs font-bold uppercase text-slate-500">Total Cash In</div>
            <div className="text-3xl font-extrabold text-emerald-600">{TZS(data.summary.total_cash)}</div>
          </div>
          <div className="rounded-2xl bg-white p-6 text-center shadow-sm">
            <div className="mb-1 text-xs font-bold uppercase text-slate-500">Transactions</div>
            <div className="text-3xl font-extrabold text-slate-900">{data.summary.total_transactions}</div>
          </div>
          <div className="rounded-2xl bg-white p-6 text-center shadow-sm">
            <div className="mb-1 text-xs font-bold uppercase text-slate-500">Items Sold</div>
            <div className="text-3xl font-extrabold text-slate-900">{data.summary.total_items}</div>
          </div>
        </div>

        <div className="rounded-2xl bg-white shadow-sm overflow-hidden">
          <div className="border-b border-slate-200 px-5 py-3">
            <h5 className="font-bold">Today&apos;s Sales Audit Log</h5>
          </div>
          <table className="w-full text-left">
            <thead className="bg-slate-50 text-xs uppercase text-slate-500">
              <tr>
                <th className="pl-5 py-3">Time</th>
                <th className="py-3">Product</th>
                <th className="py-3">Qty</th>
                <th className="pr-5 py-3 text-right">Total</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {data.dailySales.length === 0 ? (
                <tr>
                  <td colSpan={4} className="text-center py-10 text-slate-500">
                    No sales recorded today.
                  </td>
                </tr>
              ) : (
                data.dailySales.map((s, i) => (
                  <tr key={i} className="hover:bg-slate-50">
                    <td className="pl-5 py-3 text-slate-500">
                      {new Date(s.sale_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </td>
                    <td className="py-3 font-bold">{s.product_name}</td>
                    <td className="py-3">{s.quantity}</td>
                    <td className="pr-5 py-3 text-right font-bold">{TZS(s.total_price)}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

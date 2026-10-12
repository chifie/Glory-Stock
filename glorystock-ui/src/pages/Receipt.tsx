import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api } from '../api';
import { TZS, type ReceiptData } from '../types';

export default function Receipt() {
  const { id } = useParams<{ id: string }>();
  const [data, setData] = useState<ReceiptData | null>(null);

  useEffect(() => {
    if (id) api.get<ReceiptData>(`/receipt/${id}`).then(setData).catch(() => {});
  }, [id]);

  if (!data) return <p className="p-8 text-slate-500">Loading…</p>;

  return (
    <div className="min-h-screen bg-slate-100 py-10">
      <div className="no-print text-center mb-8">
        <button
          onClick={() => window.print()}
          className="rounded-full bg-slate-900 px-8 py-2 font-bold text-white shadow hover:bg-slate-800 transition"
        >
          🖨 PRINT RECEIPT
        </button>
        <div className="mt-3">
          <Link to="/pos" className="text-slate-500 text-sm hover:underline">
            ← Back to POS Dashboard
          </Link>
        </div>
      </div>

      <div className="mx-auto w-[90mm] max-w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div className="bg-gradient-to-br from-slate-900 to-slate-700 px-5 py-7 text-center text-white">
          <img src="/logo.png" alt="GloryStock" className="mx-auto mb-3 h-14 w-auto rounded-xl bg-white p-2" />
          <h2 className="text-2xl font-extrabold tracking-widest">GLORYSTOCK</h2>
          <div className="text-xs opacity-75">Inventory Management System</div>
        </div>

        <div className="p-6">
          <div className="mb-5 grid grid-cols-2 gap-2.5 text-xs text-slate-500">
            <div>
              <span className="block font-semibold uppercase text-slate-400">Receipt Number</span>
              <span className="font-bold text-slate-800">
                #GS-{String(data.sale.id).padStart(5, '0')}
              </span>
            </div>
            <div className="text-right">
              <span className="block font-semibold uppercase text-slate-400">Date &amp; Time</span>
              <span className="font-bold text-slate-800">{new Date(data.sale.sale_date).toLocaleString()}</span>
            </div>
            <div>
              <span className="block font-semibold uppercase text-slate-400">Served By</span>
              <span className="font-bold text-slate-800">{(data.sale.cashier_name ?? 'Staff').toUpperCase()}</span>
            </div>
            <div className="text-right">
              <span className="block font-semibold uppercase text-slate-400">Contact Store</span>
              <span className="font-bold text-slate-800">{data.store.phone}</span>
            </div>
          </div>

          <table className="w-full border-collapse mb-5">
            <thead>
              <tr>
                <th className="border-b-2 border-slate-100 py-2.5 text-left text-[0.7rem] uppercase text-slate-400">Description</th>
                <th className="border-b-2 border-slate-100 py-2.5 text-center text-[0.7rem] uppercase text-slate-400">Qty</th>
                <th className="border-b-2 border-slate-100 py-2.5 text-right text-[0.7rem] uppercase text-slate-400">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td className="py-3 text-sm font-bold text-slate-800">{data.sale.product_name}</td>
                <td className="py-3 text-center text-sm text-slate-500">x{data.sale.quantity}</td>
                <td className="py-3 text-right text-sm font-bold">{TZS(data.sale.total_price)}</td>
              </tr>
            </tbody>
          </table>

          <div className="rounded-xl bg-slate-50 p-4">
            <div className="mb-2 flex items-center justify-between border-t border-slate-200 pt-2">
              <span className="font-extrabold text-slate-900">GRAND TOTAL</span>
              <span className="font-mono text-xl font-extrabold text-slate-900">{TZS(data.sale.total_price)}</span>
            </div>
          </div>
        </div>

        <div className="border-t border-dashed border-slate-200 px-5 py-5 text-center text-xs text-slate-400">
          <div className="mb-1 font-bold text-slate-800">Thank you for your business!</div>
          <div>Please visit us again for more quality stock.</div>
          <div className="mt-3 text-[0.6rem] font-bold uppercase tracking-widest">
            {data.store.phone} • {data.store.city} • GloryStock
          </div>
        </div>
      </div>
    </div>
  );
}

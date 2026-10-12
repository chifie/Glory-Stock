import { useEffect, useState } from 'react';
import { api } from '../api';
import { TZS, type Product } from '../types';

interface ReorderData {
  items: Product[];
  today: string;
}

export default function Reorder() {
  const [data, setData] = useState<ReorderData | null>(null);

  useEffect(() => {
    api.get<ReorderData>('/reorder').then(setData).catch(() => {});
  }, []);

  if (!data) return <p className="text-slate-500">Loading…</p>;

  return (
    <div className="bg-white p-10">
      <div className="no-print mb-8 flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
        <span className="text-sm text-slate-500">💡 Print this list to take to the wholesaler.</span>
        <button onClick={() => window.print()} className="rounded-md bg-blue-600 px-5 py-2 font-bold text-white hover:bg-blue-700 transition">
          Print Now
        </button>
      </div>

      <div className="mb-8 flex items-center justify-between border-b-2 border-slate-100 pb-5">
        <div className="flex items-center gap-4">
          <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900 text-xl font-black text-white">GS</div>
          <div>
            <h1 className="text-2xl font-extrabold text-slate-900">GLORYSTOCK</h1>
            <p className="text-[0.8rem] text-slate-500">Inventory Intelligence Report</p>
          </div>
        </div>
        <div className="text-right">
          <p className="font-bold text-slate-800">REORDER SHEET</p>
          <p className="text-xs text-slate-500">{data.today}</p>
        </div>
      </div>

      <div className="mb-6 flex items-center justify-between">
        <div className="rounded-md bg-red-100 px-3 py-1.5 text-xs font-bold text-red-800">
          ⚠️ {data.items.length} Items Need Restocking
        </div>
      </div>

      <table className="mb-16 w-full border-collapse">
        <thead>
          <tr>
            {['SKU', 'Product Description', 'Category', 'Current Stock', 'Order Qty', 'Estimated Unit Cost'].map((h) => (
              <th key={h} className="border-b-2 border-slate-200 p-3 text-left text-[0.7rem] uppercase tracking-wider text-slate-500">
                {h}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {data.items.length === 0 ? (
            <tr>
              <td colSpan={6} className="p-12 text-center text-slate-500">
                ✅ All stock levels are healthy. Nothing to reorder!
              </td>
            </tr>
          ) : (
            data.items.map((item) => (
              <tr key={item.id} className="border-b border-slate-100 text-sm hover:bg-slate-50">
                <td className="p-3 font-mono text-slate-500">{item.sku}</td>
                <td className="p-3 font-bold">{item.name}</td>
                <td className="p-3">{item.category?.name ?? 'General'}</td>
                <td className="p-3 font-bold text-rose-600">{item.stock} units left</td>
                <td className="p-3">
                  <span className="inline-block w-20 border-b border-slate-300">&nbsp;</span>
                </td>
                <td className="p-3 text-slate-400">{TZS(item.price)}</td>
              </tr>
            ))
          )}
        </tbody>
      </table>

      <div className="mt-20 flex justify-between">
        <div className="w-56 border-t border-slate-400 pt-2 text-center text-xs text-slate-500">Store Manager Signature</div>
        <div className="w-56 border-t border-slate-400 pt-2 text-center text-xs text-slate-500">Authorized Approval</div>
      </div>
    </div>
  );
}

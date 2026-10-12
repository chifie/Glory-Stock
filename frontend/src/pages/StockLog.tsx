import { useEffect, useState } from 'react';
import { api } from '../api';
import type { StockLogRow } from '../types';

export default function StockLog() {
  const [logs, setLogs] = useState<StockLogRow[] | null>(null);

  useEffect(() => {
    api.get<{ logs: StockLogRow[] }>('/stock-log').then((d) => setLogs(d.logs)).catch(() => setLogs([]));
  }, []);

  if (!logs) return <p className="text-slate-500">Loading…</p>;

  return (
    <div>
      <div className="mb-6">
        <h4 className="text-lg font-extrabold text-slate-900">Stock Movement History</h4>
        <p className="text-slate-500">Every manual change and sale recorded for accountability.</p>
      </div>

      <div className="rounded-xl bg-white shadow-md overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th className="pl-5 py-3">Timestamp</th>
              <th className="py-3">Product Details</th>
              <th className="py-3">Type</th>
              <th className="py-3 text-center">Quantity</th>
              <th className="py-3">Reason / Reference</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {logs.length === 0 ? (
              <tr>
                <td colSpan={5} className="text-center py-10 text-slate-500">
                  No stock movements found.
                </td>
              </tr>
            ) : (
              logs.map((log) => {
                const positive = log.change_qty > 0;
                return (
                  <tr key={log.id} className="hover:bg-slate-50">
                    <td className="pl-5 py-3 text-sm text-slate-500">
                      {new Date(log.created_at).toLocaleDateString()}
                      <br />
                      <span className="font-bold">
                        {new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </td>
                    <td className="py-3">
                      <div className="font-bold">{log.product_name}</div>
                      <div className="text-xs text-slate-500">{log.sku}</div>
                    </td>
                    <td className="py-3">
                      <span className={`rounded-md px-3 py-1.5 text-xs font-bold ${positive ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}`}>
                        {positive ? 'INCOMING' : 'OUTGOING'}
                      </span>
                    </td>
                    <td className={`py-3 text-center text-lg font-extrabold ${positive ? 'text-emerald-600' : 'text-red-600'}`}>
                      {positive ? '+' : ''}
                      {log.change_qty}
                    </td>
                    <td className="py-3">
                      <span className="text-sm font-bold uppercase text-slate-900">{log.reason}</span>
                    </td>
                  </tr>
                );
              })
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

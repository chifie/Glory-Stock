import { useEffect, useRef, useState } from 'react';
import { api } from '../api';
import { TZS, type ReportRow } from '../types';

interface ReportsData {
  months: string[];
  revenueValues: number[];
  expenseValues: number[];
  topProducts: ReportRow[];
}

// Global window typing for Chart.js loaded via CDN in index.html
declare global {
  interface Window {
    Chart?: new (ctx: CanvasRenderingContext2D, config: unknown) => unknown;
  }
}

export default function Reports() {
  const [data, setData] = useState<ReportsData | null>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    api.get<ReportsData>('/reports').then(setData).catch(() => {});
  }, []);

  useEffect(() => {
    if (!data || !window.Chart || !canvasRef.current) return;

    const ctx = canvasRef.current.getContext('2d');
    if (!ctx) return;

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const ChartCtor = window.Chart as any;

    new ChartCtor(ctx, {
      type: 'line',
      data: {
        labels: data.months,
        datasets: [
          {
            label: 'Total Revenue',
            data: data.revenueValues,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
          },
          {
            label: 'Total Expenses',
            data: data.expenseValues,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ef4444',
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top', labels: { usePointStyle: true, font: { weight: 'bold' } } },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9' },
            ticks: { callback: (v: number) => v.toLocaleString() + ' /=' },
          },
          x: { grid: { display: false } },
        },
      },
    });
  }, [data]);

  if (!data) return <p className="text-slate-500">Loading…</p>;

  return (
    <div>
      <div className="flex justify-between items-center mb-6 border-b border-slate-200 pb-4">
        <div className="flex items-center gap-4">
          <img src="/logo.png" alt="GloryStock" className="h-11 w-auto object-contain" />
          <div>
            <h3 className="text-xl font-extrabold">Business Analytics</h3>
            <p className="text-slate-500 text-sm">Financial overview for the last 6 months</p>
          </div>
        </div>
        <button onClick={() => window.print()} className="rounded-full bg-slate-900 px-5 py-2 font-bold text-white hover:bg-slate-800 transition">
          🖨 Print Report
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <div className="lg:col-span-12">
          <div className="rounded-xl bg-white shadow-md p-5">
            <div className="flex justify-between items-center mb-3">
              <h5 className="font-bold">Revenue vs Expenses</h5>
              <div className="text-sm text-slate-500">Real-time Performance Graph</div>
            </div>
            <div style={{ height: 350 }}>
              <canvas ref={canvasRef} />
            </div>
          </div>
        </div>

        <div className="lg:col-span-7">
          <div className="rounded-xl bg-white shadow-md p-5 h-full">
            <h6 className="text-xs font-bold uppercase text-slate-500 mb-4">Monthly Statement</h6>
            <table className="w-full text-left">
              <thead className="text-xs uppercase text-slate-400">
                <tr>
                  <th className="py-2">Month</th>
                  <th className="py-2">Revenue</th>
                  <th className="py-2">Expenses</th>
                  <th className="py-2">Net Profit</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {data.months.map((month, i) => {
                  const profit = data.revenueValues[i] - data.expenseValues[i];
                  return (
                    <tr key={month} className="hover:bg-slate-50">
                      <td className="py-2.5 font-bold text-slate-900">{month}</td>
                      <td className="py-2.5 font-semibold text-emerald-600">{TZS(data.revenueValues[i])}</td>
                      <td className="py-2.5 text-red-600">{TZS(data.expenseValues[i])}</td>
                      <td className={`py-2.5 font-bold ${profit >= 0 ? 'text-blue-600' : 'text-red-600'}`}>
                        {(profit < 0 ? '-' : '+') + TZS(Math.abs(profit))}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>

        <div className="lg:col-span-5">
          <div className="rounded-xl bg-white shadow-md p-5 h-full">
            <h6 className="text-xs font-bold uppercase text-slate-500 mb-4">Best Selling Products</h6>
            {data.topProducts.length === 0 ? (
              <p className="text-sm text-slate-500">No sales data yet.</p>
            ) : (
              data.topProducts.map((item) => (
                <div key={item.name} className="flex justify-between items-center mb-3 border-b border-slate-100 pb-2">
                  <span className="font-bold text-slate-900">{item.name}</span>
                  <span className="rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white">
                    {item.total_sold} Units Sold
                  </span>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

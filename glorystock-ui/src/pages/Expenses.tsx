import { useCallback, useEffect, useState, type FormEvent } from 'react';
import { api, ApiError } from '../api';
import { TZS, type ExpenseRow } from '../types';

interface ExpensesData {
  expenses: ExpenseRow[];
  categories: string[];
}

export default function Expenses() {
  const [data, setData] = useState<ExpensesData | null>(null);
  const [category, setCategory] = useState('');
  const [amount, setAmount] = useState('');
  const [expenseDate, setExpenseDate] = useState(new Date().toISOString().slice(0, 10));
  const [description, setDescription] = useState('');
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  const load = useCallback(() => {
    api.get<ExpensesData>('/expenses').then((d) => {
      setData(d);
      setCategory((c) => c || d.categories[0] || '');
    }).catch(() => {});
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      const res = await api.post<{ message: string }>('/expenses', {
        category,
        amount: Number(amount),
        description,
        expense_date: expenseDate,
      });
      setNotice(res.message);
      setAmount('');
      setDescription('');
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Save failed.');
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

      <div className="flex items-center gap-4 mb-6 border-b border-slate-200 pb-4">
        <div>
          <h3 className="text-xl font-extrabold">Expense Management</h3>
          <p className="text-slate-500 text-sm">Track and record business outgoings</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div className="lg:col-span-4">
          <div className="rounded-xl bg-white shadow-md p-5">
            <h5 className="font-bold mb-5">Record New Expense</h5>

            <form onSubmit={submit}>
              <div className="mb-4">
                <label htmlFor="cat" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Category</label>
                <select
                  id="cat"
                  value={category}
                  onChange={(e) => setCategory(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                  required
                >
                  {data.categories.map((c) => (
                    <option key={c} value={c}>{c}</option>
                  ))}
                </select>
              </div>

              <div className="mb-4">
                <label htmlFor="amt" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Amount (TZS)</label>
                <div className="flex items-center rounded-lg border border-slate-300 focus-within:border-blue-500">
                  <input
                    id="amt"
                    type="number"
                    step="0.01"
                    min="0.01"
                    value={amount}
                    onChange={(e) => setAmount(e.target.value)}
                    className="w-full rounded-lg px-3 py-2 focus:outline-none"
                    placeholder="e.g. 50000"
                    required
                  />
                  <span className="px-3 text-sm font-bold text-slate-400">/=</span>
                </div>
              </div>

              <div className="mb-4">
                <label htmlFor="date" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Date</label>
                <input
                  id="date"
                  type="date"
                  value={expenseDate}
                  onChange={(e) => setExpenseDate(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                  required
                />
              </div>

              <div className="mb-5">
                <label htmlFor="desc" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Description</label>
                <textarea
                  id="desc"
                  rows={2}
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                  placeholder="What was this for?"
                />
              </div>

              <button type="submit" className="w-full rounded-lg bg-slate-900 py-3 shadow-sm font-bold text-white hover:bg-slate-800 transition">
                💾 Save to Records
              </button>
            </form>
          </div>
        </div>

        <div className="lg:col-span-8">
          <div className="rounded-xl bg-white shadow-md overflow-hidden h-full">
            <div className="flex justify-between items-center border-b border-slate-200 px-5 py-3">
              <h5 className="font-bold">Recent Outgoings</h5>
              <span className="rounded-full border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold">
                {data.expenses.length} Total Items
              </span>
            </div>
            <table className="w-full text-left">
              <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="pl-5 py-3">Date</th>
                  <th className="py-3">Category</th>
                  <th className="py-3">Details</th>
                  <th className="pr-5 py-3 text-right">Amount</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {data.expenses.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="text-center py-10 text-slate-500">No expenses recorded yet.</td>
                  </tr>
                ) : (
                  data.expenses.map((ex) => (
                    <tr key={ex.id} className="hover:bg-slate-50">
                      <td className="pl-5 py-3 text-sm text-slate-500">📅 {new Date(ex.expense_date).toLocaleDateString()}</td>
                      <td className="py-3">
                        <span className="rounded-full border border-red-200 bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                          {ex.category}
                        </span>
                      </td>
                      <td className="py-3 text-sm text-slate-800 font-medium">{ex.description}</td>
                      <td className="pr-5 py-3 text-right font-bold text-slate-900">{TZS(ex.amount)}</td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}

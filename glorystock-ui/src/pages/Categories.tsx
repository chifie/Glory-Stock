import { useCallback, useEffect, useState, type FormEvent } from 'react';
import { api, ApiError } from '../api';
import type { Category } from '../types';

export default function Categories() {
  const [categories, setCategories] = useState<Category[] | null>(null);
  const [name, setName] = useState('');
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  const load = useCallback(() => {
    api.get<{ categories: Category[] }>('/categories').then((d) => setCategories(d.categories)).catch(() => {});
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      const res = await api.post<{ message: string }>('/categories', { name });
      setNotice(res.message);
      setName('');
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Save failed.');
    }
  };

  if (!categories) return <p className="text-slate-500">Loading…</p>;

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
          <h3 className="text-xl font-extrabold">Product Categories</h3>
          <p className="text-slate-500 text-sm">Organize your inventory into groups</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div className="lg:col-span-4">
          <div className="rounded-xl bg-white shadow-md p-5">
            <h5 className="font-bold mb-5">New Category</h5>

            <form onSubmit={submit}>
              <div className="mb-4">
                <label htmlFor="name" className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Category Name</label>
                <input
                  id="name"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:outline-none"
                  placeholder="e.g. Beverages"
                  required
                />
              </div>
              <button type="submit" className="w-full rounded-lg bg-blue-600 py-2.5 font-bold text-white hover:bg-blue-700 transition">
                Create Category
              </button>
            </form>
          </div>
        </div>

        <div className="lg:col-span-8">
          <h5 className="font-bold mb-4">Existing Categories</h5>
          <div className="rounded-xl bg-white shadow-md overflow-hidden">
            <table className="w-full text-left">
              <thead className="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="pl-5 py-3">ID</th>
                  <th className="py-3">Category Name</th>
                  <th className="pr-5 py-3 text-right">Total Products</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {categories.length === 0 ? (
                  <tr>
                    <td colSpan={3} className="text-center py-10 text-slate-500">No categories yet.</td>
                  </tr>
                ) : (
                  categories.map((c) => (
                    <tr key={c.id} className="hover:bg-slate-50">
                      <td className="pl-5 py-3 text-slate-500 text-sm">{c.id}</td>
                      <td className="py-3 font-bold">{c.name}</td>
                      <td className="pr-5 py-3 text-right">
                        <span className="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs">
                          {c.products_count ?? 0} Items
                        </span>
                      </td>
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

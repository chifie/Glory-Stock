import { useCallback, useEffect, useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { api, ApiError } from '../api';
import { useAuth } from '../auth';
import { TZS, type Category, type DashboardStats, type Product } from '../types';

interface DashboardData {
  stats: DashboardStats;
  products: Product[];
  categories: Category[];
}

const emptyForm = { name: '', sku: '', category_id: '', price: '', stock: '' };

export default function Dashboard() {
  const { user } = useAuth();
  const isAdmin = user?.role === 'admin';

  const [data, setData] = useState<DashboardData | null>(null);
  const [search, setSearch] = useState('');
  const [categoryId, setCategoryId] = useState('0');
  const [lowStock, setLowStock] = useState(false);
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Product | null>(null);
  const [form, setForm] = useState(emptyForm);

  const load = useCallback(() => {
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (Number(categoryId) > 0) params.set('category_id', categoryId);
    if (lowStock) params.set('low_stock', '1');

    api.get<DashboardData>(`/dashboard?${params.toString()}`).then(setData).catch(() => {});
  }, [search, categoryId, lowStock]);

  useEffect(() => {
    load();
  }, [load]);

  const openCreate = () => {
    setEditing(null);
    setForm(emptyForm);
    setShowForm(true);
  };

  const openEdit = (p: Product) => {
    setEditing(p);
    setForm({
      name: p.name,
      sku: p.sku,
      category_id: String(p.category_id),
      price: String(p.price),
      stock: String(p.stock),
    });
    setShowForm(true);
  };

  const saveProduct = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      const body = {
        name: form.name,
        sku: form.sku,
        category_id: Number(form.category_id),
        price: Number(form.price),
        stock: Number(form.stock),
      };
      const res = editing
        ? await api.put<{ message: string }>(`/products/${editing.id}`, body)
        : await api.post<{ message: string }>('/products', body);
      setNotice(res.message);
      setShowForm(false);
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Save failed.');
    }
  };

  const destroy = async (p: Product) => {
    if (!window.confirm('Delete this product?')) return;
    setError('');
    try {
      const res = await api.delete<{ message: string }>(`/products/${p.id}`);
      setNotice(res.message);
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Delete failed.');
    }
  };

  if (!data) return <p className="text-slate-500">Loading…</p>;

  const statCards = [
    { label: 'Stock Valuation', value: TZS(data.stats.totalInventoryValue), cls: 'border-blue-500' },
    { label: "Today's Revenue", value: TZS(data.stats.todayRevenue), cls: 'border-emerald-500 text-emerald-600' },
    { label: 'Monthly Expenses', value: TZS(data.stats.monthlyExpenses), cls: 'border-red-500 text-red-600' },
    {
      label: 'Net Profit (MTD)',
      value: TZS(data.stats.netProfit),
      cls: `border-amber-500 ${data.stats.netProfit >= 0 ? 'text-slate-900' : 'text-red-600'}`,
    },
  ];

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

      <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {statCards.map((c) => (
          <div key={c.label} className={`rounded-2xl bg-white border-l-4 ${c.cls} p-5 shadow-sm`}>
            <h6 className="text-xs font-bold text-slate-500 uppercase tracking-wide">{c.label}</h6>
            <h3 className={`text-2xl font-extrabold ${c.cls.includes('text-') ? c.cls.split(' ').find((t) => t.startsWith('text-')) : ''}`}>
              {c.value} <small className="text-base font-normal text-slate-400">TZS</small>
            </h3>
          </div>
        ))}
      </div>

      <div className="rounded-xl bg-white shadow-md mb-6 p-5">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
          <div>
            <label htmlFor="search" className="block text-xs font-bold text-slate-500 mb-1">
              Search Products
            </label>
            <input
              id="search"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
              placeholder="SKU or Name..."
            />
          </div>
          <div>
            <label htmlFor="cat" className="block text-xs font-bold text-slate-500 mb-1">
              Category
            </label>
            <select
              id="cat"
              value={categoryId}
              onChange={(e) => setCategoryId(e.target.value)}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
            >
              <option value="0">All Categories</option>
              {data.categories.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="flex items-center gap-2 font-bold text-red-600 text-sm">
              <input type="checkbox" checked={lowStock} onChange={(e) => setLowStock(e.target.checked)} className="h-4 w-4 accent-red-600" />
              Low Stock Only
            </label>
          </div>
        </div>
      </div>

      <div className="rounded-xl bg-white shadow-md overflow-hidden">
        <div className="flex justify-between items-center border-b border-slate-200 px-5 py-3">
          <h5 className="font-bold">Inventory List</h5>
          {isAdmin && (
            <div className="flex gap-2">
              <Link to="/expenses" className="rounded-lg border border-red-300 px-3 py-1.5 text-sm font-bold text-red-600 hover:bg-red-50 transition">
                💸 Expenses
              </Link>
              <button onClick={openCreate} className="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-bold text-white hover:bg-blue-700 transition">
                ➕ Add New Product
              </button>
            </div>
          )}
        </div>

        <table className="w-full text-left">
          <thead className="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th className="pl-5 py-3">SKU</th>
              <th className="py-3">Product Name</th>
              <th className="py-3">Category</th>
              <th className="py-3">Price</th>
              <th className="py-3">Stock</th>
              <th className="py-3">Status</th>
              <th className="pr-5 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data.products.length === 0 ? (
              <tr>
                <td colSpan={7} className="text-center py-10 text-slate-500">
                  No products matching your search criteria.
                </td>
              </tr>
            ) : (
              data.products.map((p) => (
                <tr key={p.id} className="hover:bg-slate-50">
                  <td className="pl-5 py-3 text-slate-500 text-sm font-bold">{p.sku}</td>
                  <td className="py-3 font-bold">{p.name}</td>
                  <td className="py-3">
                    <span className="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs">
                      {p.category?.name ?? 'Uncategorized'}
                    </span>
                  </td>
                  <td className="py-3 font-bold">{TZS(p.price)}</td>
                  <td className={`py-3 font-bold ${p.stock <= 5 ? 'text-red-600' : ''}`}>{p.stock}</td>
                  <td className="py-3">
                    {p.stock <= 5 ? (
                      <span className="rounded-full border border-red-300 bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800">REORDER</span>
                    ) : (
                      <span className="rounded-full bg-emerald-500 px-2.5 py-0.5 text-xs font-bold text-white">HEALTHY</span>
                    )}
                  </td>
                  <td className="pr-5 py-3 text-right">
                    {isAdmin ? (
                      <>
                        <button onClick={() => openEdit(p)} className="rounded-lg border border-blue-300 px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 transition">
                          Edit
                        </button>
                        <button onClick={() => destroy(p)} className="ml-2 rounded-lg border border-red-300 px-3 py-1 text-sm text-red-600 hover:bg-red-50 transition">
                          Delete
                        </button>
                      </>
                    ) : (
                      <span className="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-400">Read-Only</span>
                    )}
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {showForm && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <div className="w-full max-w-2xl rounded-xl bg-white shadow-xl p-6">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-xl font-extrabold">{editing ? 'Edit Product' : 'Add New Product'}</h3>
              <button onClick={() => setShowForm(false)} className="text-slate-400 hover:text-slate-600 text-xl">
                ✕
              </button>
            </div>

            {error && (
              <div className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">❌ {error}</div>
            )}

            <form onSubmit={saveProduct}>
              <div className="mb-4">
                <label className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Product Name</label>
                <input
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                  className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">SKU / Code</label>
                  <input
                    value={form.sku}
                    onChange={(e) => setForm({ ...form, sku: e.target.value })}
                    className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                    required
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Category</label>
                  <select
                    value={form.category_id}
                    onChange={(e) => setForm({ ...form, category_id: e.target.value })}
                    className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                    required
                  >
                    <option value="">-- Select Category --</option>
                    {data.categories.map((c) => (
                      <option key={c.id} value={c.id}>
                        {c.name}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                  <label className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Selling Price (TZS)</label>
                  <input
                    type="number"
                    min={1}
                    value={form.price}
                    onChange={(e) => setForm({ ...form, price: e.target.value })}
                    className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                    required
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Stock Quantity</label>
                  <input
                    type="number"
                    min={0}
                    value={form.stock}
                    onChange={(e) => setForm({ ...form, stock: e.target.value })}
                    className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                    required
                  />
                </div>
              </div>

              {editing && (
                <p className="text-slate-500 mt-3 text-xs">
                  * Changing the stock value will create an entry in the <strong>Stock Audit Trail</strong>.
                </p>
              )}

              <div className="flex gap-3 mt-6">
                <button type="button" onClick={() => setShowForm(false)} className="flex-1 rounded-lg bg-slate-100 py-2.5 font-bold text-slate-700 hover:bg-slate-200 transition">
                  Cancel
                </button>
                <button type="submit" className="flex-1 rounded-lg bg-slate-900 py-2.5 font-bold uppercase text-white hover:bg-slate-800 transition">
                  {editing ? 'Save Changes' : 'Save Product'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

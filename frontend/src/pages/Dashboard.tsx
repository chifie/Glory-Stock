import { useCallback, useEffect, useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { AlertTriangle, Package, Plus, Receipt, RefreshCw, Search, X } from 'lucide-react';
import { api, ApiError } from '../api';
import { useAuth } from '../auth';
import { TZS, type Category, type Product } from '../types';
import {
  Alert,
  Badge,
  Button,
  Card,
  CardHeader,
  EmptyState,
  Field,
  MetricCard,
  TableSkeleton,
  inputCls,
  type MetricTone,
} from '../components/ui';

interface DashboardData {
  stats: {
    totalInventoryValue: number;
    todayRevenue: number;
    monthlyExpenses: number;
    monthlyRevenue: number;
    netProfit: number;
  };
  products: Product[];
  categories: Category[];
}

/** Matches the backend's `glorystock.low_stock_threshold` (config/glorystock.php). */
const LOW_STOCK_THRESHOLD = 5;

const emptyForm = { name: '', sku: '', category_id: '', price: '', stock: '' };

export default function Dashboard() {
  const { user } = useAuth();
  const isAdmin = user?.role === 'admin';
  const navigate = useNavigate();

  const [data, setData] = useState<DashboardData | null>(null);
  const [loadError, setLoadError] = useState(false);
  const [searchInput, setSearchInput] = useState('');
  const [search, setSearch] = useState('');
  const [categoryId, setCategoryId] = useState('0');
  const [lowStock, setLowStock] = useState(false);
  const [notice, setNotice] = useState('');
  const [actionError, setActionError] = useState('');

  const [lowItems, setLowItems] = useState<Product[] | null>(null);

  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Product | null>(null);
  const [form, setForm] = useState(emptyForm);
  const [formError, setFormError] = useState('');
  const [saving, setSaving] = useState(false);

  const hasFilters = search !== '' || categoryId !== '0' || lowStock;

  // Debounce typing so we don't hit the API on every keystroke.
  useEffect(() => {
    const t = setTimeout(() => setSearch(searchInput.trim()), 300);
    return () => clearTimeout(t);
  }, [searchInput]);

  const load = useCallback(() => {
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (Number(categoryId) > 0) params.set('category_id', categoryId);
    if (lowStock) params.set('low_stock', '1');

    api
      .get<DashboardData>(`/dashboard?${params.toString()}`)
      .then((d) => {
        setData(d);
        setLoadError(false);
      })
      .catch(() => setLoadError(true));
  }, [search, categoryId, lowStock]);

  useEffect(() => {
    load();
  }, [load]);

  // Low-stock alerts for the warning strip (real data from /reorder).
  useEffect(() => {
    api
      .get<{ items: Product[] }>('/reorder')
      .then((d) => setLowItems(d.items))
      .catch(() => setLowItems([]));
  }, []);

  // Close the product dialog with Escape.
  useEffect(() => {
    if (!showForm) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setShowForm(false);
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [showForm]);

  const clearFilters = () => {
    setSearchInput('');
    setSearch('');
    setCategoryId('0');
    setLowStock(false);
  };

  const openCreate = () => {
    setEditing(null);
    setForm(emptyForm);
    setFormError('');
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
    setFormError('');
    setShowForm(true);
  };

  const saveProduct = async (e: FormEvent) => {
    e.preventDefault();
    setFormError('');
    setSaving(true);
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
      setFormError(err instanceof ApiError ? err.firstError() : 'Save failed.');
    } finally {
      setSaving(false);
    }
  };

  const destroy = async (p: Product) => {
    if (!window.confirm(`Delete "${p.name}"? This cannot be undone.`)) return;
    try {
      const res = await api.delete<{ message: string }>(`/products/${p.id}`);
      setNotice(res.message);
      load();
    } catch (err) {
      setActionError(err instanceof ApiError ? err.firstError() : 'Delete failed.');
    }
  };

  const metrics:
    | { label: string; value: string; tone: MetricTone; hint?: string }[]
    | null = data
    ? [
        { label: 'Stock Valuation', value: TZS(data.stats.totalInventoryValue), tone: 'brand' },
        { label: "Today's Revenue", value: TZS(data.stats.todayRevenue), tone: 'success' },
        {
          label: 'Net Profit (MTD)',
          value: TZS(data.stats.netProfit),
          tone: data.stats.netProfit >= 0 ? 'warning' : 'danger',
          hint: `${TZS(data.stats.monthlyRevenue)} revenue this month`,
        },
        { label: 'Monthly Expenses', value: TZS(data.stats.monthlyExpenses), tone: 'danger' },
      ]
    : null;

  return (
    <div>
      {notice && (
        <Alert tone="success" onDismiss={() => setNotice('')}>
          {notice}
        </Alert>
      )}
      {actionError && (
        <Alert tone="error" onDismiss={() => setActionError('')}>
          {actionError}
        </Alert>
      )}

      {/* Low-stock alerts — real data from the /reorder endpoint */}
      {lowItems && lowItems.length > 0 && (
        <Card className="mb-6 flex flex-col gap-3 border-l-4 border-l-amber-500 bg-amber-50/60 p-4 sm:flex-row sm:items-center sm:justify-between">
          <div className="flex items-start gap-3">
            <span className="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
              <AlertTriangle className="h-4 w-4" aria-hidden />
            </span>
            <div>
              <p className="text-sm font-bold text-amber-900">
                {lowItems.length} product{lowItems.length === 1 ? ' is' : 's are'} running low
              </p>
              <p className="mt-0.5 text-xs text-amber-800">
                At or below {LOW_STOCK_THRESHOLD} units — restock before you run out.
              </p>
            </div>
          </div>
          <Button variant="secondary" size="sm" onClick={() => navigate('/reorder')}>
            View reorder sheet
          </Button>
        </Card>
      )}

      {/* KPI cards */}
      {metrics ? (
        <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {metrics.map((m) => (
            <MetricCard key={m.label} label={m.label} value={m.value} tone={m.tone} hint={m.hint} />
          ))}
        </div>
      ) : loadError ? (
        <Card className="mb-6">
          <EmptyState
            icon={RefreshCw}
            title="Couldn't load the dashboard"
            message="We couldn't reach the server. Check your connection and try again."
            action={
              <Button icon={RefreshCw} onClick={load}>
                Retry
              </Button>
            }
          />
        </Card>
      ) : (
        <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {[0, 1, 2, 3].map((i) => (
            <div key={i} className="animate-pulse rounded-card border border-slate-200 bg-white p-5">
              <div className="h-3 w-28 rounded bg-slate-200" />
              <div className="mt-3 h-7 w-36 rounded bg-slate-200" />
            </div>
          ))}
        </div>
      )}

      {loadError && data && (
        <Alert tone="error">
          Couldn't refresh the latest data.{' '}
          <button type="button" onClick={load} className="font-bold underline">
            Try again
          </button>
        </Alert>
      )}

      {/* Filters */}
      <Card className="mb-6 p-5">
        <div className="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div className="lg:col-span-2">
            <Field label="Search products" htmlFor="search">
              <div className="relative">
                <Search
                  className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400"
                  aria-hidden
                />
                <input
                  id="search"
                  type="search"
                  value={searchInput}
                  onChange={(e) => setSearchInput(e.target.value)}
                  className={`${inputCls} pl-9`}
                  placeholder="SKU or product name…"
                />
              </div>
            </Field>
          </div>

          <Field label="Category" htmlFor="cat">
            <select
              id="cat"
              value={categoryId}
              onChange={(e) => setCategoryId(e.target.value)}
              className={inputCls}
            >
              <option value="0">All categories</option>
              {(data?.categories ?? []).map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>
          </Field>

          <div className="flex items-center justify-between gap-3">
            <label className="flex cursor-pointer items-center gap-2 text-sm font-semibold text-red-600">
              <input
                type="checkbox"
                checked={lowStock}
                onChange={(e) => setLowStock(e.target.checked)}
                className="h-4 w-4 accent-red-600"
              />
              Low stock only
            </label>
            {hasFilters && (
              <Button variant="ghost" size="sm" onClick={clearFilters}>
                Clear
              </Button>
            )}
          </div>
        </div>
      </Card>

      {/* Inventory table */}
      <Card className="overflow-hidden">
        <CardHeader
          title={`Inventory${data ? ` · ${data.products.length} product${data.products.length === 1 ? '' : 's'}` : ''}`}
          action={
            isAdmin ? (
              <div className="flex items-center gap-2">
                <Button
                  variant="danger"
                  size="sm"
                  icon={Receipt}
                  onClick={() => navigate('/expenses')}
                >
                  Expenses
                </Button>
                <Button icon={Plus} size="sm" onClick={openCreate}>
                  Add product
                </Button>
              </div>
            ) : undefined
          }
        />

        {!data && !loadError ? (
          <TableSkeleton rows={6} cols={6} />
        ) : data && data.products.length === 0 ? (
          hasFilters ? (
            <EmptyState
              icon={Search}
              title="No products match your filters"
              message="Try a different search term, category, or clear the filters to see everything."
              action={
                <Button variant="secondary" onClick={clearFilters}>
                  Clear filters
                </Button>
              }
            />
          ) : (
            <EmptyState
              icon={Package}
              title="No products yet"
              message={
                isAdmin
                  ? 'Add your first product to start tracking stock and sales.'
                  : 'No products have been added to the catalog yet.'
              }
              action={
                isAdmin ? (
                  <Button icon={Plus} onClick={openCreate}>
                    Add product
                  </Button>
                ) : undefined
              }
            />
          )
        ) : data ? (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[640px] text-left">
              <thead>
                <tr className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-ink-500">
                  <th scope="col" className="px-5 py-3 font-bold">SKU</th>
                  <th scope="col" className="px-3 py-3 font-bold">Product</th>
                  <th scope="col" className="px-3 py-3 font-bold">Category</th>
                  <th scope="col" className="px-3 py-3 text-right font-bold">Price</th>
                  <th scope="col" className="px-3 py-3 text-right font-bold">Stock</th>
                  <th scope="col" className="px-3 py-3 font-bold">Status</th>
                  <th scope="col" className="px-5 py-3 text-right font-bold">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {data.products.map((p) => {
                  const isLow = p.stock <= LOW_STOCK_THRESHOLD;
                  return (
                    <tr key={p.id} className="transition hover:bg-slate-50">
                      <td className="px-5 py-3 text-sm font-bold text-ink-500">{p.sku}</td>
                      <td className="px-3 py-3 text-sm font-semibold text-ink-950">{p.name}</td>
                      <td className="px-3 py-3">
                        <span className="inline-flex rounded-pill bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">
                          {p.category?.name ?? 'Uncategorized'}
                        </span>
                      </td>
                      <td className="px-3 py-3 text-right text-sm font-semibold tabular-nums">
                        {TZS(p.price)}
                      </td>
                      <td
                        className={`px-3 py-3 text-right text-sm font-bold tabular-nums ${
                          isLow ? 'text-red-600' : 'text-ink-950'
                        }`}
                      >
                        {p.stock}
                      </td>
                      <td className="px-3 py-3">
                        {isLow ? <Badge tone="danger">Reorder</Badge> : <Badge tone="success">Healthy</Badge>}
                      </td>
                      <td className="px-5 py-3 text-right">
                        {isAdmin ? (
                          <div className="flex justify-end gap-2">
                            <Button variant="secondary" size="sm" onClick={() => openEdit(p)}>
                              Edit
                            </Button>
                            <Button variant="danger" size="sm" onClick={() => destroy(p)}>
                              Delete
                            </Button>
                          </div>
                        ) : (
                          <span className="text-xs text-ink-400">Read-only</span>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        ) : null}
      </Card>

      {/* Product dialog */}
      {showForm && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
          onClick={(e) => {
            if (e.target === e.currentTarget) setShowForm(false);
          }}
        >
          <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="product-dialog-title"
            className="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-card bg-white p-6 shadow-panel"
          >
            <div className="mb-5 flex items-center justify-between">
              <h2 id="product-dialog-title" className="text-section font-extrabold text-ink-950">
                {editing ? 'Edit product' : 'Add new product'}
              </h2>
              <button
                type="button"
                onClick={() => setShowForm(false)}
                aria-label="Close dialog"
                className="rounded-lg p-1.5 text-ink-400 transition hover:bg-slate-100 hover:text-ink-950"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            {formError && <Alert tone="error">{formError}</Alert>}

            <form onSubmit={saveProduct}>
              <div className="space-y-4">
                <Field label="Product name" htmlFor="p-name">
                  <input
                    id="p-name"
                    value={form.name}
                    onChange={(e) => setForm({ ...form, name: e.target.value })}
                    className={inputCls}
                    placeholder="e.g. Coca Cola 500ml"
                    required
                    autoFocus
                  />
                </Field>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <Field label="SKU / Code" htmlFor="p-sku">
                    <input
                      id="p-sku"
                      value={form.sku}
                      onChange={(e) => setForm({ ...form, sku: e.target.value })}
                      className={inputCls}
                      placeholder="e.g. COKE-001"
                      required
                    />
                  </Field>
                  <Field label="Category" htmlFor="p-category">
                    <select
                      id="p-category"
                      value={form.category_id}
                      onChange={(e) => setForm({ ...form, category_id: e.target.value })}
                      className={inputCls}
                      required
                    >
                      <option value="">-- Select category --</option>
                      {(data?.categories ?? []).map((c) => (
                        <option key={c.id} value={c.id}>
                          {c.name}
                        </option>
                      ))}
                    </select>
                  </Field>
                </div>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <Field
                    label="Selling price (TZS)"
                    htmlFor="p-price"
                    hint="Whole shillings, no decimals."
                  >
                    <input
                      id="p-price"
                      type="number"
                      min={1}
                      value={form.price}
                      onChange={(e) => setForm({ ...form, price: e.target.value })}
                      className={inputCls}
                      required
                    />
                  </Field>
                  <Field label="Stock quantity" htmlFor="p-stock">
                    <input
                      id="p-stock"
                      type="number"
                      min={0}
                      value={form.stock}
                      onChange={(e) => setForm({ ...form, stock: e.target.value })}
                      className={inputCls}
                      required
                    />
                  </Field>
                </div>

                {editing && (
                  <p className="text-xs text-ink-500">
                    Changing the stock value writes an entry to the stock audit trail.
                  </p>
                )}
              </div>

              <div className="mt-6 flex gap-3">
                <Button
                  type="button"
                  variant="secondary"
                  className="flex-1"
                  onClick={() => setShowForm(false)}
                >
                  Cancel
                </Button>
                <Button type="submit" className="flex-1" loading={saving}>
                  {editing ? 'Save changes' : 'Save product'}
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}

import { useEffect, useState, type FormEvent } from 'react';
import { Link } from 'react-router-dom';
import { ClipboardList, Receipt as ReceiptIcon, ScrollText, ShoppingBag, CheckCircle2 } from 'lucide-react';
import { api, ApiError } from '../api';
import { TZS, type Product, type RecentSale } from '../types';
import {
  Alert,
  Button,
  Card,
  CardHeader,
  EmptyState,
  Field,
  Skeleton,
  inputCls,
} from '../components/ui';

interface PosData {
  products: Product[];
  recentSales: RecentSale[];
}

interface SaleResult {
  message: string;
  receipt_id: number;
  amount: number;
}

export default function Pos() {
  const [data, setData] = useState<PosData | null>(null);
  const [result, setResult] = useState<SaleResult | null>(null);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const [productId, setProductId] = useState('');
  const [quantity, setQuantity] = useState(1);

  const load = () => api.get<PosData>('/pos').then(setData).catch(() => {});

  useEffect(() => {
    load();
  }, []);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setBusy(true);
    try {
      const res = await api.post<SaleResult>('/pos', {
        product_id: Number(productId),
        quantity: Number(quantity),
      });
      setResult(res);
      setProductId('');
      setQuantity(1);
      load();
    } catch (err) {
      setError(err instanceof ApiError ? err.firstError() : 'Sale failed. Try again.');
    } finally {
      setBusy(false);
    }
  };

  if (!data) {
    return (
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div className="lg:col-span-5">
          <div className="animate-pulse rounded-card border border-slate-200 bg-white p-5">
            <Skeleton className="mb-5 h-4 w-24" />
            <Skeleton className="mb-4 h-11 w-full" />
            <Skeleton className="mb-5 h-11 w-full" />
            <Skeleton className="h-11 w-full" />
          </div>
        </div>
        <div className="lg:col-span-7">
          <div className="animate-pulse rounded-card border border-slate-200 bg-white p-5">
            <Skeleton className="mb-4 h-4 w-32" />
            {[0, 1, 2, 3].map((i) => (
              <Skeleton key={i} className="mb-3 h-4 w-full" />
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
      {/* Checkout */}
      <div className="lg:col-span-5">
        {result && (
          <Card className="mb-6 border-l-4 border-l-emerald-500 bg-emerald-50/60 p-5 text-center">
            <CheckCircle2 className="mx-auto h-9 w-9 text-emerald-600" aria-hidden />
            <h2 className="mt-2 text-section font-extrabold text-emerald-900">
              Transaction complete
            </h2>
            <p className="mt-1 text-sm text-emerald-800">
              Paid{' '}
              <span className="font-extrabold tabular-nums text-emerald-950">
                {TZS(result.amount)}
              </span>
            </p>
            <Link
              to={`/receipt/${result.receipt_id}`}
              className="mt-3 inline-flex items-center gap-2 rounded-control bg-white px-4 py-2 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-600/30 transition hover:bg-emerald-100"
            >
              <ReceiptIcon className="h-4 w-4" aria-hidden />
              View receipt
            </Link>
          </Card>
        )}

        {error && (
          <Alert tone="error" onDismiss={() => setError('')}>
            {error}
          </Alert>
        )}

        <Card className="overflow-hidden">
          <CardHeader title="Checkout" />
          <form onSubmit={submit} className="space-y-4 p-5">
            <Field label="Select item" htmlFor="product">
              <select
                id="product"
                value={productId}
                onChange={(e) => setProductId(e.target.value)}
                className={inputCls}
                required
              >
                <option value="">-- Choose product --</option>
                {data.products.map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.name} — {TZS(p.price)} (Stock: {p.stock})
                  </option>
                ))}
              </select>
            </Field>

            <Field
              label="Quantity"
              htmlFor="qty"
              hint={
                productId
                  ? `In stock: ${
                      data.products.find((p) => String(p.id) === productId)?.stock ?? 0
                    }`
                  : 'Select an item first.'
              }
            >
              <input
                id="qty"
                type="number"
                min={1}
                value={quantity}
                onChange={(e) => setQuantity(Number(e.target.value))}
                className={inputCls}
                required
              />
            </Field>

            <Button
              type="submit"
              className="w-full py-3"
              loading={busy}
              disabled={!productId}
              icon={ShoppingBag}
            >
              {busy ? 'Processing…' : 'Process sale'}
            </Button>
          </form>
        </Card>
      </div>

      {/* Recent sales */}
      <div className="lg:col-span-7">
        <Card className="overflow-hidden">
          <CardHeader
            title="Recent sales"
            action={
              <Link
                to="/sales-history"
                className="text-sm font-semibold text-brand-700 transition hover:underline"
              >
                View all
              </Link>
            }
          />

          {data.recentSales.length === 0 ? (
            <EmptyState
              icon={ShoppingBag}
              title="No sales yet today"
              message="Completed sales will appear here with a link to each receipt."
            />
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full min-w-[480px] text-left">
                <thead>
                  <tr className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-ink-500">
                    <th scope="col" className="px-5 py-3 font-bold">Time</th>
                    <th scope="col" className="px-3 py-3 font-bold">Item</th>
                    <th scope="col" className="px-3 py-3 font-bold">Qty</th>
                    <th scope="col" className="px-3 py-3 text-right font-bold">Total</th>
                    <th scope="col" className="px-5 py-3 text-right font-bold">Receipt</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {data.recentSales.map((s) => (
                    <tr key={s.id} className="transition hover:bg-slate-50">
                      <td className="px-5 py-3 text-sm text-ink-500 tabular-nums">
                        {new Date(s.sale_date).toLocaleTimeString([], {
                          hour: '2-digit',
                          minute: '2-digit',
                        })}
                      </td>
                      <td className="px-3 py-3 text-sm font-semibold text-ink-950">
                        {s.product_name}
                      </td>
                      <td className="px-3 py-3">
                        <span className="inline-flex rounded-pill bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                          ×{s.quantity}
                        </span>
                      </td>
                      <td className="px-3 py-3 text-right text-sm font-bold tabular-nums">
                        {TZS(s.total_price)}
                      </td>
                      <td className="px-5 py-3 text-right">
                        <Link
                          to={`/receipt/${s.id}`}
                          className="inline-flex items-center gap-1.5 rounded-pill border border-slate-300 px-3 py-1 text-xs font-semibold text-ink-600 transition hover:bg-slate-900 hover:text-white"
                        >
                          <ReceiptIcon className="h-3 w-3" aria-hidden />
                          View
                        </Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </Card>

        <div className="mt-5 flex flex-wrap gap-3">
          <Link
            to="/stock-log"
            className="inline-flex items-center gap-2 rounded-control border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-ink-600 transition hover:bg-slate-50"
          >
            <ScrollText className="h-4 w-4" aria-hidden />
            Stock audit trail
          </Link>
          <Link
            to="/daily-close"
            className="inline-flex items-center gap-2 rounded-control border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-ink-600 transition hover:bg-slate-50"
          >
            <ClipboardList className="h-4 w-4" aria-hidden />
            Daily close
          </Link>
        </div>
      </div>
    </div>
  );
}

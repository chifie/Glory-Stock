export interface User {
  id: number;
  username: string;
  role: 'staff' | 'admin';
}

export interface AuthPayload {
  token: string;
  user: User;
}

export interface Product {
  id: number;
  name: string;
  sku: string;
  price: number | string;
  stock: number;
  category_id: number;
  category?: { id: number; name: string };
}

export interface Category {
  id: number;
  name: string;
  products_count?: number;
}

export interface SaleRow {
  id?: number;
  product_name: string;
  sku?: string;
  quantity: number;
  total_price: number | string;
  sale_date: string;
  cashier_name?: string | null;
}

export interface RecentSale {
  id: number;
  product_name: string;
  quantity: number;
  total_price: number | string;
  sale_date: string;
}

export interface StockLogRow {
  id: number;
  product_name: string;
  sku: string;
  change_qty: number;
  reason: string;
  created_at: string;
}

export interface DashboardStats {
  totalInventoryValue: number;
  todayRevenue: number;
  monthlyExpenses: number;
  monthlyRevenue: number;
  netProfit: number;
}

export interface ReportRow {
  name: string;
  total_sold: number | string;
}

export interface ExpenseRow {
  id: number;
  category: string;
  amount: number | string;
  description: string | null;
  expense_date: string;
}

export interface UserRow {
  id: number;
  username: string;
  role: 'staff' | 'admin';
}

export interface ReceiptData {
  sale: {
    id: number;
    product_name: string;
    quantity: number;
    total_price: number;
    sale_date: string;
    cashier_name: string | null;
  };
  store: { phone: string; city: string };
}

export const TZS = (v: number | string) =>
  `${Number(v).toLocaleString()} /=`;

export const ADMIN_KEY = 'PRO-99-SECURE';

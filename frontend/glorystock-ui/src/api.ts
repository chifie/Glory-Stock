const TOKEN_KEY = 'glorystock_token';
const USER_KEY = 'glorystock_user';

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY);
}

export function getStoredUser(): import('./types').User | null {
  const raw = localStorage.getItem(USER_KEY);
  return raw ? (JSON.parse(raw) as import('./types').User) : null;
}

export function storeSession(token: string, user: import('./types').User): void {
  localStorage.setItem(TOKEN_KEY, token);
  localStorage.setItem(USER_KEY, JSON.stringify(user));
}

export function clearSession(): void {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
}

export class ApiError extends Error {
  status: number;
  errors: Record<string, string[]>;

  constructor(status: number, message: string, errors: Record<string, string[]> = {}) {
    super(message);
    this.status = status;
    this.errors = errors;
  }

  firstError(): string {
    const vals = Object.values(this.errors);
    return vals.length ? vals[0][0] : this.message;
  }
}

async function request<T>(method: string, url: string, body?: unknown): Promise<T> {
  const headers: Record<string, string> = {
    Accept: 'application/json',
  };

  const token = getToken();
  if (token) headers.Authorization = `Bearer ${token}`;

  if (body !== undefined) headers['Content-Type'] = 'application/json';

  const res = await fetch(`/api${url}`, {
    method,
    headers,
    body: body !== undefined ? JSON.stringify(body) : undefined,
  });

  let data: unknown = null;
  try {
    data = await res.json();
  } catch {
    // non-JSON response
  }

  if (!res.ok) {
    const payload = (data ?? {}) as { message?: string; errors?: Record<string, string[]> };
    if (res.status === 401) {
      clearSession();
    }
    throw new ApiError(res.status, payload.message ?? 'Request failed', payload.errors ?? {});
  }

  return data as T;
}

export const api = {
  get: <T>(url: string) => request<T>('GET', url),
  post: <T>(url: string, body?: unknown) => request<T>('POST', url, body),
  put: <T>(url: string, body?: unknown) => request<T>('PUT', url, body),
  delete: <T>(url: string) => request<T>('DELETE', url),
};

import { createContext, useContext, useState, type ReactNode } from 'react';
import { api, clearSession, getStoredUser, getToken, storeSession } from './api';
import type { AuthPayload, User } from './types';

interface AuthContextValue {
  user: User | null;
  login: (username: string, password: string, role: string) => Promise<void>;
  register: (
    username: string,
    password: string,
    role: string,
    adminKey?: string
  ) => Promise<void>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(() =>
    getToken() ? getStoredUser() : null
  );

  const login = async (username: string, password: string, role: string) => {
    const payload = await api.post<AuthPayload>('/login', {
      username,
      password,
      role,
    });
    storeSession(payload.token, payload.user);
    setUser(payload.user);
  };

  const register = async (
    username: string,
    password: string,
    role: string,
    adminKey?: string
  ) => {
    const payload = await api.post<AuthPayload>('/register', {
      username,
      password,
      role,
      admin_key: adminKey,
    });
    storeSession(payload.token, payload.user);
    setUser(payload.user);
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch {
      // token may already be invalid; clear locally regardless
    }
    clearSession();
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider');
  return ctx;
}

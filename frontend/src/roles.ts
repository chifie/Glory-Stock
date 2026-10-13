/**
 * Where each role lands after signing in (and whenever an authed user
 * hits the landing page). Admins get the business overview; staff get
 * the counter they work at every day.
 *
 * Roles mirror the backend's `in:staff,admin` validation — see
 * backend/app/Http/Controllers/Api/AuthController.php.
 */
export function homeForRole(role: string): string {
  return role === 'admin' ? '/dashboard' : '/pos';
}

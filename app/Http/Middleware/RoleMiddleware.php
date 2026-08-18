<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập trước.');
        }

        // 2. Kiểm tra xem vai trò (role) của người dùng có khớp với vai trò được yêu cầu hay không
        if (Auth::user()->role !== $role) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        // 3. Nếu đúng vai trò thì cho phép request đi tiếp
        return $next($request);
    }
}

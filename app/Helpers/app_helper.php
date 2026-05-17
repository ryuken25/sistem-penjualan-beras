<?php

if (!defined('PRICE_STEP_PER_KG')) {
    /**
     * Selisih harga per kg antar kemasan (5 vs 10 vs 25 kg).
     * Base price = harga per kg untuk Beras 25 kg.
     *   harga_25kg = base
     *   harga_10kg = base + PRICE_STEP_PER_KG
     *   harga_5kg  = base + (PRICE_STEP_PER_KG * 2)
     */
    define('PRICE_STEP_PER_KG', 100);
}

if (!function_exists('derive_package_prices')) {
    function derive_package_prices(float $base): array
    {
        $step = (float) PRICE_STEP_PER_KG;

        return [
            5  => $base + ($step * 2),
            10 => $base + $step,
            25 => $base,
        ];
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        if (!session()->get('isLoggedIn')) {
            return null;
        }

        return [
            'id' => session()->get('user_id'),
            'full_name' => session()->get('full_name'),
            'username' => session()->get('username'),
            'role' => session()->get('role'),
        ];
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id(): ?int
    {
        $userId = session()->get('user_id');

        return $userId !== null ? (int) $userId : null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return strtolower((string) session()->get('role')) === 'admin';
    }
}

if (!function_exists('format_rupiah')) {
    function format_rupiah(float|int|string|null $value): string
    {
        $amount = (float) ($value ?? 0);

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_decimal')) {
    function format_decimal(float|int|string|null $value, int $decimals = 2): string
    {
        return number_format((float) ($value ?? 0), $decimals, ',', '.');
    }
}

if (!function_exists('format_kg')) {
    function format_kg(float|int|string|null $value): string
    {
        return format_decimal($value, 2) . ' kg';
    }
}

if (!function_exists('status_badge')) {
    function status_badge(bool|int|string $status, string $activeText = 'Aktif', string $inactiveText = 'Nonaktif'): string
    {
        $isActive = filter_var($status, FILTER_VALIDATE_BOOL) || (string) $status === '1';
        $class = $isActive ? 'success' : 'secondary';
        $text = $isActive ? $activeText : $inactiveText;

        return '<span class="badge text-bg-' . $class . '">' . esc($text) . '</span>';
    }
}

if (!function_exists('role_badge')) {
    function role_badge(string $role): string
    {
        $normalized = strtolower($role);
        $class = $normalized === 'admin' ? 'primary' : 'info';

        return '<span class="badge text-bg-' . $class . '">' . esc(ucfirst($normalized)) . '</span>';
    }
}

if (!function_exists('active_menu')) {
    function active_menu(string $segment, bool $exact = false): string
    {
        $path = trim(service('uri')->getPath(), '/');

        if ($exact) {
            return $path === trim($segment, '/') ? 'active' : '';
        }

        return str_starts_with($path, trim($segment, '/')) ? 'active' : '';
    }
}

if (!function_exists('sale_limit_text')) {
    function sale_limit_text(?array $setting): string
    {
        if ($setting === null || empty($setting['is_enabled'])) {
            return 'Mode pembatasan penjualan tidak aktif.';
        }

        return 'Mode pembatasan aktif dengan maksimum ' . format_kg($setting['max_total_kg'] ?? 0) . ' per transaksi.';
    }
}

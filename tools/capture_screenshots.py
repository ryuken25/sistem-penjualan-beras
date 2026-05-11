from __future__ import annotations

from pathlib import Path
import os
from time import sleep
from urllib.parse import urlsplit, urlunsplit

from playwright.sync_api import TimeoutError as PlaywrightTimeoutError
from playwright.sync_api import sync_playwright


ROOT = Path(__file__).resolve().parent.parent
OUTPUT = ROOT / 'screenshots'
BASE_URL = os.environ.get('APP_CAPTURE_BASE_URL', 'http://127.0.0.1:8081').strip().rstrip('/')


def ensure_output() -> None:
    OUTPUT.mkdir(parents=True, exist_ok=True)


def wait_settle(page) -> None:
    page.wait_for_load_state('domcontentloaded')
    try:
        page.wait_for_load_state('networkidle', timeout=5000)
    except PlaywrightTimeoutError:
        pass
    page.wait_for_timeout(800)


def install_baseurl_rewrite(page) -> None:
    target = urlsplit(BASE_URL)

    def _handler(route, request):
        current = request.url
        if current.startswith('http://127.0.0.1:8080') or current.startswith('http://localhost:8080'):
            parsed = urlsplit(current)
            rewritten = urlunsplit((target.scheme, target.netloc, parsed.path, parsed.query, parsed.fragment))
            route.continue_(url=rewritten)
            return
        route.continue_()

    page.route('**/*', _handler)


def save(page, filename: str) -> None:
    wait_settle(page)
    page.screenshot(path=str(OUTPUT / filename), full_page=True)


def login(page, username: str, password: str) -> None:
    page.goto(f'{BASE_URL}/login', wait_until='domcontentloaded')
    wait_settle(page)
    page.fill('input[name="username"]', username)
    page.fill('input[name="password"]', password)
    page.click('button[type="submit"]')
    try:
        page.wait_for_url('**/dashboard', timeout=20000)
    except PlaywrightTimeoutError as exc:
        raise RuntimeError(f'Login gagal untuk {username}. URL terakhir: {page.url}') from exc
    wait_settle(page)


def logout(page) -> None:
    try:
        page.click('button[type="submit"]')
        page.wait_for_timeout(500)
    except Exception:
        page.goto(f'{BASE_URL}/logout')


def capture_admin(page) -> None:
    login(page, 'admin', 'admin12345')
    save(page, '02_Dashboard_Admin.png')
    for url, filename in [
        ('/admin/users', '03_Kelola_Pengguna.png'),
        ('/admin/products', '04_Kelola_Produk.png'),
        ('/admin/prices', '05_Kelola_Harga.png'),
        ('/admin/templates', '06_Template_Transaksi.png'),
        ('/admin/sale-limit', '07_Mode_Limit.png'),
        ('/admin/reports', '08_Laporan_Penjualan.png'),
        ('/admin/charts', '09_Grafik_Penjualan.png'),
        ('/profile', '10_Profil_Admin.png'),
    ]:
        page.goto(f'{BASE_URL}{url}', wait_until='domcontentloaded')
        save(page, filename)


def capture_employee(page) -> None:
    login(page, 'pegawai', 'pegawai12345')
    save(page, '11_Dashboard_Pegawai.png')
    for url, filename in [
        ('/sales/create', '12_Transaksi_Manual.png'),
        ('/sales/template', '13_Transaksi_Template.png'),
        ('/sales', '14_Riwayat_Transaksi.png'),
    ]:
        page.goto(f'{BASE_URL}{url}', wait_until='domcontentloaded')
        save(page, filename)


def main() -> None:
    ensure_output()
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True)
        guest_context = browser.new_context(viewport={'width': 1600, 'height': 900}, device_scale_factor=1)
        page = guest_context.new_page()
        install_baseurl_rewrite(page)
        page.goto(f'{BASE_URL}/login', wait_until='domcontentloaded')
        save(page, '01_Login.png')
        guest_context.close()

        admin_context = browser.new_context(viewport={'width': 1600, 'height': 900}, device_scale_factor=1)
        page = admin_context.new_page()
        install_baseurl_rewrite(page)
        capture_admin(page)
        admin_context.close()

        employee_context = browser.new_context(viewport={'width': 1600, 'height': 900}, device_scale_factor=1)
        page = employee_context.new_page()
        install_baseurl_rewrite(page)
        capture_employee(page)
        employee_context.close()
        browser.close()
    print('Screenshots captured successfully.')


if __name__ == '__main__':
    main()

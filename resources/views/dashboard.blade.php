<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTSMS API Tester</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 900px;
            margin: 24px auto;
            padding: 0 16px;
            line-height: 1.4;
        }

        h1, h2 {
            margin: 0 0 12px;
        }

        .box {
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin: 8px 0 4px;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 8px;
        }

        button {
            width: auto;
            cursor: pointer;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        pre {
            background: #f7f7f7;
            border: 1px solid #ddd;
            padding: 12px;
            overflow: auto;
            min-height: 140px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .muted {
            color: #666;
            font-size: 14px;
        }

        .nav {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 12px 0 16px;
        }

        .nav a {
            margin-right: 10px;
        }

        .status {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 16px;
            background: #fafafa;
        }

        .help {
            font-size: 13px;
            color: #444;
            margin: 6px 0 10px;
        }

        @media (max-width: 700px) {
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <h1>PTSMS API Tester</h1>
    <p class="muted">By - Dzaki Amri Zaidaan</p>

    <div class="status" id="loginStatus">
        Status: Belum login
    </div>

    <div class="nav">
        Navigasi:
        <a href="#alur">Lihat Alur</a>
        <a href="#login">Step 1 Login</a>
        <a href="#tester">Step 2 Tester Endpoint</a>
        <a href="#contoh">Contoh Cepat</a>
    </div>

    <div class="box" id="alur">
        <h2>Alur Paling Mudah</h2>
        <p>1. Login dulu untuk ambil token.</p>
        <p>2. Pilih endpoint dari dropdown.</p>
        <p>3. Kalau method POST, isi body JSON.</p>
        <p>4. Klik Kirim Request, lihat hasil di bawah.</p>
    </div>

    <div class="box" id="login">
        <h2>1) Login</h2>
        <p class="help">Klik Login dulu. Kalau berhasil, status berubah jadi Sudah login.</p>
        <div class="row">
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" value="test@example.com">
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" type="password" value="password">
            </div>
        </div>
        <button type="button" id="loginBtn">Login</button>
        <button type="button" id="logoutBtn">Logout</button>

        <label for="token">Token</label>
        <textarea id="token" rows="3" readonly placeholder="Token akan muncul di sini setelah login"></textarea>
    </div>

    <div class="box" id="tester">
        <h2>2) Pilih Endpoint</h2>
        <p class="help">Untuk cek pagination, pilih GET /api/products atau GET /api/purchases lalu pakai Prev/Next.</p>

        <label for="endpointSelect">Endpoint</label>
        <select id="endpointSelect">
            <option value="GET|/api/me">GET /api/me</option>
            <option value="GET|/api/products">GET /api/products</option>
            <option value="POST|/api/products">POST /api/products</option>
            <option value="GET|/api/purchases">GET /api/purchases</option>
            <option value="POST|/api/purchases">POST /api/purchases</option>
            <option value="GET|/api/report/purchases?start_date=2026-01-01&end_date=2026-12-31">GET /api/report/purchases</option>
        </select>

        <div class="row">
            <div>
                <label for="method">Method</label>
                <input id="method" type="text" readonly>
            </div>
            <div>
                <label for="path">Path</label>
                <input id="path" type="text" readonly>
            </div>
        </div>

        <label for="body">Body (JSON, untuk POST/PUT/PATCH)</label>
        <textarea id="body" rows="7"></textarea>

        <button type="button" id="sendBtn">Kirim Request</button>

        <div class="row">
            <div>
                <label for="pageInput">Page (untuk list endpoint)</label>
                <input id="pageInput" type="number" min="1" value="1">
            </div>
            <div>
                <label>Pagination</label>
                <button type="button" id="prevPageBtn">Prev Page</button>
                <button type="button" id="nextPageBtn">Next Page</button>
            </div>
        </div>

        <p class="muted" id="pageInfo">Page info: -</p>

        <label for="response">Response</label>
        <pre id="response"></pre>
    </div>

    <div class="box" id="contoh">
        <h2>Contoh Cepat</h2>
        <p>Contoh alur:</p>
        <p>- Login dengan email dan password default.</p>
        <p>- Pilih <strong>GET /api/me</strong> lalu klik Kirim Request.</p>
        <p>- Lanjut pilih <strong>GET /api/products</strong> untuk cek data produk.</p>
    </div>

    <script>
        const endpointSelect = document.getElementById('endpointSelect');
        const methodInput = document.getElementById('method');
        const pathInput = document.getElementById('path');
        const bodyInput = document.getElementById('body');
        const responseBox = document.getElementById('response');
        const loginStatus = document.getElementById('loginStatus');
        const tokenInput = document.getElementById('token');
        const pageInput = document.getElementById('pageInput');
        const pageInfo = document.getElementById('pageInfo');

        let lastPagination = null;

        const bodyTemplates = {
            '/api/products': '{\n  "name": "Produk Baru",\n  "price": 10000\n}',
            '/api/purchases': '{\n  "date": "2026-04-01",\n  "items": [\n    {\n      "product_id": 1,\n      "qty": 2\n    }\n  ]\n}'
        };

        function setSelectionValues() {
            const value = endpointSelect.value;
            const parts = value.split('|');
            const method = parts[0];
            const path = parts[1];

            methodInput.value = method;
            pathInput.value = path;

            const basePath = path.split('?')[0];
            if (['POST', 'PUT', 'PATCH'].includes(method) && bodyTemplates[basePath]) {
                bodyInput.value = bodyTemplates[basePath];
            } else {
                bodyInput.value = '';
            }
        }

        function saveToken(token) {
            localStorage.setItem('ptsms_token', token);
            tokenInput.value = token;
            loginStatus.textContent = 'Status: Sudah login';
        }

        function logError(message) {
            // Keep simple debug log in browser console without showing it in UI.
            console.error('[PTSMS]', message);
        }

        function getToken() {
            return localStorage.getItem('ptsms_token') || '';
        }

        function clearToken() {
            localStorage.removeItem('ptsms_token');
            tokenInput.value = '';
            loginStatus.textContent = 'Status: Belum login';
            alert('Logout berhasil.');
        }

        function explainStatus(status) {
            if (status === 401) return 'Unauthorized: kamu belum login atau token tidak valid/expired.';
            if (status === 403) return 'Forbidden: akses ditolak.';
            if (status === 404) return 'Not Found: endpoint tidak ditemukan.';
            if (status === 405) return 'Method Not Allowed: method tidak sesuai endpoint.';
            if (status === 422) return 'Validation Error: data request belum sesuai aturan.';
            if (status >= 500) return 'Server Error: ada error di backend.';
            return 'Request gagal.';
        }

        function writeResponseMessage(title, detail, extra) {
            let text = `${title}\n${detail}`;
            if (extra) {
                text += `\n\n${extra}`;
            }
            responseBox.textContent = text;
        }

        async function readResponseSafely(res) {
            const raw = await res.text();

            try {
                return {
                    ok: true,
                    data: JSON.parse(raw),
                    raw: raw
                };
            } catch (err) {
                return {
                    ok: false,
                    data: null,
                    raw: raw
                };
            }
        }

        async function login() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!email || !password) {
                alert('Email dan password wajib diisi.');
                logError('Login gagal: email/password kosong.');
                return;
            }

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const parsed = await readResponseSafely(res);

                if (parsed.ok) {
                    responseBox.textContent = JSON.stringify(parsed.data, null, 2);
                } else {
                    responseBox.textContent = 'Server tidak mengembalikan JSON.\n\n' + parsed.raw.slice(0, 500);
                }

                const data = parsed.data;

                if (res.ok && data.success && data.data && data.data.token) {
                    saveToken(data.data.token);
                    alert('Login berhasil.');
                } else {
                    alert('Login gagal. Cek response.');
                    logError('Login gagal: ' + ((data && data.message) || 'response tidak valid'));
                }
            } catch (err) {
                responseBox.textContent = 'Error: ' + err.message;
                logError('Login error: ' + err.message);
            }
        }

        async function sendRequest() {
            const method = methodInput.value;
            let path = pathInput.value;
            const token = getToken();

            if (!token) {
                alert('Belum login. Klik Login dulu sebelum test endpoint.');
                logError('Request dibatalkan: belum login. Endpoint ' + path);
                writeResponseMessage(
                    'Request dibatalkan',
                    'Kamu belum login.',
                    'Silakan login dulu di Step 1, lalu coba request lagi.'
                );
                return;
            }

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };

            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            const options = { method, headers };

            const isListEndpoint = method === 'GET' && (
                path.startsWith('/api/products') || path.startsWith('/api/purchases')
            );

            if (isListEndpoint) {
                const page = parseInt(pageInput.value || '1', 10);
                const safePage = Number.isNaN(page) || page < 1 ? 1 : page;
                path = path.includes('?') ? `${path}&page=${safePage}` : `${path}?page=${safePage}`;
            }

            if (['POST', 'PUT', 'PATCH'].includes(method)) {
                const raw = bodyInput.value.trim();
                if (raw) {
                    try {
                        options.body = JSON.stringify(JSON.parse(raw));
                    } catch (err) {
                        alert('Body JSON tidak valid.');
                        logError('JSON body tidak valid untuk endpoint ' + path);
                        return;
                    }
                }
            }

            try {
                const res = await fetch(path, options);
                const parsed = await readResponseSafely(res);
                const data = parsed.data;

                if (parsed.ok) {
                    responseBox.textContent = JSON.stringify(data, null, 2);
                } else {
                    const nonJsonHint = res.status === 401
                        ? 'Kemungkinan token tidak valid/expired. Coba login ulang.'
                        : explainStatus(res.status);

                    writeResponseMessage(
                        `Response non-JSON (HTTP ${res.status})`,
                        nonJsonHint,
                        parsed.raw.slice(0, 700)
                    );
                    logError(`Response non-JSON (${res.status}) dari ${path}`);
                    pageInfo.textContent = 'Page info: tidak tersedia (response non-JSON).';
                    return;
                }

                if (!res.ok) {
                    const reason = explainStatus(res.status);
                    const backendMsg = data && data.message ? data.message : 'tanpa pesan dari backend';

                    writeResponseMessage(
                        `Request gagal (HTTP ${res.status})`,
                        reason,
                        `Pesan backend: ${backendMsg}`
                    );

                    logError(`Request gagal (${res.status}) ke ${path}: ${backendMsg}`);
                }

                if (data && data.data && typeof data.data === 'object' && data.data.current_page) {
                    lastPagination = {
                        currentPage: data.data.current_page,
                        lastPage: data.data.last_page,
                        total: data.data.total,
                    };

                    pageInput.value = String(data.data.current_page);
                    pageInfo.textContent = `Page info: ${data.data.current_page} / ${data.data.last_page} (total data: ${data.data.total})`;
                } else {
                    lastPagination = null;
                    pageInfo.textContent = 'Page info: endpoint ini bukan response pagination.';
                }
            } catch (err) {
                responseBox.textContent = 'Error: ' + err.message;
                logError('Fetch error ke ' + path + ': ' + err.message);
            }
        }

        function goPrevPage() {
            const current = parseInt(pageInput.value || '1', 10);
            pageInput.value = String(current > 1 ? current - 1 : 1);
            sendRequest();
        }

        function goNextPage() {
            const current = parseInt(pageInput.value || '1', 10);

            if (lastPagination && current >= lastPagination.lastPage) {
                return;
            }

            pageInput.value = String(current + 1);
            sendRequest();
        }

        document.getElementById('loginBtn').addEventListener('click', login);
        document.getElementById('logoutBtn').addEventListener('click', clearToken);
        document.getElementById('sendBtn').addEventListener('click', sendRequest);
        document.getElementById('prevPageBtn').addEventListener('click', goPrevPage);
        document.getElementById('nextPageBtn').addEventListener('click', goNextPage);
        endpointSelect.addEventListener('change', setSelectionValues);

        tokenInput.value = getToken();
        if (tokenInput.value) {
            loginStatus.textContent = 'Status: Sudah login';
        }
        setSelectionValues();
    </script>
</body>
</html>

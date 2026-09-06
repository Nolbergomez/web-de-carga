<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Acceso Administrativo - AEGOPS</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; margin: 0; font-family: 'Inter', sans-serif; }
        .admin-login-wrapper { width: 100%; max-width: 420px; }
        .admin-login-card { background: rgba(30, 41, 59, 0.95); border-radius: 20px; padding: 40px; text-align: center; border: 2px solid #d97706; box-shadow: 0 10px 40px rgba(217, 119, 6, 0.2); }
        .admin-login-card .logo { width: 80px; height: auto; margin-bottom: 20px; border-radius: 10px; }
        .admin-login-card h1 { color: #38bdf8; font-size: 1.5rem; margin-bottom: 25px; text-transform: uppercase; font-weight: 800; }
        .admin-login-card input, .admin-login-card button { width: 100%; padding: 14px; margin: 10px 0; border-radius: 12px; box-sizing: border-box; }
        .admin-login-card input { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-size: 1rem; }
        .admin-login-card input:focus { outline: none; border-color: #ffcc00; }
        .admin-login-card .error-message { color: #ef4444; font-size: 0.9rem; margin: 5px 0; min-height: 1.2em; }
        .admin-login-card .btn-primary { background: #d97706; color: #fff; border: none; font-weight: bold; cursor: pointer; text-transform: uppercase; box-shadow: 0 4px 15px rgba(217,119,6,0.4); transition: all 0.3s; }
        .admin-login-card .btn-primary:hover { background: #b45309; transform: translateY(-2px); }
        .admin-login-card .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .admin-login-card a { color: #38bdf8; text-decoration: none; font-size: 0.85rem; }
        .admin-login-card a:hover { text-decoration: underline; }
        .admin-login-footer { margin-top: 20px; text-align: center; display: flex; justify-content: center; gap: 20px; }
    </style>
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <img src="foto/logo.png" alt="AEGOPS" class="logo">
            <h1>ACCESO ADMINISTRATIVO</h1>
            <input type="password" id="adminToken" placeholder="Ingrese el token de administrador" autocomplete="off">
            <div id="adminTokenError" class="error-message"></div>
            <button id="btnAcceder" onclick="verificarTokenAdmin()" class="btn-primary">ACCEDER</button>
            <div class="admin-login-footer">
                <a href="privacidad.html">Privacidad</a>
                <a href="terminos.html">Términos</a>
            </div>
        </div>
    </div>
    <script>
        let intentosToken = 0;
        const MAX_INTENTOS_TOKEN = 5;
        const btn = document.getElementById('btnAcceder');
        const tokenInput = document.getElementById('adminToken');
        const errorDiv = document.getElementById('adminTokenError');

        tokenInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') verificarTokenAdmin();
        });

        function bloquearToken() {
            btn.disabled = true;
            btn.textContent = 'BLOQUEADO 15 MIN';
            errorDiv.textContent = 'Demasiados intentos. Espere 15 minutos.';
        }

        async function verificarTokenAdmin() {
            if (btn.disabled) return;
            intentosToken++;
            if (intentosToken > MAX_INTENTOS_TOKEN) { bloquearToken(); return; }

            const token = tokenInput.value.trim();
            if (!token) { errorDiv.textContent = 'Ingrese el token'; return; }

            const formData = new FormData();
            formData.append('token', token);

            try {
                const r = await fetch('api/admin-login.php', { method: 'POST', body: formData, credentials: 'include' });
                const d = await r.json();
                if (d.success) {
                    if (d.csrf_token) sessionStorage.setItem('csrf_token', d.csrf_token);
                    window.location.href = 'index.html';
                } else {
                    errorDiv.textContent = d.error || 'Token inválido';
                }
            } catch (e) {
                errorDiv.textContent = 'Error de conexión';
            }
        }
    </script>
</body>
</html>

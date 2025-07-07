<?php
require_once 'database.php';

// === AUTENTICAÇÃO SIMPLES ===
$usuario_correto = 'admin';
$senha_correta = 'admin123';

// Verificar se já está logado
session_start();
$logado = isset($_SESSION['logado']) && $_SESSION['logado'] === true;

// Processar login
if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    if ($_POST['usuario'] === $usuario_correto && $_POST['senha'] === $senha_correta) {
        $_SESSION['logado'] = true;
        $logado = true;
    } else {
        $erro_login = 'Usuário ou senha incorretos';
    }
}

// Processar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: logs.php');
    exit;
}

// Se não estiver logado, mostrar tela de login
if (!$logado) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Dashboard PIX</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color: #3b82f6;
                --primary-dark: #2563eb;
                --danger-color: #ef4444;
                --gray-50: #f9fafb;
                --gray-100: #f3f4f6;
                --gray-600: #4b5563;
                --gray-700: #374151;
                --gray-900: #111827;
                --border-radius: 16px;
                --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            }

            * { margin: 0; padding: 0; box-sizing: border-box; }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            /* Floating shapes background */
            body::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
                background-size: 50px 50px;
                animation: float 20s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }

            .login-container {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 420px;
                padding: 2rem;
            }

            .login-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: var(--border-radius);
                padding: 3rem 2.5rem;
                box-shadow: var(--shadow-xl);
                border: 1px solid rgba(255, 255, 255, 0.2);
                position: relative;
                overflow: hidden;
            }

            .login-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, var(--primary-color), #8b5cf6, #ec4899);
            }

            .login-header {
                text-align: center;
                margin-bottom: 2.5rem;
            }

            .login-header .logo {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                font-size: 2rem;
                color: white;
                box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            }

            .login-header h1 {
                font-size: 1.875rem;
                font-weight: 700;
                color: var(--gray-900);
                margin-bottom: 0.5rem;
            }

            .login-header p {
                color: var(--gray-600);
                font-size: 1rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                color: var(--gray-700);
                font-weight: 500;
                font-size: 0.875rem;
            }

            .input-wrapper {
                position: relative;
            }

            .input-wrapper i {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--gray-600);
                font-size: 1rem;
            }

            .form-group input {
                width: 100%;
                padding: 1rem 1rem 1rem 3rem;
                border: 2px solid rgba(229, 231, 235, 0.8);
                border-radius: 12px;
                font-size: 1rem;
                background: rgba(255, 255, 255, 0.8);
                transition: all 0.3s ease;
                font-family: inherit;
            }

            .form-group input:focus {
                outline: none;
                border-color: var(--primary-color);
                background: white;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-group input::placeholder {
                color: var(--gray-600);
            }

            .login-btn {
                width: 100%;
                padding: 1rem;
                background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            }

            .login-btn:active {
                transform: translateY(0);
            }

            .login-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }

            .login-btn:hover::before {
                left: 100%;
            }

            .error-message {
                margin-top: 1rem;
                padding: 1rem;
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.2);
                border-radius: 8px;
                color: var(--danger-color);
                font-size: 0.875rem;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .security-info {
                margin-top: 2rem;
                padding: 1rem;
                background: rgba(59, 130, 246, 0.05);
                border: 1px solid rgba(59, 130, 246, 0.1);
                border-radius: 8px;
                font-size: 0.75rem;
                color: var(--gray-600);
                text-align: center;
            }

            .floating-elements {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                overflow: hidden;
            }

            .floating-elements::before,
            .floating-elements::after {
                content: '';
                position: absolute;
                width: 200px;
                height: 200px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                animation: float-shapes 15s ease-in-out infinite;
            }

            .floating-elements::before {
                top: 10%;
                right: 10%;
                animation-delay: 0s;
            }

            .floating-elements::after {
                bottom: 10%;
                left: 10%;
                animation-delay: 7s;
            }

            @keyframes float-shapes {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-30px) scale(1.1); }
            }

            @media (max-width: 480px) {
                .login-container { padding: 1rem; }
                .login-card { padding: 2rem 1.5rem; }
                .login-header h1 { font-size: 1.5rem; }
                .login-header .logo { width: 60px; height: 60px; font-size: 1.5rem; }
            }
        </style>
    </head>
    <body>
        <div class="floating-elements"></div>
        
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h1>Dashboard PIX</h1>
                    <p>Entre com suas credenciais para acessar os logs</p>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="usuario">Usuário</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required autocomplete="current-password">
                        </div>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>
                        Entrar no Dashboard
                    </button>

                    <?php if (isset($erro_login)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= $erro_login ?>
                        </div>
                    <?php endif; ?>
                </form>

                <div class="security-info">
                    <i class="fas fa-shield-alt"></i>
                    Acesso seguro e criptografado aos dados de pagamento
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$format = $_GET['format'] ?? 'html';

// Processar ação de limpar dados
if (isset($_GET['action']) && $_GET['action'] === 'clear' && isset($_GET['confirm'])) {
    $db = new SimpleDB();
    $db->clearAll();
    header('Location: logs.php?cleared=1');
    exit;
}

$db = new SimpleDB();
$logs = $db->getAll();

// Ordenar por data mais recente
usort($logs, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

if ($format === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'total' => count($logs),
        'logs' => $logs
    ], JSON_PRETTY_PRINT);
    exit;
}

// Formato HTML (padrão)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Logs de Pagamentos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--gray-700);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .header-left .subtitle {
            color: var(--gray-600);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card .icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }

        .stat-card.total .icon { background: var(--primary-color); }
        .stat-card.approved .icon { background: var(--success-color); }
        .stat-card.pending .icon { background: var(--warning-color); }
        .stat-card.rejected .icon { background: var(--danger-color); }

        .sub-info {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .stat-card .label {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--gray-50);
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.875rem;
        }

        tr:hover {
            background: var(--gray-50);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .badge.approved {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .badge.rejected {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .action-badge.generated {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
        }

        .action-badge.verified {
            background: rgba(147, 51, 234, 0.1);
            color: #7c3aed;
        }

        .action-badge.webhook {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .payment-id {
            font-family: 'Monaco', 'Menlo', monospace;
            background: var(--gray-100);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .amount {
            font-weight: 600;
            color: var(--gray-900);
        }

        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .customer-name {
            font-weight: 500;
            color: var(--gray-900);
        }

        .customer-email {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .refresh-indicator {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            font-size: 0.875rem;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .refresh-indicator.show {
            opacity: 1;
        }

        .refresh-indicator .spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid var(--gray-300);
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-600);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gray-400);
        }

        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .header { padding: 1.5rem; }
            .table-wrapper { font-size: 0.8rem; }
            th, td { padding: 0.75rem 0.5rem; }
            .stats-cards { grid-template-columns: repeat(2, 1fr); }
        }

        .filters-panel {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .filter-group select,
        .filter-group input {
            padding: 0.625rem;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 0.875rem;
            background: white;
            transition: border-color 0.2s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filters-toggle {
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 200px; }
        }

        .filter-active {
            background: rgba(59, 130, 246, 0.1);
            border-color: var(--primary-color);
        }

        .filtered-row {
            background: rgba(255, 251, 235, 0.5);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .pagination button {
            padding: 0.5rem 1rem;
            border: 1px solid var(--gray-300);
            background: white;
            color: var(--gray-700);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pagination button:hover:not(:disabled) {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination .current-page {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-info {
            margin: 0 1rem;
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .charts-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .charts-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .charts-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }

        .charts-panel {
            padding: 2rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .chart-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
            text-align: center;
        }

        .chart-card canvas {
            max-height: 300px;
        }

        @media (max-width: 768px) {
            .filters-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-cards { grid-template-columns: 1fr; }
            .header { flex-direction: column; text-align: center; }
            .actions { justify-content: center; }
            .filters-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-chart-line"></i> Dashboard PIX</h1>
                <div class="subtitle">
                    <i class="fas fa-database"></i>
                    <?= count($logs) ?> registros encontrados
                    <span style="margin-left: 1rem;">
                        <i class="fas fa-clock"></i>
                        Última atualização: <?= date('H:i:s') ?>
                    </span>
                </div>
            </div>
            <div class="actions">
                <a href="?format=json" class="btn btn-primary">
                    <i class="fas fa-download"></i> Exportar JSON
                </a>
                <button class="btn btn-primary" onclick="exportCSV()">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </button>
                <button class="btn btn-danger" onclick="clearAllData()">
                    <i class="fas fa-trash"></i> Limpar Tudo
                </button>
                <a href="?logout=1" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if (isset($_GET['cleared'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #059669; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                <i class="fas fa-check-circle"></i> Todos os registros foram removidos com sucesso!
            </div>
        <?php endif; ?>

        <?php
        // Calcular estatísticas
        $total = count($logs);
        $approved = count(array_filter($logs, fn($log) => $log['status'] === 'APPROVED'));
        $pending = count(array_filter($logs, fn($log) => $log['status'] === 'PENDING'));
        $rejected = count(array_filter($logs, fn($log) => strtoupper($log['status'] === 'REJECTED')));
        $totalAmount = array_sum(array_map(fn($log) => $log['amount'] ?? 0, $logs));
        
        // Calcular estatísticas do dia
        $today = date('Y-m-d');
        $logsHoje = array_filter($logs, fn($log) => date('Y-m-d', strtotime($log['created_at'])) === $today);
        $transacoesDiarias = count($logsHoje);
        $approvedHoje = count(array_filter($logsHoje, fn($log) => $log['status'] === 'APPROVED'));
        $volumeHoje = array_sum(array_map(fn($log) => $log['amount'] ?? 0, $logsHoje));
        $faturadoHoje = array_sum(array_map(fn($log) => $log['status'] === 'APPROVED' ? ($log['amount'] ?? 0) : 0, $logsHoje));
        
        // Taxa de aprovação PIX (apenas transações finalizadas)
        $totalFinalizadas = $approved + $rejected;
        $taxaAprovacao = $totalFinalizadas > 0 ? ($approved / $totalFinalizadas) * 100 : 0;
        
        // Volume total gerado (PIX criados)
        $logsGerados = array_filter($logs, fn($log) => strpos($log['action'], 'GENERATED') !== false);
        $volumeGerado = array_sum(array_map(fn($log) => $log['amount'] ?? 0, $logsGerados));
        ?>

        <div class="stats-cards">
            <div class="stat-card total">
                <div class="icon"><i class="fas fa-receipt"></i></div>
                <div class="number"><?= number_format($total) ?></div>
                <div class="label">Total de Transações</div>
            </div>
            <div class="stat-card approved">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="number"><?= number_format($approved) ?></div>
                <div class="label">Aprovadas</div>
            </div>
            <div class="stat-card pending">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="number"><?= number_format($pending) ?></div>
                <div class="label">Pendentes</div>
            </div>
            <div class="stat-card rejected">
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="number">R$ <?= number_format($totalAmount / 100, 2, ',', '.') ?></div>
                <div class="label">Volume Total</div>
            </div>
        </div>

        <!-- Cards adicionais com métricas detalhadas -->
        <div class="stats-cards">
            <div class="stat-card daily">
                <div class="icon" style="background: #8b5cf6;"><i class="fas fa-calendar-day"></i></div>
                <div class="number"><?= number_format($transacoesDiarias) ?></div>
                <div class="label">Transações Hoje</div>
                <div class="sub-info">
                    <span style="color: #10b981; font-weight: 600;"><?= number_format($approvedHoje) ?> aprovadas</span>
                </div>
            </div>
            <div class="stat-card daily-volume">
                <div class="icon" style="background: #f59e0b;"><i class="fas fa-chart-bar"></i></div>
                <div class="number">R$ <?= number_format($volumeHoje / 100, 2, ',', '.') ?></div>
                <div class="label">Volume Hoje</div>
                <div class="sub-info">
                    <span style="color: #059669; font-weight: 600;">R$ <?= number_format($faturadoHoje / 100, 2, ',', '.') ?> faturado</span>
                </div>
            </div>
            <div class="stat-card approval-rate">
                <div class="icon" style="background: #10b981;"><i class="fas fa-percentage"></i></div>
                <div class="number"><?= number_format($taxaAprovacao, 1) ?>%</div>
                <div class="label">Taxa de Aprovação PIX</div>
                <div class="sub-info">
                    <span style="color: #6b7280;"><?= number_format($totalFinalizadas) ?> finalizadas</span>
                </div>
            </div>
            <div class="stat-card generated-volume">
                <div class="icon" style="background: #ec4899;"><i class="fas fa-qrcode"></i></div>
                <div class="number">R$ <?= number_format($volumeGerado / 100, 2, ',', '.') ?></div>
                <div class="label">Volume Gerado</div>
                <div class="sub-info">
                    <span style="color: #6b7280;"><?= count($logsGerados) ?> PIX criados</span>
                </div>
            </div>
        </div>

        <!-- Seção de Gráficos -->
        <div class="charts-container">
            <div class="charts-header">
                <h2><i class="fas fa-chart-area"></i> Análise de Performance</h2>
                <button class="btn btn-primary" onclick="toggleCharts()">
                    <i class="fas fa-chart-bar"></i> <span id="chartsToggleText">Mostrar Gráficos</span>
                </button>
            </div>
            
            <div class="charts-panel" id="chartsPanel" style="display: none;">
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3>Volume de Transações (Últimos 7 dias)</h3>
                        <canvas id="volumeChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3>Distribuição por Status</h3>
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3>Volume por Hora (Hoje)</h3>
                        <canvas id="hourlyChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3>Taxa de Conversão</h3>
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-list"></i> Histórico de Transações
                </div>
                <div class="filters">
                    <button class="btn btn-primary" onclick="toggleFilters()">
                        <i class="fas fa-filter"></i> Filtros
                    </button>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="filters-panel" id="filtersPanel" style="display: none;">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Período:</label>
                        <select id="filterPeriod" onchange="applyFilters()">
                            <option value="">Todos</option>
                            <option value="today">Hoje</option>
                            <option value="week">Esta Semana</option>
                            <option value="month">Este Mês</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="filterStatus" onchange="applyFilters()">
                            <option value="">Todos</option>
                            <option value="APPROVED">Aprovado</option>
                            <option value="PENDING">Pendente</option>
                            <option value="REJECTED">Rejeitado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Ação:</label>
                        <select id="filterAction" onchange="applyFilters()">
                            <option value="">Todas</option>
                            <option value="GENERATED">Gerado</option>
                            <option value="VERIFIED">Verificado</option>
                            <option value="WEBHOOK">Webhook</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Valor Mín (R$):</label>
                        <input type="number" id="filterMinValue" placeholder="0.00" step="0.01" onchange="applyFilters()">
                    </div>
                    <div class="filter-group">
                        <label>Valor Máx (R$):</label>
                        <input type="number" id="filterMaxValue" placeholder="999999.99" step="0.01" onchange="applyFilters()">
                    </div>
                </div>
                <div class="filters-row" id="customDateRow" style="display: none;">
                    <div class="filter-group">
                        <label>Data Início:</label>
                        <input type="date" id="filterStartDate" onchange="applyFilters()">
                    </div>
                    <div class="filter-group">
                        <label>Data Fim:</label>
                        <input type="date" id="filterEndDate" onchange="applyFilters()">
                    </div>
                    <div class="filter-group">
                        <button class="btn btn-danger" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Limpar
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Nenhum registro encontrado</h3>
                    <p>Os logs de transações aparecerão aqui quando houver atividade.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar"></i> Data/Hora</th>
                                <th><i class="fas fa-hashtag"></i> ID Pagamento</th>
                                <th><i class="fas fa-cog"></i> Ação</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                                <th><i class="fas fa-dollar-sign"></i> Valor</th>
                                <th><i class="fas fa-user"></i> Cliente</th>
                                <th><i class="fas fa-credit-card"></i> Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                <td>
                                    <?php if (!empty($log['payment_id'])): ?>
                                        <span class="payment-id"><?= substr($log['payment_id'], 0, 12) ?>...</span>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="action-badge <?= strtolower(explode('_', $log['action'])[0]) ?>">
                                        <?php
                                        $action = $log['action'];
                                        $icon = match(strtolower(explode('_', $action)[0])) {
                                            'generated' => 'fas fa-plus-circle',
                                            'verified' => 'fas fa-search',
                                            'webhook' => 'fas fa-bell',
                                            default => 'fas fa-circle'
                                        };
                                        ?>
                                        <i class="<?= $icon ?>"></i>
                                        <?= $action ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= strtolower($log['status']) ?>">
                                        <?php
                                        $statusIcon = match(strtoupper($log['status'])) {
                                            'APPROVED' => 'fas fa-check',
                                            'PENDING' => 'fas fa-clock',
                                            'REJECTED' => 'fas fa-times',
                                            default => 'fas fa-question'
                                        };
                                        ?>
                                        <i class="<?= $statusIcon ?>"></i>
                                        <?= $log['status'] ?>
                                    </span>
                                </td>
                                <td class="amount">
                                    R$ <?= number_format(($log['amount'] ?? 0) / 100, 2, ',', '.') ?>
                                </td>
                                <td>
                                    <?php if (!empty($log['customer_name']) || !empty($log['customer_email'])): ?>
                                        <div class="customer-info">
                                            <span class="customer-name"><?= $log['customer_name'] ?? 'N/A' ?></span>
                                            <span class="customer-email"><?= $log['customer_email'] ?? '' ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($log['payment_method'])): ?>
                                        <i class="fas fa-credit-card" style="color: var(--gray-400); margin-right: 0.25rem;"></i>
                                        <?= $log['payment_method'] ?>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="pagination" id="pagination">
                    <button onclick="changePage('prev')" id="prevBtn">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    <div class="pagination-info" id="paginationInfo">
                        Página 1 de 1
                    </div>
                    <button onclick="changePage('next')" id="nextBtn">
                        Próximo <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="refresh-indicator" id="refreshIndicator">
        <div class="spinner"></div>
        Atualizando...
    </div>

    <script>
        // Dados dos logs (convertidos do PHP)
        const allLogs = <?= json_encode($logs) ?>;
        let filteredLogs = [...allLogs];
        let currentPage = 1;
        const itemsPerPage = 20;
        let filtersVisible = false;
        let chartsVisible = false;
        let charts = {};

        // Contador regressivo para próxima atualização
        let countdown = 30;
        
        function updateCountdown() {
            if (countdown > 0) {
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                showRefreshIndicator();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }
        
        function showRefreshIndicator() {
            const indicator = document.getElementById('refreshIndicator');
            indicator.classList.add('show');
        }

        // Função para toggle dos filtros
        function toggleFilters() {
            const panel = document.getElementById('filtersPanel');
            filtersVisible = !filtersVisible;
            
            if (filtersVisible) {
                panel.style.display = 'block';
                panel.classList.add('filters-toggle');
            } else {
                panel.style.display = 'none';
            }
        }

        // Função para aplicar filtros
        function applyFilters() {
            const period = document.getElementById('filterPeriod').value;
            const status = document.getElementById('filterStatus').value;
            const action = document.getElementById('filterAction').value;
            const minValue = parseFloat(document.getElementById('filterMinValue').value) || 0;
            const maxValue = parseFloat(document.getElementById('filterMaxValue').value) || Infinity;
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;

            // Mostrar/ocultar campos de data personalizada
            const customRow = document.getElementById('customDateRow');
            if (period === 'custom') {
                customRow.style.display = 'block';
            } else {
                customRow.style.display = 'none';
            }

            filteredLogs = allLogs.filter(log => {
                // Filtro por período
                const logDate = new Date(log.created_at);
                const today = new Date();
                
                if (period === 'today') {
                    const todayStr = today.toISOString().split('T')[0];
                    const logDateStr = logDate.toISOString().split('T')[0];
                    if (todayStr !== logDateStr) return false;
                } else if (period === 'week') {
                    const weekAgo = new Date(today);
                    weekAgo.setDate(today.getDate() - 7);
                    if (logDate < weekAgo) return false;
                } else if (period === 'month') {
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(today.getMonth() - 1);
                    if (logDate < monthAgo) return false;
                } else if (period === 'custom') {
                    if (startDate && logDate < new Date(startDate)) return false;
                    if (endDate && logDate > new Date(endDate + 'T23:59:59')) return false;
                }

                // Filtro por status
                if (status && log.status !== status) return false;

                // Filtro por ação
                if (action && !log.action.includes(action)) return false;

                // Filtro por valor
                const logValue = (log.amount || 0) / 100;
                if (logValue < minValue || logValue > maxValue) return false;

                return true;
            });

            currentPage = 1;
            renderTable();
            updatePagination();
        }

        // Função para limpar filtros
        function clearFilters() {
            document.getElementById('filterPeriod').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterAction').value = '';
            document.getElementById('filterMinValue').value = '';
            document.getElementById('filterMaxValue').value = '';
            document.getElementById('filterStartDate').value = '';
            document.getElementById('filterEndDate').value = '';
            document.getElementById('customDateRow').style.display = 'none';
            
            filteredLogs = [...allLogs];
            currentPage = 1;
            renderTable();
            updatePagination();
        }

        // Função para renderizar tabela
        function renderTable() {
            const tbody = document.querySelector('tbody');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageData = filteredLogs.slice(startIndex, endIndex);

            tbody.innerHTML = '';

            if (pageData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray-600);">
                            <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            Nenhum registro encontrado com os filtros aplicados
                        </td>
                    </tr>
                `;
                return;
            }

            pageData.forEach(log => {
                const row = createTableRow(log);
                tbody.appendChild(row);
            });

            // Reaplicar hover effects
            document.querySelectorAll('tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                    this.style.boxShadow = 'var(--shadow-md)';
                    this.style.transition = 'all 0.2s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = 'none';
                });
            });
        }

        // Função para criar linha da tabela
        function createTableRow(log) {
            const row = document.createElement('tr');
            
            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
            };

            const formatCurrency = (amount) => {
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format((amount || 0) / 100);
            };

            const getStatusIcon = (status) => {
                switch (status.toUpperCase()) {
                    case 'APPROVED': return 'fas fa-check';
                    case 'PENDING': return 'fas fa-clock';
                    case 'REJECTED': return 'fas fa-times';
                    default: return 'fas fa-question';
                }
            };

            const getActionIcon = (action) => {
                const actionType = action.split('_')[0].toLowerCase();
                switch (actionType) {
                    case 'generated': return 'fas fa-plus-circle';
                    case 'verified': return 'fas fa-search';
                    case 'webhook': return 'fas fa-bell';
                    default: return 'fas fa-circle';
                }
            };

            row.innerHTML = `
                <td>${formatDate(log.created_at)}</td>
                <td>${log.payment_id ? `<span class="payment-id">${log.payment_id.substring(0, 12)}...</span>` : '<span style="color: var(--gray-400);">-</span>'}</td>
                <td>
                    <span class="action-badge ${log.action.split('_')[0].toLowerCase()}">
                        <i class="${getActionIcon(log.action)}"></i>
                        ${log.action}
                    </span>
                </td>
                <td>
                    <span class="badge ${log.status.toLowerCase()}">
                        <i class="${getStatusIcon(log.status)}"></i>
                        ${log.status}
                    </span>
                </td>
                <td class="amount">${formatCurrency(log.amount)}</td>
                <td>
                    ${log.customer_name || log.customer_email ? `
                        <div class="customer-info">
                            <span class="customer-name">${log.customer_name || 'N/A'}</span>
                            <span class="customer-email">${log.customer_email || ''}</span>
                        </div>
                    ` : '<span style="color: var(--gray-400);">-</span>'}
                </td>
                <td>
                    ${log.payment_method ? `<i class="fas fa-credit-card" style="color: var(--gray-400); margin-right: 0.25rem;"></i>${log.payment_method}` : '<span style="color: var(--gray-400);">-</span>'}
                </td>
            `;

            return row;
        }

        // Função para mudança de página
        function changePage(direction) {
            const totalPages = Math.ceil(filteredLogs.length / itemsPerPage);
            
            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
            }
            
            renderTable();
            updatePagination();
        }

        // Função para atualizar paginação
        function updatePagination() {
            const totalPages = Math.ceil(filteredLogs.length / itemsPerPage);
            const startItem = (currentPage - 1) * itemsPerPage + 1;
            const endItem = Math.min(currentPage * itemsPerPage, filteredLogs.length);
            
            document.getElementById('paginationInfo').textContent = 
                `Página ${currentPage} de ${totalPages} (${startItem}-${endItem} de ${filteredLogs.length})`;
            
            document.getElementById('prevBtn').disabled = currentPage <= 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;
        }

        // Função para exportar CSV
        function exportCSV() {
            const headers = [
                'Data/Hora',
                'ID Pagamento', 
                'Ação',
                'Status',
                'Valor (R$)',
                'Cliente',
                'Email',
                'CPF',
                'Método'
            ];

            const csvData = filteredLogs.map(log => {
                const formatDate = (dateStr) => {
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
                };

                const formatCurrency = (amount) => {
                    return ((amount || 0) / 100).toFixed(2).replace('.', ',');
                };

                return [
                    formatDate(log.created_at),
                    log.payment_id || '',
                    log.action || '',
                    log.status || '',
                    formatCurrency(log.amount),
                    log.customer_name || '',
                    log.customer_email || '',
                    log.customer_cpf || '',
                    log.payment_method || ''
                ];
            });

            // Criar conteúdo CSV
            let csvContent = headers.join(';') + '\n';
            csvData.forEach(row => {
                csvContent += row.map(field => `"${field}"`).join(';') + '\n';
            });

            // Adicionar BOM para UTF-8
            const BOM = '\uFEFF';
            csvContent = BOM + csvContent;

            // Download do arquivo
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                
                const now = new Date();
                const timestamp = now.toISOString().slice(0, 16).replace('T', '_').replace(':', '-');
                link.setAttribute('download', `logs_pagamentos_${timestamp}.csv`);
                
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        // Função para toggle dos gráficos
        function toggleCharts() {
            const panel = document.getElementById('chartsPanel');
            const toggleText = document.getElementById('chartsToggleText');
            chartsVisible = !chartsVisible;
            
            if (chartsVisible) {
                panel.style.display = 'block';
                toggleText.textContent = 'Ocultar Gráficos';
                setTimeout(() => {
                    initCharts();
                }, 100);
            } else {
                panel.style.display = 'none';
                toggleText.textContent = 'Mostrar Gráficos';
                destroyCharts();
            }
        }

        // Função para destruir gráficos existentes
        function destroyCharts() {
            Object.values(charts).forEach(chart => {
                if (chart) chart.destroy();
            });
            charts = {};
        }

        // Função para inicializar gráficos
        function initCharts() {
            destroyCharts();
            
            // Dados para os gráficos
            const chartData = prepareChartData();
            
            // Gráfico de Volume (Últimos 7 dias)
            charts.volume = new Chart(document.getElementById('volumeChart'), {
                type: 'line',
                data: {
                    labels: chartData.dailyLabels,
                    datasets: [{
                        label: 'Volume (R$)',
                        data: chartData.dailyVolume,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de Status (Pizza)
            charts.status = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: chartData.statusLabels,
                    datasets: [{
                        data: chartData.statusData,
                        backgroundColor: [
                            '#10b981', // Aprovado
                            '#f59e0b', // Pendente  
                            '#ef4444'  // Rejeitado
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Gráfico por Hora (Hoje)
            charts.hourly = new Chart(document.getElementById('hourlyChart'), {
                type: 'bar',
                data: {
                    labels: chartData.hourlyLabels,
                    datasets: [{
                        label: 'Transações',
                        data: chartData.hourlyData,
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderColor: '#8b5cf6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Gráfico de Conversão
            charts.conversion = new Chart(document.getElementById('conversionChart'), {
                type: 'bar',
                data: {
                    labels: ['Gerados', 'Pendentes', 'Aprovados', 'Rejeitados'],
                    datasets: [{
                        data: chartData.conversionData,
                        backgroundColor: [
                            '#3b82f6', // Gerados
                            '#f59e0b', // Pendentes
                            '#10b981', // Aprovados
                            '#ef4444'  // Rejeitados
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Função para preparar dados dos gráficos
        function prepareChartData() {
            const today = new Date();
            const last7Days = [];
            
            // Últimos 7 dias
            for (let i = 6; i >= 0; i--) {
                const date = new Date(today);
                date.setDate(date.getDate() - i);
                last7Days.push(date);
            }

            // Volume diário (últimos 7 dias)
            const dailyLabels = last7Days.map(date => 
                date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })
            );
            
            const dailyVolume = last7Days.map(date => {
                const dayStr = date.toISOString().split('T')[0];
                const dayLogs = allLogs.filter(log => 
                    log.created_at.startsWith(dayStr) && log.status === 'APPROVED'
                );
                return dayLogs.reduce((sum, log) => sum + (log.amount || 0), 0) / 100;
            });

            // Distribuição por status
            const statusCounts = {
                'APPROVED': allLogs.filter(log => log.status === 'APPROVED').length,
                'PENDING': allLogs.filter(log => log.status === 'PENDING').length,
                'REJECTED': allLogs.filter(log => log.status === 'REJECTED').length
            };

            // Volume por hora (hoje)
            const hourlyLabels = [];
            const hourlyData = [];
            const todayStr = today.toISOString().split('T')[0];
            
            for (let hour = 0; hour < 24; hour++) {
                hourlyLabels.push(hour.toString().padStart(2, '0') + ':00');
                const hourLogs = allLogs.filter(log => {
                    if (!log.created_at.startsWith(todayStr)) return false;
                    const logHour = new Date(log.created_at).getHours();
                    return logHour === hour;
                });
                hourlyData.push(hourLogs.length);
            }

            // Dados de conversão
            const generated = allLogs.filter(log => log.action.includes('GENERATED')).length;
            const pending = allLogs.filter(log => log.status === 'PENDING').length;
            const approved = allLogs.filter(log => log.status === 'APPROVED').length;
            const rejected = allLogs.filter(log => log.status === 'REJECTED').length;

            return {
                dailyLabels,
                dailyVolume,
                statusLabels: ['Aprovados', 'Pendentes', 'Rejeitados'],
                statusData: [statusCounts.APPROVED, statusCounts.PENDING, statusCounts.REJECTED],
                hourlyLabels,
                hourlyData,
                conversionData: [generated, pending, approved, rejected]
            };
        }

        // Função para limpar todos os dados
        function clearAllData() {
            const confirmed = confirm('⚠️ ATENÇÃO!\n\nVocê tem certeza que deseja APAGAR TODOS os registros da base de dados?\n\nEsta ação é IRREVERSÍVEL e removerá:\n• Todos os logs de transações\n• Todos os dados de pagamentos\n• Todo o histórico\n\nDigite "CONFIRMAR" para continuar:');
            
            if (confirmed !== null) {
                const confirmText = prompt('Digite "CONFIRMAR" para apagar todos os dados:');
                if (confirmText === 'CONFIRMAR') {
                    window.location.href = '?action=clear&confirm=1';
                } else if (confirmText !== null) {
                    alert('Operação cancelada. Texto de confirmação incorreto.');
                }
            }
        }

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            updatePagination();
            updateCountdown();
        });
    </script>
</body>
</html>
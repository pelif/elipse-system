<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Acesso Negado</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6; /* Light gray background */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #374151; /* Dark gray */
        }
        
        .container {
            text-align: center;
            padding: 48px 32px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            width: 90%;
            color: #374151; /* Dark gray */
        }

        .icon {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }

        .icon svg {
            width: 80px;
            height: 80px;
            fill: none;
            stroke: #4b5563; /* Dark gray */
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .icon svg .keyhole {
            fill: #4b5563; /* Dark gray */
            stroke: none;
        }

        h1 {
            font-size: 3.5rem;
            margin: 0 0 16px 0;
            color: #1f2937; /* Darker gray */
            font-weight: 800;
        }

        p {
            font-size: 1.125rem;
            line-height: 1.6;
            margin: 0 0 32px 0;
            color: #4b5563; /* Dark gray */
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #4b5563; /* Dark gray button base */
            color: #f3f4f6; /* Light gray text */
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(75, 85, 99, 0.4);
        }

        a:hover {
            background-color: #374151; /* Darker gray on hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(75, 85, 99, 0.5);
        }

        a:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <!-- Lock Body -->
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <!-- Lock Shackle -->
                <path d="M7 11V7a5 5 0 0110 0v4"></path>
                <!-- Keyhole -->
                <circle cx="12" cy="15" r="1.5" class="keyhole"></circle>
                <path d="M11.5 16.5h1l-0.3 2h-0.4l-0.3-2z" class="keyhole"></path>
            </svg>
        </div>
        <h1>404</h1>
        <p>Página não encontrada ou seu perfil não tem permissão para acessar esta página.</p>
        <a href="/">Voltar para a página inicial</a>
    </div>
</body>
</html>
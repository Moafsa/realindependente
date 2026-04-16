<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Real Independent</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #22c55e;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #22c55e;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #22c55e;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏆 Bem-vindo ao Real Independent!</h1>
    </div>
    
    <div class="content">
        <p>Olá!</p>
        
        <p>É com grande prazer que damos as boas-vindas ao <strong>{{ $tenant->name }}</strong> ao Real Independent!</p>
        
        <div class="info-box">
            <h3>Seus dados de acesso:</h3>
            <p><strong>URL do seu painel:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
            <p><strong>E-mail:</strong> {{ $adminEmail }}</p>
            <p><strong>Subdomínio:</strong> {{ $subdomain }}</p>
        </div>
        
        <p>Seu clube foi configurado com sucesso e está pronto para uso! Você pode começar a:</p>
        
        <ul>
            <li>Cadastrar seus atletas</li>
            <li>Criar equipes e categorias</li>
            <li>Gerenciar suas finanças</li>
            <li>Personalizar seu site público</li>
            <li>E muito mais!</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Acessar Meu Painel</a>
        </div>
        
        <div class="info-box">
            <h3>📚 Precisa de ajuda?</h3>
            <p>Nossa equipe está pronta para ajudá-lo. Entre em contato através do nosso suporte.</p>
        </div>
        
        <p>Boa sorte com seu clube!</p>
        
        <p>Atenciosamente,<br>
        <strong>Equipe Real Independent</strong></p>
    </div>
    
    <div class="footer">
        <p>Este é um e-mail automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} Real Independent. Todos os direitos reservados.</p>
    </div>
</body>
</html>


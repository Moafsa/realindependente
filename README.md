# Real Independent Club Management System

Sistema completo de gestão para clubes de futebol com funcionalidades de IA, multi-tenancy e integração financeira.

## 🚀 Tecnologias

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS + Blade Templates
- **Database**: PostgreSQL
- **Containerização**: Docker Compose
- **IA**: OpenAI GPT-4
- **Pagamentos**: Asaas
- **Comunicação**: Wuzapi (WhatsApp)

## 📋 Funcionalidades

### 🏢 Gestão Administrativa
- Dashboard moderno e responsivo
- Gestão completa de atletas
- Sistema de equipes e filiais
- Controle financeiro integrado
- Relatórios e analytics

### 🤖 Inteligência Artificial
- Planos de treino personalizados
- Planos nutricionais adaptativos
- Análise de performance dos atletas
- Recomendações inteligentes

### 👥 Portal do Atleta
- Dashboard personalizado
- Acesso a planos de IA
- Acompanhamento de evolução
- Comunicação com treinadores

### 🌐 Site Público
- Site gerado automaticamente
- Loja online integrada
- Sistema de contato
- Informações do clube

### 💰 Sistema Financeiro
- Integração com Asaas
- Cobrança automática
- Relatórios financeiros
- Gestão de pagamentos

## 🛠️ Instalação

### Pré-requisitos
- Docker e Docker Compose
- Git

### 1. Clone o repositório
```bash
git clone <repository-url>
cd realindependente
```

### 2. Configure as variáveis de ambiente
```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:
```env
# Database
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=real_independent_central
DB_USERNAME=postgres
DB_PASSWORD=postgres123

# API Keys
OPENAI_API_KEY=your_openai_api_key
ASAAS_API_KEY=your_asaas_api_key
WUZAPI_API_KEY=your_wuzapi_api_key
```

### 3. Inicie os serviços
```bash
docker-compose up -d
```

### 4. Execute as migrations
```bash
docker-compose exec app php artisan migrate
```

### 5. Acesse a aplicação
- **Site Público**: http://localhost:8000
- **Dashboard Admin**: http://localhost:8000/dashboard
- **Portal do Atleta**: http://localhost:8000/portal

## 📁 Estrutura do Projeto

```
realindependente/
├── app/
│   ├── Http/Controllers/     # Controllers da aplicação
│   ├── Models/              # Modelos Eloquent
│   ├── Services/            # Serviços (IA, Asaas, etc.)
│   └── ...
├── resources/
│   ├── views/               # Templates Blade
│   │   ├── layouts/         # Layouts base
│   │   ├── dashboard/       # Views do dashboard
│   │   ├── portal/         # Views do portal do atleta
│   │   └── site/           # Views do site público
│   └── ...
├── docker/                  # Configurações Docker
├── database/
│   ├── migrations/         # Migrations do banco
│   └── seeders/           # Seeders
└── ...
```

## 🔧 Configuração

### Multi-tenancy
O sistema suporta múltiplos clubes através do pacote `stancl/tenancy`. Cada clube possui:
- Banco de dados próprio
- Domínio personalizado
- Configurações independentes

### Integração com IA
- **OpenAI GPT-4**: Geração de planos personalizados
- **Análise de Performance**: Insights baseados em dados
- **Recomendações**: Sugestões inteligentes

### Sistema Financeiro
- **Asaas**: Processamento de pagamentos
- **Cobrança Automática**: Mensalidades automáticas
- **Relatórios**: Analytics financeiros

## 🚀 Deploy

### Produção
1. Configure as variáveis de ambiente de produção
2. Execute `docker-compose -f docker-compose.prod.yml up -d`
3. Configure o domínio e SSL

### Desenvolvimento
```bash
docker-compose up -d
docker-compose exec app php artisan serve
```

## 📊 Monitoramento

- **Logs**: Laravel Log
- **Performance**: Query optimization
- **Segurança**: Rate limiting, CSP headers
- **Backup**: Automated database backups

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 🆘 Suporte

Para suporte técnico, entre em contato:
- Email: suporte@realindependent.com
- WhatsApp: (11) 99999-9999

## 🔄 Roadmap

- [ ] App mobile para atletas
- [ ] Integração com wearables
- [ ] Sistema de gamificação
- [ ] Analytics avançados
- [ ] Integração com redes sociais

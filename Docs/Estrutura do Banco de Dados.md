Estrutura do Banco de Dados (PostgreSQL)
A arquitetura será multi-tenant. Teremos um banco de dados central para gerenciar os tenants (clubes) e os planos, e cada tenant terá seu próprio banco de dados isolado com o schema abaixo.

Banco de Dados Central (central)
tenants (Clubes)

id (PK, uuid)

name (string) - Nome do clube/escola

subdomain (string, unique) - Ex: corinthians-osascohttps://www.google.com/search?q=.meuclube.com

database_name (string)

asaas_customer_id (string)

plan_id (FK para plans.id)

created_at, updated_at

plans (Planos de Assinatura)

id (PK)

name (string) - Ex: Básico, Profissional, Elite

price_monthly (decimal)

features (jsonb) - Ex: {"max_athletes": 50, "max_branches": 1, "ai_features": false}

domains

id (PK)

domain (string, unique) - Domínio personalizado do cliente

tenant_id (FK para tenants.id)

Banco de Dados do Tenant (tenant_*)
users

id (PK)

name (string)

email (string, unique)

password (string)

role (enum: admin, coach, athlete, guardian) - Papel do usuário no clube

athlete_id (FK para athletes.id, nullable)

created_at, updated_at

branches (Filiais)

id (PK)

name (string) - Ex: Unidade Centro

address (string)

contact_info (jsonb)

athletes (Atletas)

id (PK)

full_name (string)

birth_date (date)

position (string)

profile_picture_url (string)

bio (text)

guardian_name (string, nullable) - Nome do responsável

guardian_contact (string, nullable)

team_id (FK para teams.id)

branch_id (FK para branches.id)

teams (Equipes)

id (PK)

name (string) - Ex: Sub-17, Profissional

category (string)

coach_user_id (FK para users.id)

performance_records (Registros de Desempenho)

id (PK)

athlete_id (FK para athletes.id)

metric (string) - Ex: velocidade_max, passes_certos_percent

value (string)

recorded_at (date)

ai_generated_content (Conteúdo Gerado por IA)

id (PK)

athlete_id (FK para athletes.id)

type (enum: meal_plan, workout_plan)

content (jsonb) - O plano detalhado gerado pela IA

prompt (text) - O prompt usado para gerar o conteúdo

generated_at (timestamp)

products (Produtos da Loja)

id (PK)

name (string) - Ex: Uniforme Oficial, Mensalidade Sub-15

description (text)

price (decimal)

type (enum: physical_product, subscription, service)

stock_quantity (integer, nullable)

orders (Pedidos/Vendas)

id (PK)

user_id (FK para users.id)

total_amount (decimal)

status (enum: pending, paid, shipped, cancelled)

asaas_payment_id (string)

created_at, updated_at

order_items

id (PK)

order_id (FK para orders.id)

product_id (FK para products.id)

quantity (integer)

price (decimal)
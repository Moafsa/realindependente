Plano de Desenvolvimento Detalhado: SaaS de Gestão de Clubes
Este documento é o blueprint completo para a criação da plataforma, servindo como guia para o desenvolvimento passo a passo.

Passo 0: Configuração do Alicerce (Setup do Projeto)
Objetivo: Preparar a estrutura base do projeto, incluindo o sistema de multi-tenancy.

Inicializar o Projeto Laravel:

composer create-project laravel/laravel gestao-clube

Configurar o arquivo .env para conectar com o banco de dados PostgreSQL principal (que chamaremos de central).

Frontend Stack:

Instalar e configurar o Tailwind CSS.

Instalar o Laravel Breeze para um scaffolding de autenticação rápido: composer require laravel/breeze --dev e php artisan breeze:install (selecionando a stack Blade).

Configurar Multi-Tenancy:

Instalar o pacote stancl/tenancyforlaravel: composer require stancl/tenancyforlaravel

Publicar a configuração: php artisan vendor:publish --provider="Stancl\Tenancy\TenancyServiceProvider"

Configurar config/tenancy.php. Definir o central_domains com o domínio principal da sua aplicação (ex: meuclube.app).

Criar as migrations para o banco de dados central (tenants, domains).

Rodar php artisan migrate.

Estrutura de Rotas:

Rotas Centrais (routes/web.php): Rotas de marketing, registro de novos clubes, etc.

Rotas do Tenant (routes/tenant.php): Todas as rotas acessadas pelos subdomínios dos clubes (dashboard, portal do atleta, site público). Elas devem ser protegidas pelo middleware de tenancy.

Passo 1: Aplicação Central - Onde os Clubes Nascem
Objetivo: Criar a "vitrine" do seu SaaS e o processo para um novo clube se cadastrar e se tornar um "tenant".

Tela 1.1: Landing Page (Marketing)
Rota (Central): GET /

Controller: MarketingController@index

UI/Componentes:

Header: Logo, links (Funcionalidades, Preços), Botão "Login", Botão "Cadastre-se Grátis".

Seção Hero: Título forte ("A gestão completa para seu clube de futebol"), subtítulo, e um botão "Começar agora".

Seção de Funcionalidades: Cards ilustrados para cada pilar do sistema (Gestão, Portal do Atleta, Site, IA).

Seção de Preços: Cards para os planos (Básico, Pro, Enterprise) com lista de features e botão de cadastro.

Footer: Links úteis, redes sociais.

Fluxo: Clicar em "Cadastre-se" leva para a Tela 1.2.

Tela 1.2: Registro de Novo Clube (Onboarding)
Rota (Central): GET /register

Controller: TenantRegistrationController@create / store

UI/Componentes:

Um formulário multi-passo para não sobrecarregar o usuário.

Passo 1: Seus Dados: Nome, E-mail, Senha (para o admin do clube).

Passo 2: Dados do Clube: Nome do Clube, Subdomínio (ex: [_____].meuclube.app - com validação de disponibilidade em tempo real).

Passo 3: Escolha do Plano: Selecionar um dos planos (Básico/Pro).

Passo 4: Pagamento: Integração com o checkout do Asaas para pagar a primeira mensalidade da assinatura do SaaS.

Lógica de Backend (store method):

Validar todos os dados dos formulários.

Chamar a API do Asaas para criar um novo customer.

Gerar uma cobrança para a assinatura do plano escolhido.

IMPORTANTE: Armazenar os dados do registro em cache/sessão e aguardar a confirmação do pagamento. Configurar um Webhook no Asaas que apontará para uma rota no seu sistema (ex: /webhooks/asaas).

Quando o webhook de pagamento confirmado for recebido, o sistema irá:

Criar o Tenant no banco central.

Criar seu domínio (subdominio.meuclube.app).

Disparar os jobs que criam o banco de dados do tenant, rodam as migrations e semeiam os dados iniciais.

Criar o User (admin) dentro do banco de dados do novo tenant.

Enviar um e-mail de boas-vindas.

Fluxo: Após o sucesso, o usuário é instruído a acessar seu subdomínio para fazer login.

Passo 2: Aplicação do Tenant - Dashboard de Gestão
Objetivo: Construir o painel administrativo que será a principal ferramenta do dono do clube.

Layout Padrão: Todas as telas abaixo usarão um layout com um menu de navegação lateral fixo e um cabeçalho com o nome do clube, notificações e menu de perfil do usuário.

Middleware: Todas as rotas em routes/tenant.php devem ser protegidas por ['web', 'auth'].

Tela 2.1: Visão Geral (Dashboard)
Rota (Tenant): GET /dashboard

Controller: DashboardController@index

UI/Componentes:

Cards de Métricas: Atletas Ativos, Receita do Mês (via Asaas), Aniversariantes da Semana, Equipes.

Gráfico: Evolução de novos atletas nos últimos 6 meses.

Tabela: Últimos pagamentos de mensalidade recebidos.

Atalhos: Botões para "Adicionar Atleta", "Gerar Cobrança", etc.

Tela 2.2: Gestão de Atletas
Listagem de Atletas:

Rota (Tenant): GET /athletes

Controller: AthleteController@index

UI: Tabela com Nome, Idade, Equipe, Status (Ativo/Inativo). Filtros por equipe e busca por nome. Botão "Adicionar Novo Atleta".

Criação/Edição de Atleta:

Rota (Tenant): GET /athletes/create, POST /athletes, GET /athletes/{id}/edit, PUT /athletes/{id}

Controller: AthleteController

UI: Formulário completo com dados pessoais, foto de perfil, dados de contato dos responsáveis, associação a uma equipe (<select>).

Perfil Detalhado do Atleta:

Rota (Tenant): GET /athletes/{id}

Controller: AthleteController@show

UI: Página com abas:

Aba "Perfil": Todas as informações cadastrais.

Aba "Desempenho": Um gráfico mostrando a evolução das métricas. Uma tabela com o histórico de performance_records. Formulário simples para um treinador adicionar um novo registro (Ex: Métrica: Velocidade (km/h), Valor: 28).

Aba "Financeiro": Histórico de todas as cobranças (mensalidades, produtos) para este atleta, com status (Paga, Pendente, Vencida). Botão "Gerar Cobrança Manual".

Aba "Planos IA": Histórico dos planos de treino/nutrição gerados para o atleta.

Tela 2.3: Gestão de Equipes e Filiais
Equipes:

Rota (Tenant): GET /teams

Controller: TeamController

UI: CRUD simples. Tabela de equipes, com formulário em modal para criar/editar (Nome da Equipe, Categoria, Treinador responsável).

Filiais:

Rota (Tenant): GET /branches

Controller: BranchController

UI: CRUD simples. Tabela de filiais, com formulário em modal para criar/editar (Nome da Unidade, Endereço).

Tela 2.4: Gestão Financeira
Rota (Tenant): GET /financial

Controller: FinancialController@index

UI:

Dashboard financeiro com dados vindos diretamente da API do Asaas.

Gráfico de Receita vs. Despesas (se houver).

Tabela completa de todas as cobranças geradas no tenant.

Funcionalidade Chave: Botão "Gerar Mensalidades em Lote". Abre um modal onde o admin seleciona a(s) equipe(s), o mês de referência e o valor. O sistema então cria uma cobrança no Asaas para cada atleta ativo nas equipes selecionadas.

Tela 2.5: Loja (Gestão de Produtos e Pedidos)
Produtos:

Rota (Tenant): GET /store/products

Controller: ProductController

UI: CRUD para produtos (Nome, Descrição, Preço, Foto, Estoque, Tipo).

Pedidos:

Rota (Tenant): GET /store/orders

Controller: OrderController

UI: Tabela de pedidos feitos através do site público. Ao clicar, exibe os detalhes (cliente, produtos, endereço, status do pagamento).

Passo 3: Aplicação do Tenant - O Site Público Gerado
Objetivo: Criar as páginas públicas que servirão de vitrine para o clube.

Controllers: Usarão os mesmos models da área de gestão, mas apenas para leitura.

Páginas:

GET / (Home): Informações gerais, puxadas de uma tabela site_settings.

GET /teams: Lista de equipes.

GET /teams/{id}: Detalhes da equipe e lista de jogadores.

GET /store: Vitrine da loja.

GET /store/products/{id}: Detalhe do produto.

Checkout: O fluxo do carrinho e checkout deve usar a API do Asaas para processar o pagamento.

Passo 4: Portal do Atleta
Objetivo: Criar a área logada para atletas e responsáveis.

Middleware de Rota: Criar um middleware CheckRole:athlete,guardian para proteger estas rotas.

Login: O login é o mesmo do sistema, mas o redirecionamento pós-login levará para /portal/dashboard se o role for de atleta.

Telas:

Dashboard do Atleta (/portal/dashboard): Resumo com próximos treinos, último plano gerado, avisos.

Minha Evolução (/portal/performance): Gráficos interativos com os dados de performance_records.

Meus Planos IA (/portal/ai-plans): Lista de planos já gerados. Botão "Gerar Novo Plano de Treino/Nutrição". Ao clicar, o backend constrói o prompt com os dados do atleta e chama a AIService. A resposta é exibida na tela e salva no banco.

Passo 5: Serviços de Integração (IA e WhatsApp)
Objetivo: Isolar a lógica de comunicação com serviços externos.

Serviço de IA (app/Services/AIService.php):

Classe com métodos como generateWorkout(Athlete $athlete) e generateNutritionPlan(Athlete $athlete).

Esses métodos serão responsáveis por:

Montar o "prompt" de forma estruturada (ex: "Aja como um preparador físico. Crie um plano de treino para um atleta de {idade} anos, {peso}kg...").

Fazer a chamada HTTP para a API da OpenAI/Gemini.

Tratar a resposta (JSON/texto) e retorná-la de forma organizada.

Salvar o resultado na tabela ai_generated_content.

Serviço de Notificação (app/Services/WuzapiService.php):

Classe com métodos como sendChargeNotification(Charge $charge) e sendTrainingReminder(Event $event).

Fará a chamada para a API da Wuzapi.

Eventos e Listeners do Laravel:

Usar o sistema de eventos do Laravel para desacoplar as ações.

Exemplo: No FinancialController, após gerar uma cobrança, dispare um evento: event(new ChargeGenerated($charge)).

Crie um Listener SendChargeNotificationListener que escuta por ChargeGenerated e, dentro dele, chama o WuzapiService.
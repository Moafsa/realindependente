Módulos e Detalhamento de Funcionalidades
Módulo 1: Core e Onboarding de Clubes
Página de Vendas Principal: Apresentação do SaaS, planos e preços.

Cadastro de Clube: Formulário para o dono do clube se cadastrar, escolher um subdomínio e um plano.

Provisionamento de Tenant: Processo automatizado que:

Cria o registro do tenant no banco central.

Cria o novo banco de dados para o tenant.

Executa as migrações do banco de dados do tenant.

Cria o usuário administrador do clube.

Integração com Asaas: No cadastro, cria o cliente no Asaas e a primeira cobrança da assinatura.

Painel de Super Admin: Um painel (separado) para você gerenciar todos os clubes, planos e assinaturas.

Módulo 2: Dashboard de Gestão do Clube
Visão Geral (Dashboard): Gráficos e números principais: total de atletas, receita do mês (via Asaas), próximos aniversários, etc.

Gerenciamento de Atletas: CRUD (Criar, Ler, Atualizar, Deletar) de atletas. Ficha completa com dados pessoais, de desempenho e histórico.

Gerenciamento de Equipes: CRUD de equipes, associação de atletas e treinadores a uma equipe.

Gerenciamento de Filiais: CRUD de filiais para clubes que possuem múltiplas unidades.

Financeiro:

Integração com o painel do Asaas para visualizar cobranças, status de pagamento dos atletas.

Geração de cobranças (mensalidades) em lote ou individualmente.

Loja Virtual:

CRUD de produtos (uniformes, acessórios, mensalidades).

Gestão de estoque.

Visualização e gestão de pedidos.

Editor do Site: Interface para o admin do clube personalizar o site público (cores, logo, textos "Sobre Nós", etc.).

Módulo 3: Portal do Atleta
Login Seguro: Atleta (ou responsável) acessa com seu e-mail e senha.

Meu Perfil: Visualização dos seus dados, fotos e biografia.

Minha Evolução: Gráficos mostrando o progresso em diferentes métricas de desempenho (dados inseridos pelos treinadores).

Planos de IA:

Plano de Treino: Botão para gerar um plano de treino para fazer em casa. O sistema envia um prompt para a IA (ex: "Gere um plano de treino de 3 dias para um jogador de futebol de 15 anos, 65kg, focado em agilidade, sem necessidade de equipamentos").

Plano Nutricional: Botão para gerar um plano alimentar. O prompt pode ser: "Crie um plano alimentar de 1 dia (café da manhã, almoço, lanche, janta) para um atleta de futebol que treina à noite. Total de 2500 calorias. Para cada refeição, inclua uma estimativa de calorias e gere uma imagem representando o prato."

Comunicação: Mural de recados ou chat simples com o treinador.

Módulo 4: Site Público Gerado
Estrutura: Home, Sobre o Clube, Equipes, Atletas, Loja, Contato.

Home: Destaques, notícias, banner principal.

Equipes: Lista todas as equipes (Sub-15, Sub-17, etc.) e ao clicar, mostra os atletas daquela equipe.

Atletas: Página com perfil público dos atletas (pode ser opcional, configurado pelo clube).

Loja: Vitrine dos produtos cadastrados no dashboard. Integração com checkout de pagamento do Asaas.

Módulo 5: Integrações (Wuzapi e IA)
Wuzapi (WhatsApp):

Notificações Automáticas: Envio de mensagem de WhatsApp para responsáveis quando uma cobrança é gerada ou está perto de vencer.

Lembretes: Envio de lembretes sobre horários de treino ou jogos.

Inteligência Artificial (OpenAI/Gemini):

Backend Service: Criar uma classe/serviço no Laravel que será responsável por se comunicar com a API da IA.

Prompt Engineering: Desenvolver os prompts padrão que serão enviados para a IA, inserindo as variáveis do atleta (idade, peso, objetivo, etc.).

Armazenamento: Salvar os resultados gerados pela IA na tabela ai_generated_content para evitar gerar o mesmo conteúdo repetidamente e para o atleta poder consultar seu histórico.

Geração de Imagens: Para as imagens dos pratos, usar um modelo de geração de imagem como DALL-E 3 (OpenAI) ou Imagen (Google).
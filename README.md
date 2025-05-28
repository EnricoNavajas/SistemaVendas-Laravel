# Sistema de Vendas em Laravel (SistemaVendas-Laravel)

![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![MySQL](https://img.shields.io/badge/MySQL-8.0-00758F?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap)
![jQuery](https://img.shields.io/badge/jQuery-3.x-0769AD?style=for-the-badge&logo=jquery)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

## 📜 Descrição Curta

Um sistema de vendas completo desenvolvido com o framework Laravel (versão 11). Funcionalidades como cadastro de produtos, clientes, funcionários, autenticação de usuários e vendas intuitivas com opções de pagamento à vista ou parcelado.

## ✨ Funcionalidades (Features)

* **Autenticação de Funcionários:** Sistema de login seguro.
* **Gerenciamento de Funcionários (CRUD):** Cadastro, listagem, edição e exclusão de funcionários.
* **Gerenciamento de Clientes (CRUD):** Cadastro, listagem, edição e exclusão de clientes.
* **Gerenciamento de Produtos (CRUD):** Cadastro, listagem, edição e exclusão de produtos com nome e preço.
* **Registro de Vendas:**
    * Interface dinâmica para adicionar múltiplos produtos à venda.
    * Busca de clientes e produtos em tempo real (AJAX).
    * Cálculo automático do total da venda.
    * Seleção de forma de pagamento: "À Vista" ou "Parcelado".
    * Geração e edição de parcelas com datas de vencimento automáticas e valores ajustáveis.
    * Validação para garantir a consistência dos dados da venda e parcelas.
    * Associação da venda a um funcionário e, opcionalmente, a um cliente.
* **Listagem de Vendas:** Histórico de vendas com informações detalhadas como ID, data, cliente, funcionário, valor total e forma de pagamento.
* **Geração de Relatório de Vendas em PDF:** Exportar detalhes das vendas para um arquivo PDF(DomPDF).
* **Persistência de Dados:** Todas as informações são salvas em banco de dados MySQL utilizando as Migrations e Eloquent ORM do Laravel.

## 💻 Tecnologias Utilizadas

* **Backend:** Laravel 11 (PHP 8.1+)
* **Frontend:** Blade Templates, Bootstrap 5, jQuery
* **Banco de Dados:** MySQL
* **Geração de PDF:** (DomPDF)
* **Servidor de Desenvolvimento:** `php artisan serve` (Laravel)
* **Gerenciador de Dependências PHP:** Composer
* **Gerenciador de Pacotes Frontend/Build Tool:** Node.js & NPM

## ⚙️ Pré-requisitos

Antes de começar, garanta que você tem os seguintes softwares instalados na sua máquina:

* PHP >= 8.1 (com extensões `mbstring`, `xml`, `ctype`, `json`, `pdo_mysql`, `tokenizer`, `bcmath`, `fileinfo`, e outras que o Laravel ou pacotes específicos possam requerer)
* Composer (Gerenciador de dependências do PHP)
* Node.js e NPM (Para compilar os assets do frontend)
* Um servidor de banco de dados MySQL (ou MariaDB compatível)
* Git (Para clonar o repositório)

## 🚀 Como Rodar o Projeto (Instalação e Configuração)

Siga os passos abaixo para configurar e executar o projeto em seu ambiente de desenvolvimento:

1.  **Clonar o repositório:**
    Abra seu terminal e execute o comando:
    ```bash
    git clone [https://github.com/EnricoNavajas/SistemaVendas-Laravel.git]
    cd SistemaVendas-Laravel
    ```

2.  **Instalar dependências do PHP (Composer):**
    ```bash
    composer install
    ```

3.  **Instalar dependências do Node.js e compilar assets (NPM):**
    ```bash
    npm install
    npm run dev
    ```
    *(Ou `npm run build` para a versão de produção dos assets)*

4.  **Configurar o arquivo de ambiente (`.env`):**
    Copie o arquivo de exemplo `.env.example` para um novo arquivo chamado `.env`:
    ```bash
    cp .env.example .env
    ```
    Abra o arquivo `.env` e configure as variáveis do banco de dados (DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.) de acordo com seu ambiente MySQL:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=seu_banco_de_dados # Ex: sistemavendas
    DB_USERNAME=seu_usuario_mysql # Ex: root
    DB_PASSWORD=sua_senha_mysql   # Ex: root ou em branco
    ```
    **Importante:** Crie o banco de dados (`seu_banco_de_dados`) no seu MySQL antes de prosseguir, se ele ainda não existir.

5.  **Gerar a chave da aplicação Laravel:**
    ```bash
    php artisan key:generate
    ```

6.  **Rodar as Migrations (criar tabelas no banco):**
    Este comando executará todos os arquivos de migration para criar a estrutura do banco de dados.
    ```bash
    php artisan migrate
    ```

7.  **Criar um Funcionário para Login Inicial (via `php artisan tinker`):**
    Para facilitar o primeiro acesso ao sistema, você pode criar um funcionário administrador diretamente pelo terminal usando o Tinker. Abra o terminal na raiz do projeto e siga os comandos:

    Execute o Tinker:
    ```bash
    php artisan tinker
    ```
    Dentro do ambiente do Tinker, cole e execute as seguintes linhas (uma por vez ou o bloco todo):
    ```php
    use App\Models\Funcionario;
    use Illuminate\Support\Facades\Hash;

    Funcionario::create([
        'nome' => 'Admin Teste',
        'cpf' => '000.000.000-00', // Use um CPF de teste único
        'senha' => Hash::make('senha123'), // A senha será 'senha123'
    ]);

    // Para sair do Tinker, digite:
    exit
    ```
    Isso criará um funcionário com os dados informados (cpf 000.000.000-00 ) e a senha `senha123` para você poder logar.


8.  **Iniciar o servidor de desenvolvimento Laravel:**
    ```bash
    php artisan serve
    ```
    Por padrão, a aplicação estará disponível em `http://127.0.0.1:8000` ou `http://localhost:8000`.

    Acesse a URL no seu navegador. Você deverá ver a tela de login. Use o CPF (`000.000.000-00`) e senha (`senha123`) do funcionário criado para acessar o sistema.

## 🗄️ Estrutura das Migrations (Principais Tabelas)

O sistema utiliza as seguintes tabelas principais, gerenciadas pelas migrations do Laravel:

* **`funcionarios`**: Armazena dados dos funcionários que podem logar e operar o sistema (nome, cpf, senha).
* **`clientes`**: Armazena dados dos clientes (nome, cpf, telefone).
* **`produtos`**: Armazena informações sobre os produtos disponíveis para venda (nome, preço).
* **`vendas`**: Registra cada venda realizada, associando funcionário, cliente (opcional), forma de pagamento e total.
* **`venda_itens`**: Tabela pivot para registrar os produtos e suas quantidades/preços em cada venda.
* **`parcelas`**: Armazena os detalhes de cada parcela para vendas parceladas (número da parcela, valor, data de vencimento).

## 📄 Licença

Este projeto está licenciado sob a Licença MIT. Para visualizar o texto completo da licença, consulte o arquivo `LICENSE` na raiz deste repositório.

👤 Autor / Contato
Autor: Enrico Navajas

GitHub: https://github.com/EnricoNavajas

LinkedIn:https://www.linkedin.com/in/enrico-santos-navajas-538615247/
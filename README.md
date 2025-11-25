## Luminous Gym - Sistema de Gestão de Academia

Este projeto é uma plataforma completa de gerenciamento para academias (Luminous Gym), desenvolvida com uma arquitetura **MVC (Model-View-Controller)** personalizada. O sistema integra controle de alunos, gestão de treinos com execução em tempo real, e um módulo de e-commerce para venda de produtos.

## Funcionalidades

### Módulo de Treino & Alunos
* **Autenticação Segura:** Login e Cadastro com criptografia de senha (hash).
* **Dashboard do Aluno:** Visualização rápida de treinos semanais, frequência e estatísticas.
* **Execução de Treino:** Interface interativa para o aluno realizar o treino, com cronômetro de descanso e checklist de exercícios.
* **Gestão de Planos:** Professores podem montar fichas de treino personalizadas (CRUD).

### Módulo E-commerce (Loja)
* **Catálogo de Produtos:** Visualização de suplementos e equipamentos.
* **Carrinho de Compras:** Gestão de itens na sessão antes da compra.
* **Pedidos:** Registro de vendas com baixa automática de estoque.

### Backend & Arquitetura
* **Router Personalizado:** Sistema de rotas amigáveis (ex: `/dashboard`, `/loja`).
* **Data Mapper Pattern:** Camada de persistência isolada para comunicação com o Banco de Dados.
* **Métodos Mágicos:** Uso de `__get` e `__set` para modelos de dados limpos.
* **API Interna:** Endpoints JSON para comunicação assíncrona com o Frontend via Fetch API.

---

## Tecnologias Utilizadas

* **Backend:** PHP 8+ (Orientado a Objetos, PDO, MVC).
* **Banco de Dados:** MariaDB / MySQL.
* **Frontend:** HTML5, CSS3 (Responsivo), JavaScript (ES6+).
* **Ícones:** Lucide Icons.
* **Servidor:** Apache (Requer `mod_rewrite` via `.htaccess`).

---

## Estrutura do Projeto

O projeto segue uma estrutura MVC estrita para garantir escalabilidade e manutenção:

```text
/Alvaros-Project
├── /config              # Arquivos de configuração (SQL dumps, etc)
├── /public              # Raiz do servidor web (apenas arquivos públicos)
│   ├── /assets          # Imagens e mídias
│   ├── /scripts         # JavaScript (Lógica de execução de treino, API)
│   ├── /styles          # Arquivos CSS
│   ├── .htaccess        # Regras de reescrita de URL
│   └── index.php        # Ponto de entrada único (Entry Point)
├── /src                 # Código fonte da aplicação (Backend)
│   ├── /Controllers     # Controladores (Auth, Dashboard, Shop)
│   ├── /Core            # Núcleo do Framework (Router, Database, Model)
│   ├── /Mappers         # Persistência de dados (SQL Logic)
│   ├── /Models          # Entidades de negócio (Student, Order, Product)
│   └── /Services        # Regras de negócio complexas (CartService)
└── /views               # Telas da aplicação (Arquivos PHP/HTML)
```

## Autores

- [@RenatoSZA](https://www.github.com/RenatoSZA)
- [@StevegitXz](https://www.github.com/StevegitXz)
- [@alineaguiargondim](https://www.github.com/alineaguiargondim)
- [@PedroL-Melo](https://www.github.com/PedroL-Melo)
- [@Maria_Santa22](https://www.github.com/Maria-Santa22)
- [@estherferrari29-collab](https://www.github.com/estherferrari29-collab)
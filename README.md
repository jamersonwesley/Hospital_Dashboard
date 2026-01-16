Para rodar a aplicação necessario substituir no .env por 

DB_CONNECTION=pgsql
SESSION_DRIVER=file

Para rodar esse projeto coloque 
`npm install`
e logo no dashboard_hospital rode
`npm start`
Para instalar esse projeto primeiro rode no example-app
`composer install`
logo em seguida rode
`php artisan migrate`
e para rodar o projeto utilize
`php artisan serve`

Para este teste técnico, optei por utilizar Laravel no backend criando uma aplicação moderna e escalável para o gerenciamento hospitalar e da farmácia.

Por que Laravel?

Laravel oferece uma estrutura organizada e limpa, com roteamento, controllers e Eloquent ORM, o que facilita o acesso e manipulação de dados do banco PostgreSQL.

Ele possui mecanismos nativos de segurança, autenticação e validação de dados, garantindo que a aplicação seja confiável e mantenha a integridade das informações médicas e farmacêuticas.

A facilidade de criar APIs RESTful torna a integração com o frontend muito direta, permitindo retornar dados estruturados para dashboards e gráficos.
Por que React?

React proporciona uma interface dinâmica e interativa, ideal para dashboards onde os dados mudam constantemente, como taxas de ocupação hospitalar, estoque de medicamentos e curvas de consumo.

Com React, é possível criar componentes reutilizáveis, como tabelas e gráficos, mantendo o código organizado e fácil de manter.

A integração com bibliotecas de gráficos (Chart.js) permitiu construir visualizações claras e intuitivas, essenciais para que gestores possam tomar decisões rápidas.
<img width="1538" height="1054" alt="Dashboard Farmacia" src="https://github.com/user-attachments/assets/6deaa5f3-a22c-4ef2-b7e4-ff7506b61ed1" />

<img width="1538" height="1197" alt="Dashboard Financeiro" src="https://github.com/user-attachments/assets/524b2d15-051d-4661-908a-2ad84f46295a" />

<img width="1538" height="1932" alt="Dashboard de Ocupacao" src="https://github.com/user-attachments/assets/1a6cf140-2ddf-480f-b208-a90b8355651f" />

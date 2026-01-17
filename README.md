Para rodar a aplicação necessario substituir no .env por <br/>

DB_CONNECTION=pgsql <br/>
SESSION_DRIVER=file
<br/>

Para rodar esse projeto coloque <br/>
`npm install`<br/>
e logo no dashboard_hospital rode<br/>
`npm start`<br/>
Para instalar esse projeto primeiro rode no example-app<br/>
`composer install`<br/>
logo em seguida rode<br/>
`php artisan migrate`<br/>
e para rodar o projeto utilize<br/>
`php artisan serve`<br/>
<br/>
Para este teste técnico, optei por utilizar Laravel no backend criando uma aplicação moderna e escalável para o gerenciamento hospitalar e da farmácia.
Neste projeto, optei por Laravel no backend e React no frontend. Laravel me permitiu estruturar a aplicação de forma organizada, usando controllers e Eloquent ORM para acessar e manipular os dados do PostgreSQL de maneira segura e eficiente. Com ele, consegui criar APIs RESTful que retornam exatamente os dados que o frontend precisa, garantindo integridade e segurança das informações hospitalares e farmacêuticas.

No frontend, usei React para construir dashboards interativos e dinâmicos. Como os dados mudam constantemente.ocupação de quartos, estoque de medicamentos,curvas de consumo.React facilitou criar componentes reutilizáveis, como tabelas e gráficos, e integrar com Chart.js para visualizações claras.

A arquitetura Laravel + React foi escolhida justamente por separar responsabilidades: o backend cuida da lógica, consultas e segurança, enquanto o frontend foca na experiência do usuário e na apresentação dos dados de forma intuitiva. Essa separação deixou o projeto escalável, fácil de manter e pronto para receber novas funcionalidades rapidamente.
<br/>
<img width="1538" height="1063" alt="Dashboard Farmacia" src="https://github.com/user-attachments/assets/24417ef7-bbac-4dce-9cdb-77e389b76fe6" />
<img width="1538" height="1197" alt="Dashboard Financeiro" src="https://github.com/user-attachments/assets/417de9c4-595c-4bc6-91c4-a7195e9d4eff" />


<img width="1538" height="1932" alt="Dashboard de Ocupacao" src="https://github.com/user-attachments/assets/1a6cf140-2ddf-480f-b208-a90b8355651f" />

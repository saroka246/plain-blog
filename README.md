# Plain Blog

Тестовое задание на PHP, MySQL и Smarty: блог с категориями, сортировкой, пагинацией, счётчиком просмотров и похожими постами.

В Docker используются PHP 8.4, nginx и MariaDB 11.4. Стили написаны на SCSS и собираются через Vite.

## Запуск

Нужны Docker и Docker Compose. Все команды выполняются из корня проекта

1. Скопировать .env.example в .env и заполнить поля

3. Собрать PHP-образ, установить зависимости, подготовить Smarty и запустить приложение:

   ```bash
   docker compose build php
   docker compose run --rm --no-deps --user "$(id -u):$(id -g)" -e COMPOSER_HOME=/tmp/composer php composer install --prefer-dist --no-interaction
   docker compose run --rm --no-deps php sh -c 'mkdir -p var/smarty/compile && chown -R www-data:www-data var/smarty && chmod -R 775 var/smarty'
   docker compose up -d
   docker compose exec php php bin/seed.php
   ```

Схема БД создаётся при первом запуске пустого MariaDB-volume. Сидер запускается вручную; повторный запуск с `--reset` удалит текущие данные и создаст тестовые заново.

- Сайт: http://localhost:8080/
- Adminer: http://localhost:8080/adminer/

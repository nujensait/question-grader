🖥️ Классическая установка (Нативная)

Этот подход предполагает установку PHP, Composer и MySQL непосредственно внутри вашего дистрибутива WSL. Подходит, если вы не хотите использовать Docker .

Шаг 1: Установка необходимого ПО
В терминале Ubuntu выполните команды для установки PHP, Composer, MySQL и дополнительных расширений :

bash
# Обновление списка пакетов
sudo apt update && sudo apt upgrade

# Установка PHP, MySQL и Composer
sudo apt install php php-mysql php-xml php-mbstring php-zip php-curl mysql-server composer
Шаг 2: Создание проекта с помощью Composer
Перейдите в рабочую директорию (например, ~/projects):

bash
mkdir ~/projects
cd ~/projects
Создайте новый проект Laravel:

bash
composer create-project laravel/laravel название-проекта
cd название-проекта
Шаг 3: Настройка базы данных (MySQL)
Войдите в MySQL:

bash
sudo mysql -u root -p
(при запросе пароля нажмите Enter, если пароль еще не установлен) .

Создайте базу данных и пользователя для проекта (выполните внутри mysql>):

sql
CREATE DATABASE имя_базы_данных;
CREATE USER 'имя_пользователя'@'localhost' IDENTIFIED BY 'пароль';
GRANT ALL PRIVILEGES ON имя_базы_данных.* TO 'имя_пользователя'@'localhost';
FLUSH PRIVILEGES;
EXIT;
Скопируйте файл .env.example в .env и отредактируйте настройки базы данных:

bash
cp .env.example .env
nano .env
Замените значения DB_DATABASE, DB_USERNAME и DB_PASSWORD на созданные вами .

Шаг 4: Запуск встроенного сервера
Сгенерируйте ключ приложения и выполните миграции:

bash
php artisan key:generate
php artisan migrate
Запустите встроенный сервер разработки:

bash
php artisan serve
Откройте в браузере http://localhost:8000. Приложение должно работать .

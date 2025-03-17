# 🚀 Инструкция по деплою проекта

## 1. Клонирование репозитория  
```sh
git clone git@github.com:LazyShake/todo_app.git
cd todo_app
```

## 2. Установка зависимостей  
Убедись, что у тебя установлен **PHP**, **Composer**, **Node.js** и **NPM**.  

```sh
composer install
npm install
```

## 3. Настройка окружения  
Скопируй `.env.example` в `.env` и заполни его нужными параметрами.  

```sh
cp .env.example .env
```

Сгенерируй ключ приложения:  

```sh
php artisan key:generate
```

## 4. Настройка базы данных  
Настрой параметры БД в `.env`, затем выполни миграции:  

```sh
php artisan migrate --seed
```

## 5. Запуск сервера  
Для локального запуска используй:  

```sh
php artisan serve
```

Если используется **Docker**, запусти:  

```sh
docker-compose up -d
```

## 6. Компиляция фронтенда  
```sh
npm run build
```

## 7. Деплой на сервер  
Скопируй файлы на сервер и запусти команду:  

```sh
php artisan config:clear && php artisan cache:clear
```

🎉 Теперь приложение развернуто и готово к работе!

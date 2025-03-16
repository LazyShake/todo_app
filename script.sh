#!/bin/bash

API_URL="http://nginx_server/api"
COOKIE_FILE="cookies.txt"

# Данные пользователя (логин и пароль)
EMAIL="alex@example.com"
PASSWORD="xawdfthb"

# Получаем CSRF токен
echo "Получение CSRF токена..."
CSRF_TOKEN=$(curl -s -L -X GET "http://nginx_server/sanctum/csrf-cookie" -c "$COOKIE_FILE" | jq -r '.csrf_token')

# Проверяем, если токен не получен, выводим ошибку
if [ -z "$CSRF_TOKEN" ]; then
    echo "Ошибка: не удалось получить CSRF токен."
    exit 1
fi

echo "CSRF токен получен: $CSRF_TOKEN"

# Получаем токен авторизации с передачей CSRF токена
echo "Получение токена авторизации..."
RESPONSE=$(curl -s -L -X POST "$API_URL/login" \
    -H "Content-Type: application/json" \
    -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
    -b "$COOKIE_FILE" \
    -d "{\"email\": \"$EMAIL\", \"password\": \"$PASSWORD\"}")

echo "Ответ API: $RESPONSE"

# Проверяем, если токен не получен, выводим ошибку
TOKEN=$(echo "$RESPONSE" | jq -r '.api_token')

if [ -z "$TOKEN" ] || [ "$TOKEN" == "null" ]; then
    echo "Ошибка: не удалось получить токен. Ответ сервера:"
    echo "$RESPONSE"
    exit 1
fi

echo "Токен авторизации получен: $TOKEN"

# Получаем список задач
echo "Получение списка задач..."
TASKS=$(curl -s -L -X GET "$API_URL/tasks" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -b "$COOKIE_FILE")
echo "$TASKS" | jq

# Создаем новую задачу с тегами
TASK_TITLE="Новая задача"
TASK_TEXT="Описание новой задачи"
TAGS_JSON='["важное", "срочное"]' # Можно передавать как строки (названия тегов), так и числа (ID)

echo "Создание новой задачи..."
RESPONSE=$(curl -s -L -X POST "$API_URL/tasks" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
    -b "$COOKIE_FILE" \
    -d "{\"title\": \"$TASK_TITLE\", \"text\": \"$TASK_TEXT\", \"tags\": $TAGS_JSON, \"completed\": false}")

echo "Ответ сервера:"
echo "$RESPONSE" | jq

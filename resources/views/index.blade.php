<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Добавим мета-тег с CSRF токеном -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .task-item { 
            cursor: move; 
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .task-item .task-handle {
            cursor: pointer;
            font-size: 18px;
        }
    </style>
</head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Select2 скрипт -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<body>
    <div class="container mt-5">
        <h2>ToDo List</h2>

        <div id="authSection">
            <h4>Вход</h4>
            <input type="email" id="loginEmail" class="form-control" placeholder="Email">
            <input type="password" id="loginPassword" class="form-control mt-2" placeholder="Пароль">
            <button id="loginBtn" class="btn btn-primary mt-2">Войти</button>

            <h4 class="mt-4">Регистрация</h4>
            <input type="text" id="registerName" class="form-control" placeholder="Имя">
            <input type="email" id="registerEmail" class="form-control mt-2" placeholder="Email">
            <input type="password" id="registerPassword" class="form-control mt-2" placeholder="Пароль">
            <button id="registerBtn" class="btn btn-success mt-2">Зарегистрироваться</button>
        </div>

        <div id="todoSection" style="display: none;">
            <button id="logoutBtn" class="btn btn-danger mt-2">Выйти</button>
            <h3 class="mt-4">Добавить задачу</h3>
            <input type="text" id="taskTitle" class="form-control" placeholder="Название">
            <textarea id="taskText" class="form-control mt-2" placeholder="Описание"></textarea>
            
            <select id="taskTags" class="form-control select2" multiple="multiple" style="width: 100%">
                <!-- Теги будут загружаться динамически -->
            </select>

            <button id="addTaskBtn" class="btn btn-success mt-2">Добавить</button>

            <h3 class="mt-4">Список задач</h3>
            <ul id="taskList" class="list-group mt-2">
                <!-- Задачи будут добавляться сюда -->
            </ul>
        </div>
    </div>

    <script src="todo.js"></script>
</body>
</html>

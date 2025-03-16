$(document).ready(function () {
    let apiToken = localStorage.getItem('api_token');

    function getCSRFToken() {
        return $("meta[name='csrf-token']").attr("content");
    }

    function getCSRFTokenAndLoadTasks() {
        $.get('/sanctum/csrf-cookie').done(function () {
            if (apiToken) {
                $("#authSection").hide();
                $("#todoSection").show();
                loadTasks();
                loadTags(); // Загружаем теги при старте
            }
        }).fail(function () {
            alert('Ошибка при получении CSRF токена');
        });
    }

    getCSRFTokenAndLoadTasks();

    $("#loginBtn").click(function () {
        let email = $("#loginEmail").val();
        let password = $("#loginPassword").val();

        $.ajax({
            url: '/api/login',
            method: 'POST',
            data: { email, password },
            headers: { 'X-CSRF-TOKEN': getCSRFToken() },
            success: function (data) {
                localStorage.setItem('api_token', data.api_token);
                $("#authSection").hide();
                $("#todoSection").show();
                loadTasks();
                loadTags();
            },
            error: function (xhr) {
                alert("Ошибка входа: " + xhr.responseText);
            }
        });
    });

    $("#registerBtn").click(function () {
        let name = $("#registerName").val();
        let email = $("#registerEmail").val();
        let password = $("#registerPassword").val();

        $.ajax({
            url: '/api/register',
            method: 'POST',
            data: { name, email, password },
            headers: { 'X-CSRF-TOKEN': getCSRFToken() },
            success: function (data) {
                localStorage.setItem('api_token', data.api_token);
                $("#authSection").hide();
                $("#todoSection").show();
                loadTasks();
                loadTags();
            },
            error: function (xhr) {
                alert("Ошибка регистрации: " + xhr.responseText);
            }
        });
    });

    $("#logoutBtn").click(function () {
        $.ajax({
            url: '/api/logout',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'X-CSRF-TOKEN': getCSRFToken(),
            },
            success: function () {
                localStorage.removeItem('api_token');
                $("#authSection").show();
                $("#todoSection").hide();
            },
            error: function (xhr) {
                alert("Ошибка при выходе: " + xhr.responseText);
            }
        });
    });

    // Добавление задачи
$("#addTaskBtn").click(function () {
    let title = $("#taskTitle").val();
    let text = $("#taskText").val();
    let tags = $("#taskTags").val(); // Получаем массив ID выбранных тегов

    // Проверяем, что теги не пустые и содержат значения
    if (!tags || tags.length === 0) {
        alert("Пожалуйста, выберите хотя бы один тег.");
        return;
    }

    $.ajax({
        url: '/api/tasks',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
            'X-CSRF-TOKEN': getCSRFToken(),
        },
        data: { title, text, tags },
        success: function () {
            loadTasks();
        },
        error: function (xhr) {
            alert("Ошибка при добавлении задачи: " + xhr.responseText);
        }
    });
});


    function loadTags() {
        $.ajax({
            url: '/api/tags',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
            },
            success: function (tags) {
                let $taskTags = $("#taskTags");
                $taskTags.empty();
                tags.forEach(tag => {
                    $taskTags.append(`<option value="${tag.id}">${tag.title}</option>`);
                });
            },
            error: function (xhr) {
                console.error("Ошибка загрузки тегов", xhr.responseText);
            }
        });
    }

    function loadTasks() {
        $.ajax({
            url: '/api/tasks',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
            },
            success: function (tasks) {
                console.log('Задачи загружены:', tasks); // Логирование для проверки
    
                $("#taskList").empty(); // Очистить текущий список задач
    
                if (tasks.length === 0) {
                    $("#taskList").append('<li class="list-group-item">Нет задач</li>'); // Если задач нет, вывести сообщение
                }
    
                tasks.forEach(task => {
                    let tagsHtml = task.tags.map(tag => `<span class="badge bg-info">${tag.title}</span>`).join(' ');
    
                    // Генерация HTML для задачи
                    $("#taskList").append(`
                        <li class="list-group-item task-item" data-id="${task.id}">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>${task.title}</h5>
                                    <p>${task.text}</p>
                                    <div>${tagsHtml}</div>
                                </div>
                                <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Удалить</button>
                            </div>
                        </li>
                    `);
                });
    
                // Инициализация Sortable для перетаскивания задач
                new Sortable(document.querySelector('#taskList'), {
                    handle: '.task-item', // Указываем, что можно перетаскивать весь элемент
                    onEnd: function (evt) {
                        let orderedTaskIds = [];
                        $("#taskList li").each(function () {
                            orderedTaskIds.push($(this).data('id'));
                        });
    
                        // Отправляем новый порядок на сервер для обновления
                        $.ajax({
                            url: '/api/tasks/reorder', // Роут для обновления порядка задач
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content") // Добавляем CSRF токен
                            },
                            data: { task_ids: orderedTaskIds },
                            success: function (data) {
                                console.log("Задачи успешно обновлены");
                            },
                            error: function (xhr) {
                                alert("Ошибка при сохранении порядка задач: " + xhr.responseText);
                            }
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                alert("Ошибка при загрузке задач: " + xhr.responseText);
            }
        });
    }
    
    
    
    
   
    
    
    

    $(document).on('click', '.delete-task', function () {
        let taskId = $(this).data('id');

        $.ajax({
            url: `/api/tasks/${taskId}`,
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'X-CSRF-TOKEN': getCSRFToken(),
            },
            success: function () {
                loadTasks();
            },
            error: function (xhr) {
                alert("Ошибка при удалении задачи: " + xhr.responseText);
            }
        });
    });

    $("#taskTags").select2({
        tags: true, // Позволяет вводить новые теги
        tokenSeparators: [',', ' '], // Разделители для ввода
        placeholder: "Выберите или создайте теги",
        width: '100%', // Ширина селекта 100%
        ajax: {
            url: '/api/tags/search', // Путь к методу поиска тегов
            dataType: 'json',
            delay: 250,
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'), // Добавляем токен
                'X-CSRF-TOKEN': getCSRFToken(), // CSRF токен, если нужно
            },
            data: function (params) {
                return {
                    query: params.term // передаем строку запроса, введенную пользователем
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(tag => ({
                        id: tag.id,
                        text: tag.title
                    }))
                };
            }
        },
        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') return null;
    
            // Отправляем запрос на создание нового тега
            $.ajax({
                url: '/api/tags',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                    'X-CSRF-TOKEN': getCSRFToken(),
                },
                data: { title: term },
                success: function (data) {
                    // Если тег успешно создан, добавляем его в список
                    let newTag = {
                        id: data.id,
                        text: data.title
                    };
                    // Добавляем новый тег в Select2
                    let newOption = new Option(newTag.text, newTag.id, true, true);
                    $("#taskTags").append(newOption).trigger('change');
                },
                error: function (xhr) {
                    alert("Ошибка при создании тега: " + xhr.responseText);
                }
            });
    
            return null; // Не возвращаем созданный тег сразу в список, так как он будет добавлен через success
        }
    });
    
    

    //  Добавление нового тега
    $("#taskTags").on('select2:select', function (e) {
        let data = e.params.data;
        if (data.newTag) {
            $.ajax({
                url: '/api/tags',
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem('api_token') },
                data: { title: data.text },
                success: function (response) {
                    let newOption = new Option(response.title, response.id, true, true);
                    $("#taskTags").append(newOption).trigger('change');
                },
                error: function (xhr) {
                    alert("Ошибка при создании тега: " + xhr.responseText);
                }
            });
        }
    });
});

document.getElementById('feedback-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const responseMessage = document.getElementById('response-message');
    
    fetch('api/api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        responseMessage.textContent = data.message;
        responseMessage.style.color = data.success ? 'green' : 'red';
        
        if (data.success) {
            this.reset();
        }
    })
    .catch(error => {
        responseMessage.textContent = 'Ошибка соединения с сервером';
        responseMessage.style.color = 'red';
    });
});

document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const searchResults = document.getElementById('search-results');
    
    fetch('api/search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayUsers(data.users);
        } else {
            searchResults.innerHTML = '<p style="color: red;">' + data.message + '</p>';
        }
    })
    .catch(error => {
        searchResults.innerHTML = '<p style="color: red;">Ошибка соединения с сервером</p>';
    });
});

document.getElementById('show-all-btn').addEventListener('click', function() {
    const searchResults = document.getElementById('search-results');
    
    fetch('api/search.php', {
        method: 'POST',
        body: new FormData()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayUsers(data.users);
        } else {
            searchResults.innerHTML = '<p style="color: red;">' + data.message + '</p>';
        }
    })
    .catch(error => {
        searchResults.innerHTML = '<p style="color: red;">Ошибка соединения с сервером</p>';
    });
});

document.getElementById('update-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const updateMessage = document.getElementById('update-message');
    
    fetch('api/update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        updateMessage.textContent = data.message;
        updateMessage.style.color = data.success ? 'green' : 'red';
        
        if (data.success) {
            this.reset();
            this.style.display = 'none';
            document.getElementById('show-all-btn').click();
        }
    })
    .catch(error => {
        updateMessage.textContent = 'Ошибка соединения с сервером';
        updateMessage.style.color = 'red';
    });
});

document.getElementById('cancel-update-btn').addEventListener('click', function() {
    document.getElementById('update-form').style.display = 'none';
    document.getElementById('update-form').reset();
});

function displayUsers(users) {
    const searchResults = document.getElementById('search-results');
    
    if (users.length === 0) {
        searchResults.innerHTML = '<p>Пользователи не найдены</p>';
        return;
    }
    
    let html = '<h3>Результаты поиска:</h3><ul>';
    users.forEach(user => {
        html += '<li>';
        html += '<strong>ID:</strong> ' + user.id + ', ';
        html += '<strong>Имя:</strong> ' + user.name + ', ';
        html += '<strong>Сообщение:</strong> ' + (user.message || 'нет') + ', ';
        html += '<strong>Дата:</strong> ' + user.created_at;
        html += ' <button onclick="editUser(' + user.id + ', \'' + user.name.replace(/'/g, "\\'") + '\', \'' + (user.message || '').replace(/'/g, "\\'") + '\')">Редактировать</button>';
        html += ' <button onclick="deleteUser(' + user.id + ', \'' + user.name.replace(/'/g, "\\'") + '\')">Удалить</button>';
        html += '</li>';
    });
    html += '</ul>';
    
    searchResults.innerHTML = html;
}

function editUser(id, name, message) {
    document.getElementById('update-id').value = id;
    document.getElementById('update-name').value = name;
    document.getElementById('update-message').value = message;
    document.getElementById('update-password').value = '';
    document.getElementById('update-form').style.display = 'block';
    document.getElementById('update-message').textContent = '';
}

function deleteUser(id, name) {
    if (!confirm('Вы уверены, что хотите удалить пользователя "' + name + '"?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('api/delete.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('show-all-btn').click();
        }
    })
    .catch(error => {
        alert('Ошибка соединения с сервером');
    });
}
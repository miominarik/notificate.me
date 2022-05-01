function ShowHistoryTask(task_id) {
    axios.post('/tasks/' + task_id + '/history')
        .then(function (response) {
            if (response.data) {
                let container = document.querySelector('#history_list');
                let ul = document.createElement('ol');
                ul.classList.add('list-group');

                response.data.forEach(function (item) {
                    let li = document.createElement('li');
                    li.classList.add('list-group-item');

                    li.textContent = item.created_at;
                    ul.appendChild(li);
                });

                container.appendChild(ul);

                const offcanvasShowHistory = new bootstrap.Offcanvas(document.getElementById(
                    'offcanvasShowHistory'));
                offcanvasShowHistory.show();
            }
        })
        .catch(function (error) {
            //console.log(error);
        });
}

function getDataToEditTaskForm(task_id) {
    axios.post('/tasks/' + task_id + '/edit')
        .then(function(response) {
            document.getElementById('task_name_edit').value = response.data[0].task_name;
            document.getElementById('task_note_edit').value = response.data[0].task_note;
            document.getElementById('task_next_date_edit').value = response.data[0].task_next_date;
            document.getElementById('task_repeat_value_edit').value = response.data[0].task_repeat_value;
            document.getElementById('task_repeat_type_edit').value = response.data[0].task_repeat_type;
            document.getElementById('task_notification_value_edit').value = response.data[0]
                .task_notification_value;
            document.getElementById('task_notification_type_edit').value = response.data[0]
                .task_notification_type;

            document.getElementById('delete_form').action = 'tasks/' + task_id;
            document.getElementById('offcanvasEditForm').action = 'tasks/' + task_id;
            document.getElementById('show_history_btn').setAttribute('onclick', 'ShowHistoryTask(' +
                task_id + ')')

            const offcanvasEditTask = new bootstrap.Offcanvas(document.getElementById(
                'offcanvasEditTask'));
            offcanvasEditTask.show();
        })
        .catch(function(error) {
            //console.log(error);
        });
}

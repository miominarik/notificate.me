var glower = document.getElementById('notif_bell');
window.setInterval(function () {
    glower.classList.toggle('active');
}, 1000);


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
        .then(function (response) {

            if (response.data[0].task_type == 0) {
                document.getElementById("task_repeat_value_edit").required = false;
                document.getElementById('add_task_repeat_group_edit').style.display = 'none';

                document.getElementById('task_type_edit0').checked = true;
                document.getElementById('task_type_edit1').checked = false;

                document.getElementById('show_history_btn').style.display = 'none';
                document.getElementById('complete_btn').style.display = 'none';

            } else {
                document.getElementById('task_repeat_value_edit').value = response.data[0].task_repeat_value;
                document.getElementById('task_repeat_type_edit').value = response.data[0].task_repeat_type;

                document.getElementById('add_task_repeat_group_edit').removeAttribute('style');
                document.getElementById("task_repeat_value_edit").required = true;

                document.getElementById('task_type_edit0').checked = false;
                document.getElementById('task_type_edit1').checked = true;

                document.getElementById('show_history_btn').setAttribute('onclick', 'ShowHistoryTask(' +
                    task_id + ')')
                document.getElementById('show_history_btn').removeAttribute('style');

                document.getElementById('form_complete_task').setAttribute('action', '/tasks/complete/' + task_id)
                document.getElementById('complete_btn').removeAttribute('style');


            }

            document.getElementById('task_name_edit').value = response.data[0].task_name;
            document.getElementById('task_note_edit').value = response.data[0].task_note;
            document.getElementById('task_next_date_edit').value = response.data[0].task_next_date;
            document.getElementById('task_notification_value_edit').value = response.data[0].task_notification_value;
            document.getElementById('task_notification_type_edit').value = response.data[0].task_notification_type;
            document.getElementById('delete_form').action = '/tasks/' + task_id;
            document.getElementById('offcanvasEditForm').action = '/tasks/' + task_id;


            const offcanvasEditTask = new bootstrap.Offcanvas(document.getElementById(
                'offcanvasEditTask'));
            offcanvasEditTask.show();
        })
        .catch(function (error) {
            //console.log(error);
        });
}

function NewTaskChangeType(type) {

    if (type) {
        if (type == 'one') {
            document.getElementById("task_repeat_value").required = false;
            document.getElementById('add_task_repeat_group').style.display = 'none';
        } else if (type == 'reap') {
            document.getElementById('add_task_repeat_group').removeAttribute('style');
            document.getElementById("task_repeat_value").required = true;
        }
    }


}

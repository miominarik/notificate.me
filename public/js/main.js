var glower = document.getElementById('notif_bell');

let empty_list_text = "";

if (empty_list_text == "") {
    empty_list_text = document.getElementById('files_list').innerHTML;
}

if (glower) {
    window.setInterval(function () {
        glower.classList.toggle('active');
    }, 1000);
}


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

function ShowFiles(task_id) {
    axios.post('/tasks/' + task_id + '/all_files')
    .then(function (response) {

        let final_html = "";

        if (response.data.length > 0) {
            response.data.forEach(element => {

                final_html += '<div class="card me-4 mb-4 file_card"  onclick="window.location.replace(\'https://notificate.me/files/' + element.id + '/download\')">\n' +
                    '  <img src="https://notificate.me/images/file_avatar_new.png" class="card-img-top" alt="...">\n' +
                    '  <div class="card-body">\n' +
                    '    <h5 class="text-muted text-truncate">' + element.file_name + '</h5>\n' +
                    '  </div>\n' +
                    '</div>'
            });
        } else {
            final_html = empty_list_text;
        }

        document.getElementById('files_list').innerHTML = final_html;


        const offcanvasShowFiles = new bootstrap.Offcanvas(document.getElementById(
            'offcanvasShowFiles'));
        offcanvasShowFiles.show();
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
                task_id + ')');
            document.getElementById('show_history_btn').removeAttribute('style');

            document.getElementById('form_complete_task').setAttribute('action', '/tasks/complete/' + task_id)
            document.getElementById('complete_btn').removeAttribute('style');


        }

        document.getElementById('file_upload_task_id').value = response.data[0].id;
        document.getElementById('show_files_btn').setAttribute('onclick', 'ShowFiles(' + task_id + ')')
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

function CopyIcsUrl() {
    var textBox = document.getElementById("public_ics_url");
    textBox.select();
    document.execCommand("copy");
}

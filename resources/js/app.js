require('./bootstrap');

var glower = document.getElementById('notif_bell');
        window.setInterval(function() {
            glower.classList.toggle('active');
        }, 1000);

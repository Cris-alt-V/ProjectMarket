<div class="notifications-wrapper">
    <button id="notificationsToggle" class="notifications-toggle" type="button" aria-label="Ver notificaciones">
        <span class="notifications-bell">N</span>
        <span id="notificationBadge" class="notifications-badge">0</span>
    </button>

    <div id="notificationsDropdown" class="notifications-dropdown">
        <div class="notifications-header">
            <strong>Notificaciones</strong>
            <small>Mensajes y actividad reciente</small>
        </div>
        <div id="notificationsList" class="notifications-list"></div>
        <div class="notifications-footer">Haz clic para marcar como leida</div>
    </div>
</div>

<script>
    (function(){
        const toggle = document.getElementById('notificationsToggle');
        const dropdown = document.getElementById('notificationsDropdown');
        const badge = document.getElementById('notificationBadge');
        const list = document.getElementById('notificationsList');

        function formatDate(value) {
            return value ? new Date(value).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' }) : '';
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function(char) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
            });
        }

        function renderNotifications(data){
            const { unread, notifications = [] } = data;
            const notificationList = Array.isArray(notifications) ? notifications : [];
            badge.style.display = unread && unread > 0 ? 'inline-flex' : 'none';
            badge.textContent = unread || 0;

            list.innerHTML = '';
            if(notificationList.length === 0){
                list.innerHTML = '<div class="notifications-empty">No hay notificaciones.</div>';
                return;
            }

            notificationList.forEach(n => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'notification-item' + (!n.read_at ? ' unread' : '');

                item.innerHTML = `
                    <span class="notification-icon">${escapeHtml(n.icon || 'NOT')}</span>
                    <span class="notification-copy">
                        <strong>${escapeHtml(n.title || 'Notificacion')}</strong>
                        <span>${escapeHtml(n.body || n.text || 'Tienes una nueva notificacion.')}</span>
                        <small>${escapeHtml(n.meta || '')}${n.meta ? ' - ' : ''}${escapeHtml(formatDate(n.created_at))}</small>
                    </span>
                `;

                item.addEventListener('click', function(){
                    fetch('/notifications/' + n.id + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    }).then(() => {
                        fetchAndRender();
                        if (n.url) {
                            window.location.href = n.url;
                        }
                    });
                });
                list.appendChild(item);
            });
        }

        function fetchAndRender(){
            fetch('/notifications', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            })
                .then(r => r.json())
                .then(data => renderNotifications(data))
                .catch(() => {
                    list.innerHTML = '<div class="notifications-empty">Error cargando notificaciones.</div>';
                    badge.style.display = 'none';
                });
        }

        toggle.addEventListener('click', function(event){
            event.stopPropagation();
            dropdown.classList.toggle('active');
            if(dropdown.classList.contains('active')) fetchAndRender();
        });

        document.addEventListener('click', function(event) {
            if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        document.addEventListener('DOMContentLoaded', function(){
            if(window.currentSessionUser){ fetchAndRender(); }
        });
    })();
</script>
<?php /**PATH C:\Users\ASUS\Desktop\aritomarket\ProjectMarket\market\resources\views/partials/notifications.blade.php ENDPATH**/ ?>
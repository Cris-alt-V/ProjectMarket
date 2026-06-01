<div class="notifications-wrapper" style="position: relative; display: inline-block; margin-right: 12px;">
    <button id="notificationsToggle" style="background:none;border:none;cursor:pointer;position:relative;font-size:18px;">
        🔔 <span id="notificationBadge" style="background:#e53e3e;color:#fff;border-radius:12px;padding:2px 6px;font-size:12px;display:none;position:absolute;top:-8px;right:-8px;">0</span>
    </button>

    <div id="notificationsDropdown" style="display:none; position:absolute; right:0; top:36px; width:320px; max-height:420px; overflow:auto; background:#fff; border:1px solid #eee; box-shadow:0 8px 24px rgba(0,0,0,0.08); z-index:1000;">
        <div style="padding:12px; border-bottom:1px solid #f2f2f2; font-weight:700;">Notificaciones</div>
        <div id="notificationsList" style="padding:8px;"></div>
        <div style="padding:8px; border-top:1px solid #f2f2f2; text-align:center;"><small style="color:#666;">Haz clic en una notificación para marcarla como leída</small></div>
    </div>
</div>

<script>
    (function(){
        const toggle = document.getElementById('notificationsToggle');
        const dropdown = document.getElementById('notificationsDropdown');
        const badge = document.getElementById('notificationBadge');
        const list = document.getElementById('notificationsList');

        function renderNotifications(data){
            const { unread, notifications } = data;
            if(unread && unread > 0){
                badge.style.display = 'inline-block';
                badge.textContent = unread;
            } else {
                badge.style.display = 'none';
            }

            list.innerHTML = '';
            if(notifications.length === 0){
                list.innerHTML = '<div style="padding:12px;color:#666">No hay notificaciones.</div>';
                return;
            }

            notifications.forEach(n => {
                const item = document.createElement('div');
                item.style.padding = '10px';
                item.style.borderBottom = '1px solid #f5f5f5';
                item.style.cursor = 'pointer';
                if(!n.read_at){ item.style.background = '#f9fbff'; }
                const text = (n.data && n.data.message) ? n.data.message : (n.type || 'Notificación');
                item.innerHTML = `<div style="font-weight:600">${text}</div><div style="font-size:12px;color:#666;margin-top:4px">${new Date(n.created_at).toLocaleString()}</div>`;
                item.addEventListener('click', function(){
                    fetch('/notifications/' + n.id + '/read', { method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                        .then(()=>{ fetchAndRender(); });
                });
                list.appendChild(item);
            });
        }

        function fetchAndRender(){
            fetch('/notifications', {credentials: 'same-origin'})
                .then(r => r.json())
                .then(data => renderNotifications(data))
                .catch(()=>{/* ignore errors */});
        }

        toggle.addEventListener('click', function(){
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            if(dropdown.style.display === 'block') fetchAndRender();
        });

        // initial background fetch after page load
        document.addEventListener('DOMContentLoaded', function(){
            if(window.currentSessionUser){ fetchAndRender(); }
        });
    })();
</script>
<?php /**PATH C:\Users\crist\OneDrive\Desktop\asdf\ProjectMarket\market\resources\views/partials/notifications.blade.php ENDPATH**/ ?>
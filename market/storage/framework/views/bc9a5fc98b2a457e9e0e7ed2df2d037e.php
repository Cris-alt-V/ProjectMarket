<?php $__env->startSection('title', 'Mensajes - Marketplace Local'); ?>

<?php $__env->startSection('content'); ?>
  <section class="messages-page">
    <div class="messages-shell">
      <aside class="messages-sidebar">
        <div class="messages-sidebar-header">
          <h1>Mensajes</h1>
          <span id="chatConnectionStatus" class="chat-status">Conectando</span>
        </div>
        <div id="conversationList" class="conversation-list">
          <div class="conversation-empty">Cargando conversaciones...</div>
        </div>
      </aside>

      <section class="chat-panel">
        <div id="chatWelcome" class="chat-welcome">
          <h2>Selecciona una conversacion</h2>
          <p>Tus chats con compradores y vendedores apareceran aqui.</p>
        </div>

        <div id="chatRoom" class="chat-room hidden">
          <header class="chat-room-header">
            <div>
              <h2 id="chatPartnerName">Contacto</h2>
              <p id="chatProductName">Conversacion</p>
            </div>
          </header>

          <div id="chatMessages" class="chat-messages"></div>

          <form id="chatForm" class="chat-form">
            <textarea id="chatBody" rows="1" maxlength="5000" placeholder="Escribe un mensaje"></textarea>
            <button type="submit" aria-label="Enviar mensaje">Enviar</button>
          </form>
        </div>
      </section>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(rtrim($chatServerUrl, '/')); ?>/socket.io/socket.io.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('messages-body');

    function updateMessagesViewport() {
      const pageHeader = document.querySelector('body > header');
      const headerHeight = pageHeader ? pageHeader.offsetHeight : 0;
      document.documentElement.style.setProperty('--messages-header-height', headerHeight + 'px');
    }

    updateMessagesViewport();
    window.addEventListener('resize', updateMessagesViewport);

    const currentUser = window.currentSessionUser;
    const chatServerUrl = <?php echo json_encode($chatServerUrl, 15, 512) ?>;
    const conversationList = document.getElementById('conversationList');
    const chatWelcome = document.getElementById('chatWelcome');
    const chatRoom = document.getElementById('chatRoom');
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatBody = document.getElementById('chatBody');
    const chatPartnerName = document.getElementById('chatPartnerName');
    const chatProductName = document.getElementById('chatProductName');
    const chatConnectionStatus = document.getElementById('chatConnectionStatus');
    const initialContact = new URLSearchParams(window.location.search);
    let conversations = [];
    let activeConversation = null;
    let socket = null;
    let lastMessageScroll = 0;

    function escapeHtml(value) {
      return String(value || '').replace(/[&<>"']/g, function(char) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
      });
    }

    function timeLabel(value) {
      if (!value) {
        return '';
      }
      return new Date(value).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });
    }

    function setStatus(text, connected) {
      chatConnectionStatus.textContent = text;
      chatConnectionStatus.classList.toggle('connected', Boolean(connected));
    }

    function renderConversations() {
      if (!conversations.length) {
        conversationList.innerHTML = '<div class="conversation-empty">Aun no tienes conversaciones.</div>';
        return;
      }

      conversationList.innerHTML = conversations.map(function(conversation) {
        const active = activeConversation && Number(activeConversation.conversation_id) === Number(conversation.conversation_id);
        return `
          <button class="conversation-item ${active ? 'active' : ''}" data-conversation-id="${conversation.conversation_id}">
            <span class="conversation-avatar">${escapeHtml((conversation.partner_name || 'U').charAt(0).toUpperCase())}</span>
            <span class="conversation-meta">
              <span class="conversation-topline">
                <strong>${escapeHtml(conversation.partner_name || 'Usuario')}</strong>
                <small>${timeLabel(conversation.last_at)}</small>
              </span>
              <span class="conversation-product">${escapeHtml(conversation.product_name || 'Conversacion')}</span>
              <span class="conversation-last">${escapeHtml(conversation.last_message || '')}</span>
            </span>
            ${conversation.unread ? `<span class="conversation-unread">${conversation.unread}</span>` : ''}
          </button>
        `;
      }).join('');
    }

    function renderMessages(messages) {
      if (!messages.length) {
        chatMessages.innerHTML = '<div class="chat-empty">No hay mensajes todavia.</div>';
        return;
      }

      messages = [...messages].sort(function(a, b) {
        const aTime = new Date(a.created_at).getTime() || 0;
        const bTime = new Date(b.created_at).getTime() || 0;
        if (aTime === bTime) {
          return (Number(a.id) || 0) - (Number(b.id) || 0);
        }
        return aTime - bTime;
      });

      chatMessages.innerHTML = messages.map(function(message) {
        const mine = Number(message.sender_id) === Number(currentUser.id_usuario);
        return `
          <div class="message-row ${mine ? 'mine' : 'theirs'}">
            <div class="message-bubble">
              <p>${escapeHtml(message.body)}</p>
              <time>${timeLabel(message.created_at)}</time>
            </div>
          </div>
        `;
      }).join('');
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function initialContactConversation() {
      const productId = Number(initialContact.get('product_id') || 0);
      const receiverId = Number(initialContact.get('receiver_id') || 0);

      if (!productId || !receiverId) {
        return null;
      }

      const existing = conversations.find(function(conversation) {
        return Number(conversation.product_id) === productId && Number(conversation.partner_id) === receiverId;
      });

      return existing || {
        conversation_id: null,
        product_id: productId,
        product_name: initialContact.get('product_name') || 'Producto',
        partner_id: receiverId,
        partner_name: initialContact.get('partner_name') || 'Vendedor',
        last_message: '',
        last_at: null,
        unread: 0,
      };
    }

    async function loadConversations() {
      const data = await MarketplaceApp.api('/messages/conversations');
      conversations = data.conversations || [];
      renderConversations();

      if (!activeConversation) {
        const contactConversation = initialContactConversation();
        if (contactConversation) {
          openConversation(contactConversation);
        } else if (conversations.length) {
          openConversation(conversations[0]);
        }
      }
    }

    async function openConversation(conversationRef) {
      activeConversation = typeof conversationRef === 'object'
        ? conversationRef
        : conversations.find(function(conversation) {
            return Number(conversation.conversation_id) === Number(conversationRef);
          });

      if (!activeConversation) {
        return;
      }

      renderConversations();
      chatWelcome.classList.add('hidden');
      chatRoom.classList.remove('hidden');
      chatPartnerName.textContent = activeConversation.partner_name || 'Usuario';
      chatProductName.textContent = activeConversation.product_name || 'Conversacion';

      if (activeConversation.conversation_id) {
        const data = await MarketplaceApp.api('/messages/conversation/' + activeConversation.conversation_id);
        renderMessages(data.messages || []);
        socket?.emit('joinConversation', { conversation_id: activeConversation.conversation_id });
      } else {
        renderMessages([]);
      }
    }

    chatMessages.addEventListener('scroll', function() {
      const currentScroll = chatMessages.scrollTop;
      const shouldHideHeader = currentScroll > 24 && currentScroll > lastMessageScroll;
      chatRoom.classList.toggle('header-hidden', shouldHideHeader);
      lastMessageScroll = Math.max(currentScroll, 0);
    });

    conversationList.addEventListener('click', function(event) {
      const item = event.target.closest('.conversation-item');
      if (item) {
        openConversation(item.dataset.conversationId);
      }
    });

    chatForm.addEventListener('submit', function(event) {
      event.preventDefault();
      sendActiveMessage();
    });

    chatBody.addEventListener('keydown', function(event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendActiveMessage();
      }
    });

    function sendActiveMessage() {
      if (!activeConversation) {
        return;
      }

      const body = chatBody.value.trim();
      if (!body) {
        return;
      }

      const payload = {
        conversation_id: activeConversation.conversation_id,
        sender_id: currentUser.id_usuario,
        receiver_id: activeConversation.partner_id,
        product_id: activeConversation.product_id,
        body: body,
      };

      if (socket && socket.connected) {
        socket.emit('sendMessage', payload);
        chatBody.value = '';
      } else {
        MarketplaceApp.api('/messages/send', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        }).then(function() {
          chatBody.value = '';
          loadConversations().then(function() {
            const savedConversationId = activeConversation.conversation_id;
            const createdConversation = conversations.find(function(conversation) {
              return Number(conversation.conversation_id) === Number(savedConversationId)
                || (
                  Number(conversation.product_id) === Number(payload.product_id)
                  && Number(conversation.partner_id) === Number(payload.receiver_id)
                );
            });

            if (createdConversation) {
              openConversation(createdConversation);
            }
          });
        }).catch(function() {
          MarketplaceApp.showNotification('No se pudo enviar el mensaje.', 'error');
        });
      }
    }

    if (window.io) {
      socket = io(chatServerUrl, { transports: ['websocket', 'polling'] });
      socket.on('connect', function() {
        setStatus('En linea', true);
        socket.emit('registerUser', { user_id: currentUser.id_usuario });
        if (activeConversation) {
          socket.emit('joinConversation', { conversation_id: activeConversation.conversation_id });
        }
      });
      socket.on('disconnect', function() {
        setStatus('Sin conexion', false);
      });
      socket.on('messageSaved', function(message) {
        const belongsToDraft = activeConversation
          && !activeConversation.conversation_id
          && Number(message.product_id) === Number(activeConversation.product_id)
          && (
            Number(message.sender_id) === Number(activeConversation.partner_id)
            || Number(message.receiver_id) === Number(activeConversation.partner_id)
          );

        if (belongsToDraft) {
          activeConversation.conversation_id = message.conversation_id;
          socket.emit('joinConversation', { conversation_id: message.conversation_id });
        }

        if (activeConversation && Number(message.conversation_id) === Number(activeConversation.conversation_id)) {
          MarketplaceApp.api('/messages/conversation/' + activeConversation.conversation_id)
            .then(data => renderMessages(data.messages || []));
        }
        loadConversations();
      });
      socket.on('chatError', function(error) {
        MarketplaceApp.showNotification(error.message || 'Error en el chat.', 'error');
      });
    } else {
      setStatus('Servidor no disponible', false);
    }

    loadConversations().catch(function() {
      conversationList.innerHTML = '<div class="conversation-empty">No se pudieron cargar las conversaciones.</div>';
    });
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASUS\Desktop\aritomarket\ProjectMarket\market\resources\views/mensajes.blade.php ENDPATH**/ ?>
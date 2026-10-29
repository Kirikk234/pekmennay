const API = BASE_URL + '/api.php';
async function post(action, data = {}) {
  const fd = new FormData();
  fd.append('action', action);
  Object.entries(data).forEach(([k, v]) => fd.append(k, v));
  const r = await fetch(API, { method: 'POST', body: fd });
  return r.json();
}

async function getCart() {
  const r = await fetch(`${API}?action=get`);
  return r.json();
}

function showToast(text) {
  const t = document.createElement('div');
  t.textContent = text;
  t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#2d2a26;color:#fff;padding:12px 24px;border-radius:12px;font-size:14px;z-index:9999;opacity:0;transition:0.3s;transform:translateY(10px);box-shadow:0 10px 30px rgba(0,0,0,0.2);';
  document.body.appendChild(t);
  requestAnimationFrame(() => { t.style.opacity = '1'; t.style.transform = 'translateY(0)'; });
  setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(10px)'; setTimeout(() => t.remove(), 300); }, 2500);
}

async function refreshBadge() {
  const b = document.getElementById('cart-count');
  if (!b) return;
  const d = await getCart();
  let n = 0; d.items.forEach(i => n += Number(i.quantity));
  b.textContent = n;
  b.style.transform = 'scale(1.2)';
  setTimeout(() => b.style.transform = 'scale(1)', 200);
}

document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('burger');
  const nav = document.getElementById('nav');
  if (burger && nav) burger.addEventListener('click', () => nav.classList.toggle('open'));

  const chips = document.querySelectorAll('#filters .chip');
  const cards = document.querySelectorAll('#grid .card');
  chips.forEach(ch => ch.addEventListener('click', () => {
    chips.forEach(c => c.classList.remove('active'));
    ch.classList.add('active');
    const cat = ch.dataset.cat;
    cards.forEach(c => c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none');
  }));

  document.querySelectorAll('.add-to-cart').forEach(b => {
    b.addEventListener('click', async () => {
      const res = await post('add', { id: b.dataset.id });
      if (res.ok) {
        refreshBadge();
        showToast('Товар добавлен в корзину 🥟');
        const oldText = b.textContent;
        b.textContent = '✓ Добавлено';
        b.style.background = '#2ecc71';
        setTimeout(() => { b.textContent = oldText; b.style.background = ''; }, 1500);
      }
    });
  });

  renderCartPage();
  refreshBadge();
  initAuthForm();
});

async function renderCartPage() {
  const box = document.getElementById('cart-items');
  const sidebar = document.getElementById('cart-sidebar');
  if (!box) return;

  const data = await getCart();
  
  if (!data.items.length) {
    box.innerHTML = '<div style="text-align:center;padding:60px 20px;background:#fff;border-radius:16px;"><div style="font-size:60px;margin-bottom:20px;">🛒</div><h3>Корзина пуста</h3><p style="color:#7a746e;margin-bottom:20px;">Самое время добавить пельменей!</p><a href="/pekmenay/dumpling_shop/index.php#menu" class="btn btn-primary">В меню</a></div>';
    if (sidebar) sidebar.style.display = 'none';
    return;
  }

  if (sidebar) sidebar.style.display = 'block';

  box.innerHTML = data.items.map(i => `
    <div class="cart-item">
      <div class="card-img">🥟</div>
      <div class="cart-item-info">
        <h4>${i.name}</h4>
        <p>${i.price} ₽</p>
      </div>
      <div class="qty-control">
        <button data-act="dec" data-id="${i.id}">−</button>
        <span>${i.quantity}</span>
        <button data-act="inc" data-id="${i.id}">+</button>
        <button data-act="del" data-id="${i.id}" style="color:#e74c3c;margin-left:10px;">✕</button>
      </div>
    </div>
  `).join('');

  if (sidebar) {
    document.getElementById('cart-total').textContent = data.total.toLocaleString('ru-RU') + ' ₽';
    document.getElementById('cart-subtotal').textContent = data.total.toLocaleString('ru-RU') + ' ₽';
  }

  box.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', async () => {
      const act = btn.dataset.act, id = btn.dataset.id;
      if (act === 'inc') await post('update', { id, delta: 1 });
      if (act === 'dec') await post('update', { id, delta: -1 });
      if (act === 'del') await post('remove', { id });
      renderCartPage();
      refreshBadge();
    });
  });
}

function initAuthForm() {
  const form = document.getElementById('auth-form');
  if (!form) return;
  form.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const msg = document.getElementById('form-msg');
    const fd = new FormData(form);
    
    btn.disabled = true; btn.textContent = 'Загрузка...';
    msg.textContent = '';
    
    const res = await post(fd.get('action'), Object.fromEntries(fd.entries()));
    
    if (res.ok) {
      window.location.href = BASE_URL + '/profile.php';
    } else {
      msg.textContent = res.error || 'Ошибка';
      btn.disabled = false; btn.textContent = 'Отправить';
    }
  });
}
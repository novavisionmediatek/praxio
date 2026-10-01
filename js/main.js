/* Praxia – animations + form handling (layout is rendered by PHP) */
(function () {
  const header = document.getElementById('header');
  document.getElementById('burger').onclick = () => header.classList.toggle('open');
  document.querySelectorAll('#navLinks a').forEach(a => a.addEventListener('click', () => header.classList.remove('open')));

  /* scroll: header, progress, parallax */
  const bar = document.getElementById('progress'), pars = [...document.querySelectorAll('[data-parallax] img')];
  const onScroll = () => {
    const y = scrollY, h = document.documentElement.scrollHeight - innerHeight;
    header.classList.toggle('scrolled', y > 40);
    bar.style.width = (h > 0 ? y / h * 100 : 0) + '%';
    pars.forEach(img => {
      const r = img.parentElement.getBoundingClientRect();
      if (r.bottom < 0 || r.top > innerHeight) return;
      img.style.transform = `translateY(${(r.top + r.height / 2 - innerHeight / 2) * -0.08}px)`;
    });
  };
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  /* split text */
  document.querySelectorAll('[data-split]').forEach(el => {
    let i = 0;
    const wrap = node => [...node.childNodes].forEach(n => {
      if (n.nodeType === 3) {
        const f = document.createDocumentFragment();
        n.textContent.split(/(\s+)/).forEach(p => {
          if (!p.trim()) { f.append(p); return; }
          const w = document.createElement('span'); w.className = 'w';
          const s = document.createElement('span'); s.className = 'wi'; s.style.setProperty('--i', i++); s.textContent = p;
          w.append(s); f.append(w);
        });
        n.replaceWith(f);
      } else if (n.nodeType === 1 && !n.classList.contains('rotator')) wrap(n);
    });
    wrap(el);
  });

  /* reveal + count-up */
  const io = new IntersectionObserver(es => es.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.classList.add('in'); io.unobserve(e.target);
    if (e.target.dataset.count !== undefined) countUp(e.target);
  }), { threshold: .15 });
  document.querySelectorAll('.reveal,[data-split],[data-count]').forEach(el => io.observe(el));
  function countUp(el) {
    const end = parseFloat(el.dataset.count), pre = el.dataset.prefix || '', suf = el.dataset.suffix || '', t0 = performance.now();
    const tick = t => { const p = Math.min((t - t0) / 1800, 1); el.textContent = pre + Math.round(end * (1 - Math.pow(1 - p, 3))) + suf; if (p < 1) requestAnimationFrame(tick); };
    requestAnimationFrame(tick);
  }

  /* typing rotator */
  const rot = document.querySelector('.rotator');
  if (rot) {
    const words = JSON.parse(rot.dataset.words); let w = 0, c = 0, del = false;
    (function type() {
      const word = words[w]; rot.textContent = word.slice(0, c);
      if (!del && c === word.length) { del = true; return setTimeout(type, 1600); }
      if (del && c === 0) { del = false; w = (w + 1) % words.length; }
      c += del ? -1 : 1; setTimeout(type, del ? 40 : 85);
    })();
  }

  /* tilt on feature cards */
  if (matchMedia('(hover:hover)').matches) {
    document.querySelectorAll('.feat').forEach(c => {
      c.addEventListener('mousemove', e => {
        const r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
        c.style.transform = `perspective(800px) rotateX(${-y * 5}deg) rotateY(${x * 5}deg) translateY(-6px)`;
      });
      c.addEventListener('mouseleave', () => c.style.transform = '');
    });
    document.querySelectorAll('.btn-gold').forEach(b => {
      b.addEventListener('mousemove', e => { const r = b.getBoundingClientRect(); b.style.transform = `translate(${(e.clientX - r.left - r.width / 2) * .15}px,${(e.clientY - r.top - r.height / 2) * .25}px)`; });
      b.addEventListener('mouseleave', () => b.style.transform = '');
    });
  }

  /* testimonial carousel */
  const car = document.querySelector('.tcar');
  if (car) {
    const step = () => car.querySelector('.quote').offsetWidth + 24;
    document.querySelector('[data-car=prev]').onclick = () => car.scrollBy({ left: -step(), behavior: 'smooth' });
    document.querySelector('[data-car=next]').onclick = () => car.scrollBy({ left: step(), behavior: 'smooth' });
    setInterval(() => { if (car.scrollLeft + car.clientWidth >= car.scrollWidth - 10) car.scrollTo({ left: 0, behavior: 'smooth' }); else car.scrollBy({ left: step(), behavior: 'smooth' }); }, 5500);
  }

  /* FAQ */
  document.querySelectorAll('.faq-q').forEach(q => q.addEventListener('click', () => {
    const item = q.parentElement, open = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!open) item.classList.add('open');
  }));
  document.querySelectorAll('.chip').forEach(ch => ch.addEventListener('click', () => {
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active')); ch.classList.add('active');
    document.querySelectorAll('.faq-item').forEach(i => i.style.display = (ch.dataset.cat === 'all' || i.dataset.cat === ch.dataset.cat) ? '' : 'none');
  }));

  /* forms -> api/submit.php (saved to MySQL) */
  document.querySelectorAll('form[data-form]').forEach(f => f.addEventListener('submit', async e => {
    e.preventDefault();
    const msg = f.querySelector('.form-msg'), btn = f.querySelector('button[type=submit]');
    const show = (text, ok) => {
      if (!msg) return;
      msg.textContent = text; msg.classList.add('show');
      msg.style.background = ok ? '' : 'rgba(200,50,50,.1)'; msg.style.color = ok ? '' : '#b32d2d';
    };
    const token = document.querySelector('meta[name=csrf-token]');
    const data = new FormData(f); if (token) data.set('csrf', token.content);
    btn.disabled = true;
    try {
      const r = await fetch(f.getAttribute('action'), { method: 'POST', body: data, headers: { 'X-Requested-With': 'fetch' } });
      const j = await r.json();
      show(j.message, j.ok);
      if (j.ok) { f.reset(); f.dispatchEvent(new CustomEvent('form:success', { bubbles: true })); }
    } catch (err) { show('Something went wrong. Please try again or email us.', false); }
    btn.disabled = false;
  }));
})();

/* Enquiry popup: opens after a short delay, remembers when it was closed */
(function () {
  const pop = document.getElementById('leadPop');
  if (!pop) return;
  const KEY = 'praxia_popup_until', delay = (+pop.dataset.delay || 0) * 1000, days = +pop.dataset.days || 0;
  let until = 0; try { until = +localStorage.getItem(KEY) || 0; } catch (e) {}
  if (Date.now() < until) return;
  let lastFocus = null;
  const open = () => {
    lastFocus = document.activeElement;
    pop.hidden = false; requestAnimationFrame(() => pop.classList.add('open'));
    document.body.classList.add('pop-lock');
    const first = pop.querySelector('input[name=name]'); if (first) setTimeout(() => first.focus(), 350);
  };
  const close = () => {
    pop.classList.remove('open'); document.body.classList.remove('pop-lock');
    setTimeout(() => { pop.hidden = true; }, 350);
    try { localStorage.setItem(KEY, String(Date.now() + days * 864e5)); } catch (e) {}
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  };
  pop.addEventListener('click', e => { if (e.target === pop || e.target.closest('[data-pop-close]')) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && pop.classList.contains('open')) close(); });
  pop.addEventListener('form:success', () => setTimeout(close, 2600));
  setTimeout(open, delay);
})();

/* CV upload box: shows the chosen file, supports drag and drop, checks type and size */
document.querySelectorAll('.upload').forEach(box => {
  const input = box.querySelector('input[type=file]'), name = box.querySelector('.upload-name'), err = box.querySelector('.upload-err');
  const hint = name.textContent, max = +box.dataset.max || 0;
  const fmt = n => n > 1048576 ? (n / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(n / 1024)) + ' KB';
  const fail = msg => { input.value = ''; box.classList.remove('has-file'); box.classList.add('bad'); err.textContent = msg; name.textContent = hint; };
  const update = () => {
    const f = input.files[0]; box.classList.remove('bad');
    if (!f) { box.classList.remove('has-file'); name.textContent = hint; return; }
    if (!/\.(pdf|docx?)$/i.test(f.name)) return fail('Please choose a PDF, DOC or DOCX file.');
    if (max && f.size > max) return fail('That file is ' + fmt(f.size) + '. The maximum is ' + fmt(max) + '.');
    box.classList.add('has-file'); name.textContent = f.name + ' · ' + fmt(f.size);
  };
  input.addEventListener('change', update);
  ['dragenter', 'dragover'].forEach(ev => box.addEventListener(ev, () => box.classList.add('drag')));
  ['dragleave', 'drop'].forEach(ev => box.addEventListener(ev, () => box.classList.remove('drag')));
  input.form && input.form.addEventListener('reset', () => setTimeout(update));
});

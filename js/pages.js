
(function(){
  let db = tourimGetDB();
  const money = n => '₹' + Number(n||0).toLocaleString('en-IN');
  const live = arr => (arr||[]).filter(x => ['published','active'].includes(x.status));
  const qs = new URLSearchParams(location.search);
  const esc = v => String(v ?? '').replace(/[&<>'"]/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[ch]));

  function assetImage(item){
    const raw=String((item&&item.image)||'').trim();
    if(raw && !/^linear-gradient|^radial-gradient/i.test(raw) && !(/^data:image/i.test(raw) && raw.length > 60000)) return tourimAssetUrl(raw);
    return tourimAssetUrl(tourimFallbackImageForItem(item));
  }
  function bg(item){
    const src=String(assetImage(item)||'').trim();
    if(/^linear-gradient|^radial-gradient|^url\(/i.test(src)) return src;
    const absolute=new URL(src, location.href).href;
    return `url('${absolute.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}')`;
  }
  function bgStyle(item){
    const css=esc(bg(item));
    return `style="background-image:${css};--img:${css}"`;
  }
  function imageUrl(item){ return esc(assetImage(item)); }
  function cardImage(item, alt){
    const fallback=esc(tourimAssetUrl(tourimFallbackImageForItem(item)));
    return `<img src="${imageUrl(item)}" data-fallback-src="${fallback}" alt="${esc(alt||'TOURIM travel photo')}" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;display:block;z-index:1" onerror="if(this.src!==this.dataset.fallbackSrc){this.src=this.dataset.fallbackSrc}else{this.remove()}">`;
  }
  function galleryCard(g, big=false){
    const fallback=esc(tourimAssetUrl(tourimFallbackImageForItem(g)));
    return `<article class="photo-card ${big?'big':''}" tabindex="0" data-photo-title="${esc(g.title)}" data-photo-src="${imageUrl(g)}"><img src="${imageUrl(g)}" data-fallback-src="${fallback}" alt="${esc(g.title)}" loading="lazy" onerror="if(this.src!==this.dataset.fallbackSrc){this.src=this.dataset.fallbackSrc}else{this.remove()}"><div class="photo-shade"></div><div class="photo-caption"><span>${esc(g.tag)}</span><b>${esc(g.title)}</b></div></article>`;
  }
  function bindPhotoLightbox(){
    if(document.querySelector('.photo-modal')) return;
    const modal=document.createElement('div'); modal.className='photo-modal'; modal.innerHTML='<button type="button" aria-label="Close photo">×</button><img alt="TOURIM gallery photo"><strong></strong>';
    document.body.appendChild(modal);
    const img=modal.querySelector('img'), title=modal.querySelector('strong');
    const close=()=>modal.classList.remove('open');
    modal.querySelector('button').addEventListener('click', close);
    modal.addEventListener('click', e=>{ if(e.target===modal) close(); });
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') close(); });
    document.querySelectorAll('.photo-card').forEach(card=>{
      const open=()=>{ img.src=card.dataset.photoSrc; img.alt=card.dataset.photoTitle||'TOURIM photo'; title.textContent=card.dataset.photoTitle||''; modal.classList.add('open'); };
      card.addEventListener('click', open);
      card.addEventListener('keydown', e=>{ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); open(); } });
    });
  }
  function settings(){
    const s=db.settings||{};
    document.querySelectorAll('[data-brand]').forEach(el=>el.textContent=s.websiteName||'TOURIM');
    document.querySelectorAll('[data-tagline]').forEach(el=>el.textContent=s.tagline||'See the world, Feel with TOURIM');
    document.querySelectorAll('[data-phone]').forEach(el=>el.textContent=s.phone||'7384732179');
    document.querySelectorAll('[data-email]').forEach(el=>el.textContent=s.email||'owner.tourim@gmail.com');
    document.querySelectorAll('[data-address]').forEach(el=>el.textContent=s.address||'Ashokenagar Kachua More, North 24 Parganas, 743272');
    document.querySelectorAll('[data-wa]').forEach(el=>{el.href='https://wa.me/91'+(s.whatsapp||s.phone||'7384732179')});
    document.querySelectorAll('[data-tel]').forEach(el=>{el.href='tel:'+(s.phone||'7384732179')});
  }
  function toast(msg){const t=document.querySelector('.toast')||document.body.appendChild(Object.assign(document.createElement('div'),{className:'toast'})); t.textContent=msg; t.style.display='block'; setTimeout(()=>t.style.display='none',3000)}
  function cleanPhone(value){
    const digits=String(value||'').replace(/\D/g,'');
    if(digits.length===10) return '91'+digits;
    return digits || '917384732179';
  }
  function enquiryWhatsappUrl(q){
    const s=db.settings||{};
    const to=cleanPhone(s.whatsapp||s.phone||'7384732179');
    const rows=[
      ['Name',q.name],
      ['Phone / WhatsApp',q.phone],
      ['Email',q.email],
      ['Destination',q.destination],
      ['Package',q.package],
      ['Hotel',q.hotel],
      ['Travel Date',q.date],
      ['Guests',q.pax],
      ['Budget',q.budget],
      ['Message',q.message]
    ].filter(([,v])=>String(v||'').trim());
    const text=['Hi TOURIM, I submitted a travel enquiry.','',...rows.map(([k,v])=>`${k}: ${v}`),'',`Query ID: ${q.id}`].join('\n');
    return `https://wa.me/${to}?text=${encodeURIComponent(text)}`;
  }
  function card(type,item){
    if(type==='hotel') return `<article class="hotel-card lift-card"><div class="card-img" ${bgStyle(item)}>${cardImage(item,item.name)}<span class="badge purple">${esc(item.category)}</span></div><div class="card-body"><h3>${esc(item.name)}</h3><p>${esc(item.amenities)}</p><div class="meta"><span>${esc(item.destination)} - ${esc(item.meal)}</span><b>${money(item.price)}/night</b></div><a class="btn teal" href="contact.html?hotel=${encodeURIComponent(item.name)}">Check Availability</a></div></article>`;
    if(type==='package') return `<article class="package-card lift-card package-click-card" tabindex="0" role="link" aria-label="View details for ${esc(item.title)}" data-package-id="${esc(item.id)}" data-detail-url="package-detail.html?id=${esc(item.id)}"><div class="card-img" ${bgStyle(item)}>${cardImage(item,item.title)}<span class="badge orange">${esc(item.category)}</span><div class="card-img-title"><b>${esc(item.title)}</b><span>${esc(item.destination || 'TOURIM package')}</span></div></div><div class="card-body"><h3>${esc(item.title)}</h3><p>${esc(item.short)}</p><div class="meta"><span>${esc(item.duration)}</span><b>${money(item.price)}</b></div><div class="action-row"><a class="btn teal" href="package-detail.html?id=${esc(item.id)}">View Details</a><a class="btn ghost" href="contact.html?package=${encodeURIComponent(item.title)}">Enquire</a></div></div></article>`;
    if(type==='destination') return `<article class="destination-card lift-card"><div class="card-img" ${bgStyle(item)}>${cardImage(item,item.name)}<span class="badge green">${esc(item.country)}</span></div><div class="card-body"><h3>${esc(item.name)}</h3><p>${esc(item.short)}</p><div class="meta"><span>${esc(item.region)}</span><b>${esc(item.bestTime)}</b></div><div class="action-row"><a class="btn teal" href="destination-detail.html?id=${esc(item.id)}">Explore</a><a class="btn ghost" href="packages.html?destination=${encodeURIComponent(item.name)}">Packages</a></div></div></article>`;
    if(type==='blog') return `<article class="blog-card lift-card"><div class="card-img" ${bgStyle(item)}>${cardImage(item,item.title)}<span class="badge">${esc(item.category)}</span></div><div class="card-body"><h3>${esc(item.title)}</h3><p>${esc(item.excerpt)}</p><div class="meta"><span>${esc(item.author)}</span><b>Guide</b></div><a class="btn teal" href="blog-detail.html?id=${esc(item.id)}">Read Blog</a></div></article>`;
  }
  function offerCard(o){
    const status=String(o.status||'active').toLowerCase();
    const statusText=status==='active'?'Active Offer':status.charAt(0).toUpperCase()+status.slice(1);
    const waNumber=(db.settings||{}).whatsapp || (db.settings||{}).phone || '7384732179';
    const waText=encodeURIComponent(`I want to claim TOURIM offer: ${o.title} | Coupon: ${o.coupon || 'N/A'}`);
    return `<article class="offer-card premium-offer lift-card" data-offer-id="${esc(o.id)}">
      <div class="offer-media" ${bgStyle(o)}>
        ${cardImage(o,o.title)}
        <span class="offer-status ${esc(status)}">${esc(statusText)}</span>
        <span class="offer-save">${esc(o.discount || 'Limited Deal')}</span>
      </div>
      <div class="offer-body">
        <div class="offer-kicker">${esc(o.tag || o.destination || 'TOURIM Special')}</div>
        <h3>${esc(o.title)}</h3>
        <p>${esc(o.description)}</p>
        <div class="offer-meta-line"><span>Destination</span><b>${esc(o.destination || 'Selected packages')}</b></div>
        <div class="offer-meta-line"><span>Validity</span><b>${esc(o.validity || 'Limited time')}</b></div>
        <div class="coupon-box"><span>Coupon Code</span><strong>${esc(o.coupon || 'ASKTOURIM')}</strong></div>
        <div class="offer-actions"><a class="btn primary" href="contact.html?offer=${encodeURIComponent(o.title)}">Claim Offer</a><a class="btn ghost" target="_blank" href="https://wa.me/91${esc(waNumber)}?text=${waText}">WhatsApp</a></div>
      </div>
    </article>`;
  }

  function renderGrid(type, arr){
    const grid=document.querySelector('[data-grid]'); if(!grid) return;
    grid.innerHTML=arr.map(x=>card(type,x)).join('') || `<div class="soft-card"><h3>No items found</h3><p>Try another search or contact TOURIM for a custom package.</p></div>`;
    if(type==='package') bindPackageCards();
    if(type==='package') highlightPackageCard();
  }
  function bindPackageCards(){
    document.querySelectorAll('.package-click-card').forEach(card=>{
      if(card.dataset.clickBound) return;
      card.dataset.clickBound='true';
      const open=()=>{ if(card.dataset.detailUrl) location.href=card.dataset.detailUrl; };
      card.addEventListener('click', e=>{
        if(e.target.closest('a,button,input,select,textarea,label')) return;
        open();
      });
      card.addEventListener('keydown', e=>{
        if(e.target.closest('a,button,input,select,textarea,label')) return;
        if(e.key==='Enter' || e.key===' '){ e.preventDefault(); open(); }
      });
    });
  }
  function highlightPackageCard(){
    const id=qs.get('highlight');
    if(!id) return;
    const card=document.querySelector(`[data-package-id="${CSS.escape(id)}"]`);
    if(!card) return;
    card.classList.add('package-highlight');
    setTimeout(()=>card.scrollIntoView({behavior:'smooth',block:'center'}),120);
  }
  function highlightOfferCard(){
    const highlight=qs.get('highlight');
    if(!highlight) return;
    let card=document.querySelector(`[data-offer-id="${CSS.escape(highlight)}"]`);
    if(!card){
      const cards=document.querySelectorAll('.offer-card');
      cards.forEach(c=>{
        const title=c.querySelector('h3');
        if(title && title.textContent.toLowerCase().includes(highlight.toLowerCase())) card=c;
      });
    }
    if(!card && document.querySelector('.offer-card')) card=document.querySelector('.offer-card');
    if(!card) return;
    card.classList.add('offer-highlight');
    setTimeout(()=>card.scrollIntoView({behavior:'smooth',block:'center'}),120);
    setTimeout(()=>toast('Here is your selected offer!'), 600);
  }
  function filterList(list, keys){
    const q=(document.querySelector('#pageSearch')?.value||qs.get('q')||'').toLowerCase();
    const dest=(qs.get('destination')||'').toLowerCase();
    return list.filter(item=>{
      const hay=keys.map(k=>item[k]).join(' ').toLowerCase();
      const byQ=!q || hay.includes(q);
      const byDest=!dest || hay.includes(dest);
      return byQ && byDest;
    });
  }
  function setupSearch(type, list, keys){
    const input=document.querySelector('#pageSearch'); if(!input) return;
    input.addEventListener('input',()=>{
      const filtered=filterList(list, keys);
      if(type==='offers'){ const grid=document.querySelector('[data-grid]'); if(grid) grid.innerHTML=filtered.map(o=>offerCard(o)).join('') || `<div class="soft-card"><h3>No active offers found</h3><p>Try another search or ask TOURIM for a custom discount.</p></div>`; return; }
      renderGrid(type, filtered);
    });
  }
  function renderDetail(kind, list){
    const id=qs.get('id');
    const item=list.find(x=>x.id===id) || list[0];
    const wrap=document.querySelector('[data-detail]'); if(!wrap || !item) return;
    if(kind==='package'){
      const routeText=String(item.route||item.itinerary||'').trim();
      const routeStops=routeText.split(/\s*(?:→|->|>)\s*/).filter(Boolean);
      const routeHtml=routeStops.length>1 ? `<div class="route-path">${routeStops.map(stop=>`<span>${esc(stop)}</span>`).join('')}</div>` : `<p>${esc(routeText)}</p>`;
      wrap.innerHTML=`<div class="detail-hero" ${bgStyle(item)}><span class="badge orange">${esc(item.category)}</span><h1>${esc(item.title)}</h1><p>${esc(item.short)}</p></div><div class="detail-grid"><main class="panel"><h2>Tour Route</h2>${routeHtml}<h2>Day-wise plan</h2><p>${esc(item.itinerary)}</p><h2>Inclusions</h2><p>${esc(item.inclusions)}</p><h2>Exclusions</h2><p>${esc(item.exclusions)}</p><h2>Why book with TOURIM?</h2><ul><li>Custom route and budget planning</li><li>Hotel, vehicle and sightseeing coordination</li><li>Direct support before and during travel</li><li>Transparent inclusions and exclusions</li></ul></main><aside class="booking-box"><h3>${money(item.price)}</h3><p>Starting price per person / package basis as applicable.</p><div class="mini-line"><span>Duration</span><b>${esc(item.duration)}</b></div><div class="mini-line"><span>Destination</span><b>${esc(item.destination)}</b></div><div class="mini-line"><span>Route Stops</span><b>${routeStops.length || 'Custom'}</b></div><a class="btn primary" href="contact.html?package=${encodeURIComponent(item.title)}">Get Quote</a><a class="btn teal" data-wa href="https://wa.me/91${(db.settings||{}).whatsapp||'7384732179'}?text=${encodeURIComponent('I want details for '+item.title)}">WhatsApp</a></aside></div>`;
    }
    if(kind==='destination'){
      const packs=live(db.packages).filter(p=>String(p.destination).toLowerCase().includes(String(item.name).toLowerCase()));
      wrap.innerHTML=`<div class="detail-hero" ${bgStyle(item)}><span class="badge green">${esc(item.region)}</span><h1>${esc(item.name)}</h1><p>${esc(item.short)}</p></div><div class="detail-grid"><main class="panel"><h2>Destination Overview</h2><p>${esc(item.short)} TOURIM can customise hotels, transfers, sightseeing and budget for this destination.</p><h2>Best Time</h2><p>${esc(item.bestTime)}</p><h2>Related Packages</h2><div class="cards-grid compact-grid">${packs.map(p=>card('package',p)).join('') || '<p>No package listed yet. Request a custom quote.</p>'}</div></main><aside class="booking-box"><h3>Plan ${esc(item.name)}</h3><p>Share date, guests and budget to receive a custom itinerary.</p><a class="btn primary" href="contact.html?destination=${encodeURIComponent(item.name)}">Get Custom Quote</a></aside></div>`;
    }
    if(kind==='blog'){
      wrap.innerHTML=`<div class="detail-hero" ${bgStyle(item)}><span class="badge">${esc(item.category)}</span><h1>${esc(item.title)}</h1><p>${esc(item.excerpt)}</p></div><article class="panel article"><p>${esc(item.excerpt)}</p><p>TOURIM recommends checking travel dates, hotel category, meal plan, transfers, sightseeing route, permit requirements and seasonal weather before booking. For the best quotation, share your exact travel date, number of guests, budget and hotel preference.</p><h2>TOURIM Planning Checklist</h2><ul><li>Choose destination and route.</li><li>Confirm number of travellers and rooms.</li><li>Decide hotel category and meal plan.</li><li>Check transport type and sightseeing points.</li><li>Confirm inclusion, exclusion and payment terms.</li></ul><a class="btn primary" href="contact.html">Ask TOURIM for help</a></article>`;
    }
    settings();
  }
  function renderContact(){
    const form=document.querySelector('#contactForm'); if(!form) return;
    ['package','destination','hotel'].forEach(k=>{ if(qs.get(k) && form.elements[k]) form.elements[k].value=qs.get(k); });
    if(qs.get('service') && form.elements.message) form.elements.message.value = 'I want details for this TOURIM service: ' + qs.get('service');
    if(qs.get('offer') && form.elements.message) form.elements.message.value = 'I want to claim this offer: ' + qs.get('offer');
    form.addEventListener('submit', async e=>{
      e.preventDefault();
      const q=Object.fromEntries(new FormData(form).entries());
      q.id='#Q'+Math.floor(1000+Math.random()*9000); q.source='Website Contact Page'; q.status='New'; q.country='India'; q.created_at=new Date().toISOString();
      if(window.tourimSubmitRemoteEnquiry) await window.tourimSubmitRemoteEnquiry(q);
      const db2=tourimGetDB(); db2.queries=db2.queries||[]; db2.queries.unshift(q); db2.analytics=db2.analytics||{}; db2.analytics.enquiries=(db2.analytics.enquiries||0)+1; db2.notifications=db2.notifications||[]; db2.notifications.unshift({id:'query_'+String(q.id).replace(/[^A-Za-z0-9_-]/g,''),queryId:q.id,title:`New query received from ${q.name||'Customer'}`,text:`${q.package||q.destination||'Custom Package'}${q.phone?' - '+q.phone:''}`,created_at:new Date().toISOString(),read:false}); tourimSaveDB(db2);
      const waUrl=enquiryWhatsappUrl(q);
      form.reset(); toast('Thank you! Opening WhatsApp with your enquiry details.');
      setTimeout(()=>{ location.href=waUrl; }, 450);
    });
  }
  function renderHomeExtras(){
    const faqs=document.querySelector('[data-faqs]'); if(faqs) faqs.innerHTML=(db.faqs||[]).map(f=>`<details class="faq"><summary>${esc(f.q)}</summary><p>${esc(f.a)}</p></details>`).join('');
    const testi=document.querySelector('[data-testimonials]'); if(testi) testi.innerHTML=(db.testimonials||[]).map(t=>`<div class="soft-card testimonial"><b>${esc(t.name)}</b><span>${esc(t.place)} - ★ ${esc(t.rating)}</span><p>${esc(t.text)}</p></div>`).join('');
    const gal=document.querySelector('[data-gallery]'); if(gal){ gal.classList.add('photo-gallery'); gal.innerHTML=(db.gallery||[]).slice(0,8).map(g=>galleryCard(g,false)).join(''); bindPhotoLightbox(); }
  }
  function renderCurrentPage(){
    settings(); renderHomeExtras(); renderContact();
    const page=document.body.dataset.page;
    if(page==='packages'){const list=filterList(live(db.packages),['title','destination','category','short']); renderGrid('package',list); setupSearch('package',live(db.packages),['title','destination','category','short']);}
    if(page==='destinations'){const list=filterList(live(db.destinations),['name','country','region','short']); renderGrid('destination',list); setupSearch('destination',live(db.destinations),['name','country','region','short']);}
    if(page==='hotels'){const list=filterList(live(db.hotels),['name','destination','category','amenities']); renderGrid('hotel',list); setupSearch('hotel',live(db.hotels),['name','destination','category','amenities']);}
    if(page==='blogs'){const list=filterList(live(db.blogs),['title','category','author','excerpt']); renderGrid('blog',list); setupSearch('blog',live(db.blogs),['title','category','author','excerpt']);}
    if(page==='offers'){const list=filterList(live(db.offers),['title','description','coupon','destination','tag','status']); const grid=document.querySelector('[data-grid]'); if(grid) grid.innerHTML=list.map(o=>offerCard(o)).join('') || `<div class="soft-card"><h3>No active offers found</h3><p>Try another search or ask TOURIM for a custom discount.</p></div>`; setupSearch('offers',live(db.offers),['title','description','coupon','destination','tag','status']); highlightOfferCard();}
    if(page==='gallery'){const grid=document.querySelector('[data-gallery]'); if(grid){ grid.classList.add('photo-gallery'); grid.innerHTML=(db.gallery||[]).map(g=>galleryCard(g,true)).join(''); bindPhotoLightbox(); }}
    if(page==='package-detail') renderDetail('package',live(db.packages));
    if(page==='destination-detail') renderDetail('destination',live(db.destinations));
    if(page==='blog-detail') renderDetail('blog',live(db.blogs));
  }
  document.addEventListener('DOMContentLoaded',()=>{ renderCurrentPage(); });
  window.addEventListener('tourimdb:ready',e=>{ db=e.detail||tourimGetDB(); renderCurrentPage(); }); // TOURIM page rerender after hosted DB sync
})();

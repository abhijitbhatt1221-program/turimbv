const db = tourimGetDB();
if(new URLSearchParams(location.search).get('preview')==='draft' && db.draftHomepage){ db.homepage = db.draftHomepage; }
const HOME_POPULAR_RULES = [
  /kashmir/i,
  /manali|himachal/i,
  /goa/i,
  /rajasthan/i,
  /kerala/i,
  /darjeeling/i,
  /gangtok|sikkim/i,
  /andaman/i,
  /ladakh/i,
  /meghalaya/i
];
function homePopularPackages(){
  const published = db.packages.filter(p=>p.status==='published');
  return HOME_POPULAR_RULES.map(rule=>{
    return published.find(pkg=>rule.test([pkg.title,pkg.destination].filter(Boolean).join(' ')));
  }).filter(Boolean);
}
let filteredPackages = homePopularPackages();
function mediaPath(value){
  const v=(value||'').trim();
  if(!v) return '';
  if(/gradient\(/.test(v)) return v;
  if(/^url\(/.test(v)) return v;
  const absolute=new URL((typeof tourimAssetUrl==='function'?tourimAssetUrl(v):v), location.href).href;
  return `url('${absolute.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}')`;
}
function assetImage(item){
  const raw=String((item&&item.image)||'').trim();
  if(raw && !/^linear-gradient|^radial-gradient/i.test(raw) && !(/^data:image/i.test(raw) && raw.length > 60000)) return tourimAssetUrl(raw);
  return tourimAssetUrl(tourimFallbackImageForItem(item));
}
function itemMediaPath(item){return mediaPath(assetImage(item))}
function itemMediaStyle(item){
  const css=itemMediaPath(item);
  return `background-image:${css};--img:${css}`;
}
function itemImage(item, alt){
  const src=assetImage(item).replace(/"/g,'&quot;');
  const fallback=tourimAssetUrl(tourimFallbackImageForItem(item)).replace(/"/g,'&quot;');
  return `<img src="${src}" data-fallback-src="${fallback}" alt="${String(alt||'TOURIM travel photo').replace(/"/g,'&quot;')}" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;display:block;z-index:1" onerror="if(this.src!==this.dataset.fallbackSrc){this.src=this.dataset.fallbackSrc}else{this.remove()}">`;
}
function track(event_type, item_type='', item_id=''){
  const current = tourimGetDB();
  const event = {id:uid('evt'), event_type, item_type, item_id, page_url:location.href, visitor_id:getVisitorId(), session_id:sessionStorage.getItem('tourim_session') || uid('session'), device:navigator.userAgent.includes('Mobile')?'Mobile':'Desktop', browser:navigator.userAgent.split(' ')[0], referrer:document.referrer, created_at:new Date().toISOString()};
  sessionStorage.setItem('tourim_session', event.session_id);
  if(event_type==='page_view'){
    current.analytics = current.analytics || {};
    current.analytics.visitors = (current.analytics.visitors || 0) + 1;
    return;
  }
  current.events = current.events || [];
  current.events.push(event);
  if(['package_view','destination_view','whatsapp_clicked','phone_clicked','enquiry_form_opened'].includes(event_type)) current.analytics.interested += 1;
  tourimSaveDB(current);
  if(window.tourimTrackRemote){
    const send=()=>window.tourimTrackRemote(event);
    if(typeof tourimRunWhenIdle==='function') tourimRunWhenIdle(send, 2000);
    else setTimeout(send, 900);
  }
}
function cleanWhatsappNumber(value){
  const digits=String(value||'').replace(/\D/g,'');
  if(digits.length===10) return '91'+digits;
  return digits || '917384732179';
}
function quickQuoteWhatsappUrl(query){
  const settings=(tourimGetDB().settings||{});
  const to=cleanWhatsappNumber(settings.whatsapp||settings.phone||'7384732179');
  const rows=[
    ['Name',query.name],
    ['Phone / WhatsApp',query.phone],
    ['Email',query.email],
    ['Destination',query.destination],
    ['Package',query.package],
    ['Travel Date',query.date],
    ['Guests',query.pax],
    ['Message / Budget',query.note]
  ].filter(([,v])=>String(v||'').trim());
  const text=['Hi TOURIM, I submitted a quick quotation request.','',...rows.map(([k,v])=>`${k}: ${v}`),'',`Query ID: ${query.id}`].join('\n');
  return `https://wa.me/${to}?text=${encodeURIComponent(text)}`;
}
function getVisitorId(){
  let id=localStorage.getItem('tourim_visitor_id');
  if(!id){id=uid('visitor'); localStorage.setItem('tourim_visitor_id', id)}
  return id;
}
function cssMediaValue(value){
  const v=(value||'').trim();
  if(!v) return '';
  if(/gradient\(/.test(v)) return v;
  if(/^url\(/.test(v)) return v;
  return `url("${new URL((typeof tourimAssetUrl==='function'?tourimAssetUrl(v):v), location.href).href.replace(/"/g,'\\"')}")`;
}
function applySectionMedia(el, cssVarName, value, className){
  if(!el) return;
  const cssValue = cssMediaValue(value);
  if(cssValue){
    el.style.setProperty(cssVarName, cssValue);
    el.classList.add(className);
  }else{
    el.style.removeProperty(cssVarName);
    el.classList.remove(className);
  }
}
function applySettings(){
  const s=db.settings,h=db.homepage;
  document.title = `${s.websiteName} - ${s.tagline}`;
  const phoneLink=document.getElementById('phoneLink');
  const whatsappNumber=(s.whatsapp||s.phone||'7384732179').replace(/\D/g,'');
  phoneLink.textContent = s.phone||whatsappNumber;
  phoneLink.href = 'https://wa.me/91' + whatsappNumber + '?text=' + encodeURIComponent('Hi TOURIM, I want to plan a tour');
  phoneLink.target = '_blank';
  phoneLink.rel = 'noopener';
  document.getElementById('heroTitle').textContent=h.heroTitle;
  document.getElementById('heroSubtitle').textContent=h.heroSubtitle;
  document.getElementById('heroPrimary').textContent=h.heroPrimary;
  document.getElementById('heroSecondary').textContent=h.heroSecondary;
  document.getElementById('offerTitle').textContent=h.offerTitle;
  document.getElementById('offerSubtitle').textContent=h.offerSubtitle;
  
  const offerBtn = document.getElementById('offerButton');
  if(offerBtn){
    offerBtn.textContent = h.offerButton;
    const offerUrl = `offers.html?highlight=${encodeURIComponent(h.offerTitle)}`;
    if(offerBtn.tagName === 'A') offerBtn.href = offerUrl;
    else offerBtn.onclick = () => location.href = offerUrl;
  }

  document.getElementById('footerTagline').textContent=s.tagline;
  document.getElementById('footerContact').innerHTML=`Phone: ${s.phone}${s.phone2?' / '+s.phone2:''}<br>Email: ${s.email}`;
  document.getElementById('footerAddress').textContent=s.address;
  document.getElementById('footerCredit').textContent=s.footerCredit;
  applySectionMedia(document.getElementById('heroSection'),'--hero-media',h.heroImage,'has-custom-media');
  applySectionMedia(document.querySelector('.offer-banner'),'--offer-media',h.offerImage,'has-custom-media');
  ['hero','offer','packages','destinations','hotels','blogs'].forEach(key=>{
    const map={hero:'heroSection',offer:'offerSection',packages:'packages',destinations:'destinations',hotels:'hotels',blogs:'blogs'};
    const show = key==='hero'?h.showHero:key==='offer'?h.showOffer:key==='packages'?h.showPackages:key==='destinations'?h.showDestinations:key==='hotels'?h.showHotels:h.showBlogs;
    document.getElementById(map[key])?.classList.toggle('hidden', !show);
  });
}
function renderPackages(){
  const grid=document.getElementById('packageGrid');
  grid.innerHTML=filteredPackages.map(p=>`
    <article class="package-card soft-card home-package-link" data-package-id="${p.id}" tabindex="0" role="link" aria-label="Open ${p.title} on packages page">
      <div class="card-img" style="${itemMediaStyle(p)}">${itemImage(p,p.title)}<span class="badge orange" style="position:absolute;left:14px;top:14px;z-index:2">${p.category}</span><div class="card-img-title"><b>${p.title}</b><span>${p.destination || 'TOURIM package'}</span></div></div>
      <div class="card-content">
        <h3>${p.title}</h3><p>${p.short||''}</p>
        <div class="meta-row"><span class="badge">${p.destination}</span><span class="badge green">${p.duration}</span></div>
        <div class="price-row"><span class="price">${money(p.price)}</span><button class="btn teal" onclick="event.stopPropagation(); openPackage('${p.id}')">View Details</button></div>
      </div>
    </article>`).join('') || '<p>No packages found.</p>';
  grid.querySelectorAll('.home-package-link').forEach(card=>{
    const open=()=>{ location.href='packages.html?highlight='+encodeURIComponent(card.dataset.packageId); };
    card.addEventListener('click',open);
    card.addEventListener('keydown',e=>{if(e.key==='Enter'){open()}});
  });
  requestAnimationFrame(()=>refreshSiteMotion(grid));
}
function renderDestinations(){
  const grid=document.getElementById('destinationGrid');
  grid.innerHTML=db.destinations.filter(d=>d.status==='published').map(d=>`
    <article class="destination-card soft-card">
      <div class="card-img" style="${itemMediaStyle(d)}">${itemImage(d,d.name)}</div>
      <div class="card-content"><h3>${d.name}</h3><p>${d.short}</p><div class="meta-row"><span class="badge">${d.country}</span><span class="badge green">${d.bestTime}</span></div><button class="btn primary" onclick="track('destination_view','destination','${d.id}'); location.hash='quote'; document.querySelector('[name=destination]').value='${d.name}'">Plan ${d.name}</button></div>
    </article>`).join('');
  const select=document.getElementById('quoteDestination');
  select.innerHTML='<option value="">Select destination</option>'+db.destinations.filter(d=>d.status==='published').map(d=>`<option>${d.name}</option>`).join('');
  requestAnimationFrame(()=>refreshSiteMotion(grid));
}
function renderHotels(){
  const grid=document.getElementById('hotelGrid');
  grid.innerHTML=db.hotels.filter(h=>h.status==='published').map(h=>`
    <article class="hotel-card soft-card"><div class="card-img" style="${itemMediaStyle(h)}">${itemImage(h,h.name)}<span class="badge" style="position:absolute;left:14px;top:14px;z-index:2">${h.category}</span></div><div class="card-content"><h3>${h.name}</h3><p>${h.amenities}</p><div class="meta-row"><span class="badge green">${h.destination}</span><span class="badge orange">${h.meal}</span></div><div class="price-row"><span class="price">${money(h.price)}/night</span><a href="#quote" class="btn ghost">Ask Rate</a></div></div></article>`).join('');
  requestAnimationFrame(()=>refreshSiteMotion(grid));
}
function renderBlogs(){
  const grid=document.getElementById('blogGrid');
  grid.innerHTML=db.blogs.filter(b=>b.status==='published').map(b=>`
    <article class="blog-card soft-card"><div class="card-img" style="${itemMediaStyle(b)}">${itemImage(b,b.title)}</div><div class="card-content"><span class="badge purple">${b.category}</span><h3>${b.title}</h3><p>${b.excerpt}</p><button class="btn ghost" onclick="track('blog_view','blog','${b.id}'); toast('Blog detail page can be connected here')">Read Guide</button></div></article>`).join('');
  requestAnimationFrame(()=>refreshSiteMotion(grid));
}
function openPackage(id){
  const p=db.packages.find(x=>x.id===id);
  if(!p) return;
  track('package_view','package',id);
  document.getElementById('modalBadge').textContent=p.category;
  document.getElementById('modalTitle').textContent=p.title;
  document.getElementById('modalBody').innerHTML=`<div class="modal-package-media" style="${itemMediaStyle(p)}">${itemImage(p,p.title)}</div><div class="modal-package-content"><p>${p.short}</p><div class="meta-row"><span class="badge">${p.destination}</span><span class="badge green">${p.duration}</span><span class="badge orange">${money(p.price)} per person</span></div><h3>Itinerary</h3><p>${p.itinerary}</p><h3>Inclusions</h3><p>${p.inclusions}</p><h3>Exclusions</h3><p>${p.exclusions}</p><a href="#quote" class="btn primary" onclick="document.getElementById('detailModal').style.display='none'; document.querySelector('[name=destination]').value='${p.destination}'">Send Enquiry</a></div>`;
  document.getElementById('detailModal').style.display='grid';
}
function setupSearch(){
  const run=()=>{
    const q=document.getElementById('searchInput').value.trim().toLowerCase();
    const c=document.getElementById('categoryFilter').value;
    filteredPackages=homePopularPackages().filter(p=>p.status==='published' && (!q || p.title.toLowerCase().includes(q) || p.destination.toLowerCase().includes(q)) && (!c || p.category===c));
    renderPackages();
    track('search_used','search',q||c);
  };
  document.getElementById('searchBtn').addEventListener('click',run);
  document.getElementById('searchInput').addEventListener('keydown',e=>{if(e.key==='Enter') run()});
}
function setupQuoteForm(){
  document.getElementById('quoteForm').addEventListener('focusin',()=>track('enquiry_form_opened'),{once:true});
  document.getElementById('quoteForm').addEventListener('submit',e=>{
    e.preventDefault();
    const data=Object.fromEntries(new FormData(e.target).entries());
    const current=tourimGetDB();
    const id='#Q'+Math.floor(1000+Math.random()*9000);
    const query={id,name:data.name,phone:data.phone,email:data.email,package:'Custom Package',destination:data.destination,date:data.date,pax:data.pax,budget:'Not shared',country:'India',source:'Website Quick Quote',status:'New',note:data.message,created_at:new Date().toISOString()};
    current.queries.unshift(query);
    current.analytics.enquiries+=1; current.analytics.interested+=1;
    current.notifications=current.notifications||[];
    current.notifications.unshift({id:'query_'+String(id).replace(/[^A-Za-z0-9_-]/g,''),queryId:id,title:`New query received from ${data.name||'Customer'}`,text:`${data.destination||'Custom Package'}${data.phone?' - '+data.phone:''}`,created_at:new Date().toISOString(),read:false});
    tourimSaveDB(current);
    if(window.tourimSubmitRemoteEnquiry) window.tourimSubmitRemoteEnquiry(query);
    track('enquiry_submitted','query',id);
    const waUrl=quickQuoteWhatsappUrl(query);
    e.target.reset();
    toast('Thank you! Opening WhatsApp with your quotation details.');
    setTimeout(()=>{ location.href=waUrl; },450);
  });
}
function prefersReducedMotion(){return window.matchMedia('(prefers-reduced-motion: reduce)').matches}
function setupMotion(){
  document.body.classList.add('reveal-ready');
  if(!prefersReducedMotion() && 'IntersectionObserver' in window){
    window.siteMotionObserver = new IntersectionObserver(entries=>{
      entries.forEach(entry=>{
        if(entry.isIntersecting){
          entry.target.classList.add('is-visible');
          window.siteMotionObserver.unobserve(entry.target);
        }
      });
    },{threshold:.12,rootMargin:'0px 0px -8% 0px'});
  }
  refreshSiteMotion();
  setupActiveNav();
}
function refreshSiteMotion(scope=document){
  if(!document.body.classList.contains('reveal-ready')) return;
  const targets = scope.querySelectorAll('.section-title,.offer-banner,.trust-item,.package-card,.destination-card,.hotel-card,.blog-card,.search-panel,.glass-form,.footer-grid>div');
  targets.forEach((el,i)=>{
    if(!el.classList.contains('reveal-target')){
      el.classList.add('reveal-target');
      el.style.setProperty('--reveal-delay', `${Math.min(i*45,260)}ms`);
    }
    if(prefersReducedMotion() || !window.siteMotionObserver) el.classList.add('is-visible');
    else window.siteMotionObserver.observe(el);
  });
  setupTiltCards(scope);
}
function setupTiltCards(scope=document){
  if(prefersReducedMotion()) return;
  scope.querySelectorAll('.package-card,.destination-card,.hotel-card,.blog-card').forEach(card=>{
    if(card.dataset.tiltReady) return;
    card.dataset.tiltReady='true';
    card.addEventListener('pointermove',e=>{
      const rect=card.getBoundingClientRect();
      const x=(e.clientX-rect.left)/rect.width-.5;
      const y=(e.clientY-rect.top)/rect.height-.5;
      card.style.setProperty('--tilt-x', `${x*7}deg`);
      card.style.setProperty('--tilt-y', `${-y*7}deg`);
    });
    card.addEventListener('pointerleave',()=>{
      card.style.removeProperty('--tilt-x');
      card.style.removeProperty('--tilt-y');
    });
  });
}
function setupActiveNav(){
  const links=[...document.querySelectorAll('.nav-links a[href^="#"]')];
  const sections=links.map(a=>document.querySelector(a.getAttribute('href'))).filter(Boolean);
  if(!('IntersectionObserver' in window) || !sections.length) return;
  const navObserver=new IntersectionObserver(entries=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        links.forEach(a=>a.classList.toggle('active', a.getAttribute('href')==='#'+entry.target.id));
      }
    });
  },{threshold:.22,rootMargin:'-18% 0px -58% 0px'});
  sections.forEach(section=>navObserver.observe(section));
}
const mobileMenuBtn=document.getElementById('mobileMenuBtn');
mobileMenuBtn?.addEventListener('click',()=>{
  const nav=document.getElementById('navLinks');
  const open=nav.classList.toggle('is-open');
  mobileMenuBtn.setAttribute('aria-expanded', open?'true':'false');
});
document.querySelectorAll('.nav-links a').forEach(link=>link.addEventListener('click',()=>document.getElementById('navLinks')?.classList.remove('is-open')));
document.getElementById('closeModal').addEventListener('click',()=>document.getElementById('detailModal').style.display='none');
document.getElementById('detailModal').addEventListener('click',e=>{if(e.target.id==='detailModal') e.currentTarget.style.display='none'});
applySettings(); renderPackages(); renderDestinations(); renderHotels(); renderBlogs(); setupSearch(); setupQuoteForm(); setupMotion(); track('page_view');

window.addEventListener('tourimdb:ready',()=>{
  const fresh=tourimGetDB();
  Object.keys(db).forEach(k=>delete db[k]);
  Object.assign(db, fresh);
  if(new URLSearchParams(location.search).get('preview')==='draft' && db.draftHomepage){ db.homepage = db.draftHomepage; }
  filteredPackages = homePopularPackages();
  applySettings(); renderPackages(); renderDestinations(); renderHotels(); renderBlogs();
});

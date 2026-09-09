let prodModules = {};
function prodToast(msg){ const t=document.querySelector('.toast'); if(!t){alert(msg);return;} t.textContent=msg; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),2600); }
async function prodApi(action, payload={}, options={}){
  const url=tourimApiUrl('production.php')+'?action='+encodeURIComponent(action);
  let res;
  if(options.formData){ res=await fetch(url,{method:'POST',body:options.formData,credentials:'include'}); }
  else { res=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload),credentials:'include'}); }
  return await res.json();
}
function localFallbackModules(){ const db=tourimGetDB(); return {users:db.users||[],roles:db.roles||[],payments:db.payments||[],bookings:db.bookings||[],invoices:db.invoices||[],vouchers:db.vouchers||[],media:db.media||[],whatsappTemplates:db.whatsappTemplates||[],seoRoutes:db.seoRoutes||[],auditLogs:db.auditLogs||[],analytics:db.analytics||{},events:db.events||[]}; }
async function loadProductionTools(){
  try{ const out=await prodApi('get'); prodModules=out.ok?out.modules:localFallbackModules(); if(!out.ok) prodToast('Server requires admin login; showing local fallback data.'); }
  catch(e){ prodModules=localFallbackModules(); prodToast('Offline/local fallback mode active.'); }
  renderProductionTools();
}
function renderProductionTools(){
  const m=prodModules;
  document.getElementById('prodUsers').textContent=(m.users||[]).length;
  document.getElementById('prodPayments').textContent=(m.payments||[]).length;
  document.getElementById('prodBookings').textContent=(m.bookings||[]).length;
  document.getElementById('prodDocs').textContent=(m.invoices||[]).length+(m.vouchers||[]).length;
  document.getElementById('prodMedia').textContent=(m.media||[]).length;
  document.getElementById('prodEvents').textContent=(m.events||[]).length;
  document.getElementById('prodUsersList').innerHTML=(m.users||[]).map(u=>`<div class="mini-item"><span class="icon purple">👤</span><div><b>${u.name||u.username}</b><span>${u.role} · ${u.status}</span></div></div>`).join('')||'<p class="muted-text">No users yet.</p>';
  document.getElementById('prodMediaList').innerHTML=(m.media||[]).slice(0,8).map(x=>`<div class="mini-item"><span class="icon teal">🖼</span><div><b>${x.type||'media'}</b><span>${x.file_url||''}</span></div></div>`).join('')||'<p class="muted-text">No server uploads yet.</p>';
  document.getElementById('prodPaymentList').innerHTML=(m.payments||[]).slice(0,8).map(p=>`<div class="mini-item"><span class="icon orange">₹</span><div><b>${p.customer} · ₹${p.amount}</b><span>${p.package} · ${p.status}</span></div></div>`).join('')||'<p class="muted-text">No payments yet.</p>';
  document.getElementById('prodBookingList').innerHTML=(m.bookings||[]).slice(0,6).map(b=>`<div class="mini-item"><span class="icon green">🎫</span><div><b>${b.customer}</b><span>${b.package} · ${b.travel_date||''}</span></div></div>`).join('')||'<p class="muted-text">No bookings yet.</p>';
  const docs=[...(m.invoices||[]),...(m.vouchers||[])];
  document.getElementById('prodDocList').innerHTML=docs.slice(0,6).map(d=>`<div class="mini-item"><span class="icon">📄</span><div><b>${d.document_type||'document'} · ${d.id}</b><span>${d.url?`<a href="${d.url}" target="_blank">Open document</a>`:'Generated document'}</span></div></div>`).join('')||'<p class="muted-text">No generated documents yet.</p>';
  document.getElementById('prodWhatsappTemplates').value=JSON.stringify(m.whatsappTemplates||[],null,2);
  document.getElementById('prodSeoRoutes').value=JSON.stringify(m.seoRoutes||[],null,2);
  document.getElementById('prodAuditList').innerHTML=(m.auditLogs||[]).slice(0,20).map(a=>`<div class="mini-item"><span class="icon purple">🧾</span><div><b>${a.action}</b><span>${a.details||''} · ${a.created_at||''}</span></div></div>`).join('')||'<p class="muted-text">No audit logs yet.</p>';
}
function formObj(form){return Object.fromEntries(new FormData(form).entries());}
document.getElementById('prodUserForm').addEventListener('submit', async e=>{e.preventDefault(); const data=formObj(e.target); try{const out=await prodApi('user_save',data); if(out.ok){prodToast('User saved'); e.target.reset(); loadProductionTools(); return;}}catch(err){} const db=tourimGetDB(); db.users=db.users||[]; db.users.push({...data,id:'user_'+Date.now(),created_at:new Date().toISOString()}); tourimSaveDB(db); prodToast('User saved locally'); loadProductionTools();});
document.getElementById('prodPaymentForm').addEventListener('submit', async e=>{e.preventDefault(); const data=formObj(e.target); try{const out=await prodApi('payment_save',data); if(out.ok){prodToast('Payment saved'); e.target.reset(); loadProductionTools(); return;}}catch(err){} const db=tourimGetDB(); db.payments=db.payments||[]; db.payments.unshift({...data,id:'pay_'+Date.now(),created_at:new Date().toISOString()}); tourimSaveDB(db); prodToast('Payment saved locally'); loadProductionTools();});
document.getElementById('prodBookingForm').addEventListener('submit', async e=>{e.preventDefault(); const data=formObj(e.target); try{const out=await prodApi('booking_save',data); if(out.ok){prodToast('Booking saved'); loadProductionTools(); return;}}catch(err){} const db=tourimGetDB(); db.bookings=db.bookings||[]; db.bookings.unshift({...data,id:'book_'+Date.now(),created_at:new Date().toISOString()}); tourimSaveDB(db); prodToast('Booking saved locally'); loadProductionTools();});
document.getElementById('prodUploadForm').addEventListener('submit', async e=>{e.preventDefault(); const fd=new FormData(e.target); try{const out=await prodApi('upload',{}, {formData:fd}); if(out.ok){prodToast('Media uploaded'); e.target.reset(); loadProductionTools(); return;} prodToast(out.error||'Upload failed');}catch(err){prodToast('Server upload needs hosting/admin login.');}});
async function generateDoc(type){ const data=formObj(document.getElementById('prodBookingForm')); data.type=type; data.record={...data,id:type+'_'+Date.now()}; try{ const out=await prodApi('document_generate',data); if(out.ok){prodToast(type+' generated'); window.open(out.url,'_blank'); loadProductionTools(); return;} prodToast(out.error||'Document failed'); }catch(e){prodToast('Document generator needs server hosting/admin login.');} }
async function saveProductionSettings(){ let whatsappTemplates=[], seoRoutes=[]; try{whatsappTemplates=JSON.parse(document.getElementById('prodWhatsappTemplates').value||'[]'); seoRoutes=JSON.parse(document.getElementById('prodSeoRoutes').value||'[]');}catch(e){prodToast('Invalid JSON'); return;} try{ const out=await prodApi('save_settings',{whatsappTemplates,seoRoutes}); if(out.ok){prodToast('Settings saved'); loadProductionTools(); return;} }catch(e){} const db=tourimGetDB(); db.whatsappTemplates=whatsappTemplates; db.seoRoutes=seoRoutes; tourimSaveDB(db); prodToast('Settings saved locally'); loadProductionTools(); }
async function loadAnalyticsReport(){ try{ const out=await prodApi('analytics_report'); if(out.ok){ document.getElementById('prodAnalyticsReport').textContent=JSON.stringify(out.report,null,2); return; } }catch(e){} const m=localFallbackModules(); const byType={}; (m.events||[]).forEach(e=>{byType[e.event_type||'event']=(byType[e.event_type||'event']||0)+1;}); document.getElementById('prodAnalyticsReport').textContent=JSON.stringify({byType,totals:m.analytics},null,2); }
async function createBackup(){ try{ const out=await prodApi('backup'); if(out.ok){prodToast('Backup created'); window.open(out.url,'_blank'); return;} prodToast(out.error||'Backup failed'); }catch(e){prodToast('Backup needs server hosting/admin login.');} }
loadProductionTools();

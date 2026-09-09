function launchToast(msg){const t=document.querySelector('.toast'); if(!t){alert(msg);return;} t.textContent=msg; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),2800);}
async function setupApi(action,payload={}){const res=await fetch(tourimApiUrl('setup_wizard.php')+'?action='+encodeURIComponent(action),{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload),credentials:'include'}); return await res.json();}
function badge(pass){return pass?'<span class="badge green">PASS</span>':'<span class="badge orange">FIX</span>';}
async function loadLaunchReadiness(){
  try{
    const out=await setupApi('status');
    const r=out.readiness||{};
    document.getElementById('launchScore').textContent=(r.software_complete?'100':(r.score||0))+'%';
    document.getElementById('softwareComplete').textContent=r.software_complete?'Yes':'Check';
    document.getElementById('storageMode').textContent=(r.storage||'json').toUpperCase();
    document.getElementById('baseUrl').textContent=(r.base_url||'local').replace(/^https?:\/\//,'').slice(0,24);
    document.getElementById('readinessList').innerHTML=(r.checks||[]).map(c=>`<div class="mini-item"><span class="icon ${c.pass?'green':'orange'}">${c.pass?'✓':'!'}</span><div><b>${c.label} ${badge(c.pass)}</b><span>${c.pass?'Ready':(c.fix||'Needs attention')} · ${c.level||'required'}</span></div></div>`).join('');
    document.getElementById('externalList').innerHTML=(r.external_accounts_needed||[]).map(c=>`<div class="mini-item"><span class="icon orange">🔑</span><div><b>${c.label}</b><span>${c.fix}</span></div></div>`).join('') || '<div class="mini-item"><span class="icon green">✓</span><div><b>All external keys configured</b><span>Live external integrations can be tested from Production Tools.</span></div></div>';
  }catch(e){ launchToast('Open through PHP hosting/localhost to run the launch checker.'); }
}
document.getElementById('domainForm').addEventListener('submit',async e=>{e.preventDefault(); const data=Object.fromEntries(new FormData(e.target).entries()); try{const out=await setupApi('save_domain',data); launchToast(out.ok?'Domain settings saved':(out.error||'Could not save')); loadLaunchReadiness();}catch(err){launchToast('Admin login/server permission required.');}});
document.getElementById('externalForm').addEventListener('submit',async e=>{e.preventDefault(); const data=Object.fromEntries(new FormData(e.target).entries()); try{const out=await setupApi('save_external_placeholders',data); launchToast(out.ok?'External settings saved':(out.error||'Could not save')); loadLaunchReadiness();}catch(err){launchToast('Admin login/server permission required.');}});
loadLaunchReadiness();

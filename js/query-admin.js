let queryDb={queries:[]};
let queryVersion='';
let queriesLoaded=false;
let queryPollBusy=false;
let queryPollTimer=null;
const QUERY_POLL_MS=3000;
const $=id=>document.getElementById(id);
const esc=value=>String(value??'').replace(/[&<>"]/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch]));

function notify(message){const el=$('queryToast');el.textContent=message;el.style.display='block';clearTimeout(notify.timer);notify.timer=setTimeout(()=>el.style.display='none',2800)}
function queryList(){return Array.isArray(queryDb?.queries)?queryDb.queries:[]}
function queryApiUrl(action){return `${tourimApiUrl('db.php')}?action=${encodeURIComponent(action)}`}
async function queryApi(action,payload){
  const response=await fetch(queryApiUrl(action),{method:payload?'POST':'GET',credentials:'same-origin',headers:payload?{'Content-Type':'application/json'}:{},body:payload?JSON.stringify(payload):undefined,cache:'no-store'});
  let data={};try{data=await response.json()}catch(error){data={ok:false,error:'Invalid server response'}}
  if(!response.ok||!data.ok){const error=new Error(data.error||'Query request failed');error.status=response.status;throw error}
  return data;
}

function render(){
  const all=queryList();
  const search=$('querySearch').value.trim().toLowerCase();
  const status=$('statusFilter').value;
  const filtered=all.filter(q=>(!status||q.status===status)&&(!search||[q.name,q.phone,q.email,q.package,q.destination,q.country].join(' ').toLowerCase().includes(search)));
  $('totalCount').textContent=all.length;
  $('newCount').textContent=all.filter(q=>q.status==='New').length;
  $('pendingCount').textContent=all.filter(q=>q.status==='Pending').length;
  $('repliedCount').textContent=all.filter(q=>q.status==='Replied').length;
  $('emptyState').hidden=filtered.length>0;
  $('queryRows').innerHTML=filtered.map(q=>`<tr><td><b>${esc(q.name||'Customer')}</b><br><small>${esc(q.id)}</small></td><td>${esc(q.phone)}<br><small>${esc(q.email)}</small></td><td><b>${esc(q.package||'Custom package')}</b><br><small>${esc(q.destination)}</small></td><td>${esc(q.date||'Not set')}<br><small>${esc(q.pax||'')}</small></td><td><select data-status="${esc(q.id)}">${['New','Pending','Replied','Follow-up','Converted','Cancelled'].map(s=>`<option${q.status===s?' selected':''}>${s}</option>`).join('')}</select></td><td><div class="row-actions"><button data-view="${esc(q.id)}">View</button><button class="delete" data-delete="${esc(q.id)}">Delete</button></div></td></tr>`).join('');
}

function showQuery(id){
  const q=queryList().find(item=>item.id===id);if(!q)return;
  $('queryDetail').innerHTML=`<h2>${esc(q.name||'Customer')}</h2><div class="detail-grid"><div><span>Phone</span><b>${esc(q.phone||'-')}</b></div><div><span>Email</span><b>${esc(q.email||'-')}</b></div><div><span>Package</span><b>${esc(q.package||'-')}</b></div><div><span>Destination</span><b>${esc(q.destination||'-')}</b></div><div><span>Travel date</span><b>${esc(q.date||'-')}</b></div><div><span>Travellers</span><b>${esc(q.pax||'-')}</b></div><div><span>Budget</span><b>${esc(q.budget||'-')}</b></div><div><span>Source</span><b>${esc(q.source||'-')}</b></div></div><div class="detail-note"><span>Note</span><p>${esc(q.note||'No note added.')}</p></div>`;
  $('queryDialog').showModal();
}

function showLogin(message=''){
  stopLiveUpdates();
  localStorage.removeItem('tourim_admin_session');
  $('queryApp').hidden=true;
  $('loginScreen').hidden=false;
  $('loginMessage').textContent=message;
}

async function loadQueries({announceNew=true,force=false}={}){
  if(queryPollBusy)return false;
  queryPollBusy=true;
  try{
    const oldIds=new Set(queryList().map(q=>String(q.id)));
    const data=await queryApi('queries');
    if(!force&&queriesLoaded&&data.version===queryVersion)return true;
    queryDb={...queryDb,queries:Array.isArray(data.queries)?data.queries:[]};
    queryVersion=data.version||'';
    const newQueries=queriesLoaded?queryDb.queries.filter(q=>!oldIds.has(String(q.id))):[];
    queriesLoaded=true;
    render();
    if(announceNew&&newQueries.length){
      const newest=newQueries[0];
      notify(newQueries.length===1?`New customer query from ${newest.name||'Customer'}`:`${newQueries.length} new customer queries received`);
    }
    return true;
  }catch(error){
    if(error.status===401||error.status===403)showLogin('Your session expired. Please sign in again.');
    else if(force)notify('Could not refresh queries. Please check your connection.');
    return false;
  }finally{queryPollBusy=false}
}

function startLiveUpdates(){
  stopLiveUpdates();
  queryPollTimer=setInterval(()=>{if(!document.hidden)loadQueries()},QUERY_POLL_MS);
}
function stopLiveUpdates(){if(queryPollTimer){clearInterval(queryPollTimer);queryPollTimer=null}}

const togglePasswordBtn = $('togglePasswordBtn');
const loginPasswordInput = $('loginPassword');
if (togglePasswordBtn && loginPasswordInput) {
  togglePasswordBtn.addEventListener('click', () => {
    const isPassword = loginPasswordInput.type === 'password';
    loginPasswordInput.type = isPassword ? 'text' : 'password';
    togglePasswordBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    togglePasswordBtn.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
    const eye = togglePasswordBtn.querySelector('.eye-icon');
    const eyeOff = togglePasswordBtn.querySelector('.eye-off-icon');
    if (eye) eye.style.display = isPassword ? 'none' : 'block';
    if (eyeOff) eyeOff.style.display = isPassword ? 'block' : 'none';
  });
}

$('loginForm').addEventListener('submit',async event=>{
  event.preventDefault();const form=new FormData(event.currentTarget);$('loginMessage').textContent='Signing in…';
  const ok=await window.tourimRemoteLogin(form.get('login'),form.get('password'));
  if(!ok){$('loginMessage').textContent='Incorrect username, email, or password.';return}
  localStorage.setItem('tourim_admin_session','true');$('loginScreen').hidden=true;$('queryApp').hidden=false;$('loginMessage').textContent='';
  queriesLoaded=false;await loadQueries({announceNew:false,force:true});startLiveUpdates();
});
$('logoutBtn').addEventListener('click',async()=>{stopLiveUpdates();await window.tourimRemoteLogout?.();localStorage.removeItem('tourim_admin_session');location.reload()});
$('refreshBtn').addEventListener('click',async()=>{if(await loadQueries({announceNew:true,force:true}))notify('Queries refreshed')});
$('querySearch').addEventListener('input',render);
$('statusFilter').addEventListener('change',render);
$('queryRows').addEventListener('change',async event=>{
  const id=event.target.dataset.status;if(!id)return;
  const query=queryList().find(item=>String(item.id)===id);if(!query)return;
  const previous=query.status;const next=event.target.value;event.target.disabled=true;
  try{
    const data=await queryApi('update_query',{id,status:next});
    query.status=data.query?.status||next;query.updated_at=data.query?.updated_at||query.updated_at;queryVersion=data.version||queryVersion;
    render();notify('Status updated');
  }catch(error){query.status=previous;render();notify(error.message||'Could not update status')}
});
$('queryRows').addEventListener('click',async event=>{
  const view=event.target.dataset.view;if(view)return showQuery(view);
  const id=event.target.dataset.delete;if(!id||!confirm('Delete this customer query?'))return;
  event.target.disabled=true;
  try{const data=await queryApi('delete_query',{id});queryDb.queries=queryList().filter(q=>String(q.id)!==id);queryVersion=data.version||queryVersion;render();notify('Query deleted')}
  catch(error){event.target.disabled=false;notify(error.message||'Could not delete query')}
});
function formatExcelDate(rawDate){
  if(!rawDate) return '';
  const s = String(rawDate).trim();
  try{
    const d = new Date(s);
    if(!isNaN(d.getTime())){
      const day = String(d.getDate()).padStart(2, '0');
      const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const mon = months[d.getMonth()];
      const yr = d.getFullYear();
      if(s.includes('T') || s.includes(':')){
        let hrs = d.getHours();
        const mins = String(d.getMinutes()).padStart(2, '0');
        const ampm = hrs >= 12 ? 'PM' : 'AM';
        hrs = hrs % 12 || 12;
        return `${day}-${mon}-${yr} ${hrs}:${mins} ${ampm}`;
      }
      return `${day}-${mon}-${yr}`;
    }
  }catch(_){}
  return s;
}

function escapeXml(val){
  return String(val ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

function exportXmlSpreadsheet(list, dateStr){
  const columns = [
    { title: 'Query ID', width: 90, val: q => q.id || '' },
    { title: 'Customer Name', width: 140, val: q => q.name || 'Guest' },
    { title: 'Phone Number', width: 120, val: q => q.phone || '' },
    { title: 'Email Address', width: 180, val: q => q.email || '' },
    { title: 'Tour Package', width: 160, val: q => q.package || 'Custom Package' },
    { title: 'Destination', width: 130, val: q => q.destination || '' },
    { title: 'Travel Date', width: 120, val: q => formatExcelDate(q.date) },
    { title: 'Travellers / Pax', width: 110, val: q => q.pax || '' },
    { title: 'Budget', width: 100, val: q => q.budget || '' },
    { title: 'Country', width: 90, val: q => q.country || 'India' },
    { title: 'Lead Source', width: 100, val: q => q.source || 'Website' },
    { title: 'Status', width: 90, val: q => q.status || 'New' },
    { title: 'Customer Notes', width: 260, val: q => (q.note || '').replace(/[\r\n]+/g, ' ') },
    { title: 'Date Received', width: 160, val: q => formatExcelDate(q.created_at || q.date) },
    { title: 'Last Updated', width: 160, val: q => formatExcelDate(q.updated_at) }
  ];

  let xml = `<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1E293B"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="HeaderStyle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0F2D4A"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#0F2D4A" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataStyle">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#0F172A"/>
   <NumberFormat ss:Format="@"/>
  </Style>
  <Style ss:ID="DataStyleAlt">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#0F172A"/>
   <Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="@"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Customer Queries">
  <Table ss:DefaultRowHeight="20">
`;

  columns.forEach(col => {
    xml += `   <Column ss:Width="${col.width}"/>\n`;
  });

  xml += `   <Row ss:Height="26">\n`;
  columns.forEach(col => {
    xml += `    <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">${escapeXml(col.title)}</Data></Cell>\n`;
  });
  xml += `   </Row>\n`;

  list.forEach((item, index) => {
    const styleId = index % 2 === 0 ? 'DataStyle' : 'DataStyleAlt';
    xml += `   <Row ss:Height="22">\n`;
    columns.forEach(col => {
      const cellValue = col.val(item);
      xml += `    <Cell ss:StyleID="${styleId}"><Data ss:Type="String">${escapeXml(cellValue)}</Data></Cell>\n`;
    });
    xml += `   </Row>\n`;
  });

  xml += `  </Table>
 </Worksheet>
</Workbook>`;

  const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `tourim-customer-queries-${dateStr}.xls`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
  notify(`Exported ${list.length} enquiries to Excel (.xls)`);
}

function exportQueriesToExcel(){
  const list = queryList();
  if(!list.length){
    notify('No customer queries available to export.');
    return;
  }

  const dateStr = new Date().toISOString().slice(0, 10);

  if(typeof XLSX !== 'undefined' && XLSX.utils && XLSX.writeFile){
    try{
      const rows = list.map(q => ({
        'Query ID': q.id || '',
        'Customer Name': q.name || 'Guest',
        'Phone Number': String(q.phone || ''),
        'Email Address': q.email || '',
        'Tour Package': q.package || 'Custom Package',
        'Destination': q.destination || '',
        'Travel Date': formatExcelDate(q.date),
        'Travellers / Pax': q.pax || '',
        'Budget': q.budget || '',
        'Country': q.country || 'India',
        'Lead Source': q.source || 'Website',
        'Status': q.status || 'New',
        'Customer Notes': (q.note || '').replace(/[\r\n]+/g, ' '),
        'Date Received': formatExcelDate(q.created_at || q.date),
        'Last Updated': formatExcelDate(q.updated_at)
      }));

      const ws = XLSX.utils.json_to_sheet(rows);
      ws['!cols'] = [
        { wch: 12 }, { wch: 22 }, { wch: 16 }, { wch: 26 },
        { wch: 22 }, { wch: 18 }, { wch: 16 }, { wch: 14 },
        { wch: 14 }, { wch: 12 }, { wch: 14 }, { wch: 12 },
        { wch: 35 }, { wch: 22 }, { wch: 22 }
      ];

      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Customer Queries');
      XLSX.writeFile(wb, `tourim-customer-queries-${dateStr}.xlsx`);
      notify(`Exported ${list.length} enquiries to Excel (.xlsx)`);
      return;
    }catch(err){
      console.warn('XLSX export fallback to XML Spreadsheet', err);
    }
  }

  exportXmlSpreadsheet(list, dateStr);
}

function exportQueriesToCsv(){
  const list = queryList();
  if(!list.length){
    notify('No customer queries available to export.');
    return;
  }

  const columns = [
    { header: 'Query ID', getValue: q => q.id || '' },
    { header: 'Customer Name', getValue: q => q.name || 'Guest' },
    { header: 'Phone Number', getValue: q => q.phone || '' },
    { header: 'Email Address', getValue: q => q.email || '' },
    { header: 'Tour Package', getValue: q => q.package || 'Custom Package' },
    { header: 'Destination', getValue: q => q.destination || '' },
    { header: 'Travel Date', getValue: q => formatExcelDate(q.date) },
    { header: 'Travellers / Pax', getValue: q => q.pax || '' },
    { header: 'Budget', getValue: q => q.budget || '' },
    { header: 'Country', getValue: q => q.country || 'India' },
    { header: 'Lead Source', getValue: q => q.source || 'Website' },
    { header: 'Status', getValue: q => q.status || 'New' },
    { header: 'Customer Notes', getValue: q => (q.note || '').replace(/[\r\n]+/g, ' ') },
    { header: 'Date Received', getValue: q => formatExcelDate(q.created_at || q.date) },
    { header: 'Last Updated', getValue: q => formatExcelDate(q.updated_at) }
  ];

  const escapeCell = val => {
    const s = String(val ?? '').trim().replace(/[\r\n]+/g, ' ');
    return '"' + s.replace(/"/g, '""') + '"';
  };

  const headerRow = columns.map(col => '"' + col.header.replace(/"/g, '""') + '"').join(',');
  const dataRows = list.map(item => columns.map(col => escapeCell(col.getValue(item))).join(','));
  const csvContent = '\uFEFF' + [headerRow, ...dataRows].join('\r\n');

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  const dateStr = new Date().toISOString().slice(0, 10);
  a.href = url;
  a.download = `tourim-customer-queries-${dateStr}.csv`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
  notify(`Exported ${list.length} enquiries to CSV`);
}

const exportExcelBtn = $('exportExcelBtn');
if (exportExcelBtn) {
  exportExcelBtn.addEventListener('click', exportQueriesToExcel);
}
$('exportBtn').addEventListener('click', exportQueriesToCsv);
document.addEventListener('visibilitychange',()=>{if(!document.hidden&&localStorage.getItem('tourim_admin_session')==='true')loadQueries()});
window.addEventListener('focus',()=>{if(localStorage.getItem('tourim_admin_session')==='true')loadQueries()});

if(localStorage.getItem('tourim_admin_session')==='true'){
  $('loginScreen').hidden=true;$('queryApp').hidden=false;
  loadQueries({announceNew:false,force:true}).then(ok=>{if(ok)startLiveUpdates()});
}

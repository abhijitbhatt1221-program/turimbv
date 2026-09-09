function tourimThemeBase(){ return String(window.TOURIM_THEME_URI || '').replace(/\/$/, ''); }
function tourimAssetUrl(path){
  let v=String(path||'').trim();
  if(!v) return v;
  v = v.replace(/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\//i, '');
  if(/^(https?:|data:|blob:|mailto:|tel:|#|\/|linear-gradient|radial-gradient|url\()/i.test(v)) return v;
  const base=tourimThemeBase();
  return (base ? base+'/' : '') + v.replace(/^\.\//,'');
}
function tourimApiUrl(file){
  const base=String(window.TOURIM_API_BASE || (tourimThemeBase()?tourimThemeBase()+'/api/':'api/'));
  return base.replace(/\/?$/, '/') + String(file||'').replace(/^\//,'');
}
const TOURIM_DEFAULTS = {
  photoVersion: 5,
  offerVersion: 2,
  settings: {
    websiteName: 'TOURIM',
    tagline: 'See the world, Feel with TOURIM',
    email: 'owner.tourim@gmail.com',
    phone: '7384732179',
    phone2: '8972076635',
    whatsapp: '7384732179',
    address: 'Kachua more, Ashoknagar to Jirat road, Habra',
    currency: 'INR',
    footerCredit: 'Developed by Abhijit Bhattacharjee',
    adminUsername: 'tourimadmin',
    adminEmail: 'owner.tourim@gmail.com',
    adminPassword: '',
    adminPasswordHash: '',
    adminRecoveryEmail: 'abhijitbhatt1221@gmail.com'
  },
  homepage: {
    heroTitle: 'Discover beautiful journeys with TOURIM',
    heroSubtitle: 'Premium domestic and international tour packages, customised for families, groups, honeymooners and corporate travellers.',
    heroPrimary: 'Explore Packages',
    heroSecondary: 'Get Free Quote',
    offerTitle: 'Special Kashmir Group Tour',
    offerSubtitle: 'Durga Puja departure from Kolkata. Limited seats with curated hotel, transport and sightseeing support.',
    offerButton: 'View Offer',
    heroImage: 'assets/travel/hero-kashmir-1600x520.jpg',
    offerImage: 'assets/travel/offer-bali-1600x450.jpg',
    showHero: true,
    showPackages: true,
    showDestinations: true,
    showHotels: true,
    showBlogs: true,
    showOffer: true
  },
  packages: [
    {id:'pkg_1', title:'Kashmir Group Tour', destination:'Kashmir', duration:'9 Nights / 10 Days', price:13990, category:'Group Tour', status:'published', featured:true, bookings:240, image:'assets/travel/kashmir.jpg', short:'Kolkata to Kolkata Kashmir group package with houseboat stay and Shikara ride.', itinerary:'Srinagar, Sonmarg, Gulmarg, Pahalgam, Houseboat stay and return journey.', inclusions:'Train sleeper, hotel, meals as per plan, private vehicle in Kashmir.', exclusions:'Personal expenses, entry fees, pony rides, GST if applicable.'},
    {id:'pkg_2', title:'Goa Beach Holiday', destination:'Goa', duration:'4 Nights / 5 Days', price:9990, category:'Beach', status:'published', featured:true, bookings:205, image:'assets/travel/goa.jpg', short:'Relaxing Goa beach holiday with hotel, sightseeing and leisure time.', itinerary:'North Goa, South Goa, beach leisure and market visit.', inclusions:'Hotel, breakfast, sightseeing transfers.', exclusions:'Flights, personal expenses, water sports.'},
    {id:'pkg_3', title:'Himalayan Trek', destination:'Himachal', duration:'5 Nights / 6 Days', price:16500, category:'Adventure', status:'published', featured:true, bookings:178, image:'assets/travel/himalayan-trek.jpg', short:'Adventure-focused Himalayan escape with nature, valleys and scenic drives.', itinerary:'Delhi, Manali, Solang, Atal Tunnel and local sightseeing.', inclusions:'Hotel, breakfast, dinner, vehicle, tour support.', exclusions:'Adventure activities, permits, airfare.'},
    {id:'pkg_4', title:'Dubai City Tour', destination:'Dubai', duration:'4 Nights / 5 Days', price:44990, category:'International', status:'published', featured:true, bookings:153, image:'assets/travel/dubai-city-tour.jpg', short:'Dubai and Abu Dhabi package with Dhow Cruise, Desert Safari and Burj Khalifa.', itinerary:'Dubai arrival, city tour, desert safari, Abu Dhabi excursion and airport drop.', inclusions:'Hotel breakfast, SIC transfers, major tours.', exclusions:'Visa, flights, tourism dirham, personal expenses.'},
    {id:'pkg_5', title:'Kerala Backwaters', destination:'Kerala', duration:'5 Nights / 6 Days', price:18990, category:'Family', status:'published', featured:true, bookings:140, image:'assets/travel/kerala.jpg', short:'Scenic Kerala package with hills, backwaters, houseboat and beaches.', itinerary:'Munnar, Thekkady, Alleppey houseboat and Cochin.', inclusions:'Hotel, breakfast, houseboat, vehicle.', exclusions:'Flights, entry tickets, personal expenses.'},
    {id:'pkg_6', title:'Darjeeling Monsoon Escape', destination:'Darjeeling', duration:'3N / 4D', price:6500, category:'Hill Station', status:'published', featured:true, bookings:116, image:'assets/travel/darjeeling.jpg', short:'Misty 3N/4D Darjeeling & Mirik monsoon escape with tea gardens, Tiger Hill, and cloud-kissed Himalayan views.', itinerary:'Day 1: Arrival at NJP / Bagdogra, scenic uphill drive through pine & tea gardens to Darjeeling, hotel check-in, evening leisure walk at Mall Road & Chowrasta. Day 2: Early morning Tiger Hill sunrise view, Batasia Loop, Ghoom Monastery, followed by Himalayan Mountaineering Institute (HMI), P.N. Zoo and Happy Valley Tea Estate. Day 3: Full-day scenic excursion to Mirik Lake (Sumendu Lake), pine forest walk, Pashupati viewpoint, and return to Darjeeling. Day 4: Morning breakfast, check-out, scenic drive via Kurseong tea hills to NJP railway station / Bagdogra airport for departure.', inclusions:'Hotel accommodation (3 Nights), daily breakfast, dedicated private vehicle for transfers & sightseeing, driver allowances, toll and parking.', exclusions:'Train/air fare, entry tickets to monuments & zoo, personal expenses, meals not specified, joy ride on Toy Train.', route:'NJP / Bagdogra → Darjeeling → Tiger Hill & Local Sightseeing → Mirik Lake → Kurseong → NJP / Bagdogra'}
  ],
  destinations: [
    {id:'dest_1', name:'Kashmir', country:'India', region:'North India', status:'published', image:'assets/travel/kashmir.jpg', short:'Heavenly valleys, snow peaks, gardens and houseboats.', bestTime:'March to October'},
    {id:'dest_2', name:'Bali', country:'Indonesia', region:'International', status:'published', image:'assets/travel/bali.jpg', short:'Romantic beaches, temples, villas and honeymoon experiences.', bestTime:'April to October'},
    {id:'dest_3', name:'Darjeeling', country:'India', region:'East India', status:'published', image:'assets/travel/darjeeling.jpg', short:'Tea gardens, toy train, Tiger Hill and beautiful mountain weather.', bestTime:'March to June, October to December'},
    {id:'dest_4', name:'Dubai', country:'UAE', region:'International', status:'published', image:'assets/travel/dubai.jpg', short:'Luxury skyline, desert safari, shopping and family attractions.', bestTime:'November to March'},
    {id:'dest_5', name:'Andaman', country:'India', region:'Island', status:'published', image:'assets/travel/andaman.jpg', short:'Crystal beaches, islands, snorkelling and relaxed tropical stays.', bestTime:'October to May'},
    {id:'dest_6', name:'Sikkim', country:'India', region:'North East India', status:'published', image:'assets/travel/sikkim.jpg', short:'Monasteries, lakes, mountain passes and peaceful hill towns.', bestTime:'March to May, October to December'}
  ],
  hotels: [
    {id:'hotel_1', name:'Maya Inn Hotel', destination:'Gangtok', category:'3 Star', meal:'Breakfast', price:2500, status:'published', image:'assets/travel/sikkim.jpg', amenities:'Wifi, Breakfast, Family Rooms'},
    {id:'hotel_2', name:'Demattrie Hotel', destination:'Kolkata', category:'3 Star', meal:'Breakfast', price:3750, status:'published', image:'assets/travel/darjeeling.jpg', amenities:'AC Room, Breakfast, City Location'},
    {id:'hotel_3', name:'Deluxe Houseboat', destination:'Srinagar', category:'Deluxe', meal:'MAP', price:4200, status:'published', image:'assets/travel/kashmir.jpg', amenities:'Lake View, Dinner, Shikara Support'}
  ],
  blogs: [
    {id:'blog_1', title:'Best Time to Visit Kashmir', category:'Guide', author:'TOURIM Team', status:'published', image:'assets/travel/kashmir.jpg', excerpt:'A simple guide for travellers planning Kashmir from Kolkata.'},
    {id:'blog_2', title:'How to Choose a Family Tour Package', category:'Travel Tips', author:'TOURIM Team', status:'published', image:'assets/travel/goa.jpg', excerpt:'Things to check before booking hotels, transfers and sightseeing.'},
    {id:'blog_3', title:'Monsoon in Darjeeling', category:'Hill Travel', author:'TOURIM Team', status:'published', image:'assets/travel/darjeeling.jpg', excerpt:'Tea gardens, misty roads and beautiful local sightseeing ideas.'}
  ],
  gallery: [
    {title:'Kashmir Lake & Valley', image:'assets/travel/kashmir.jpg', tag:'Kashmir'},
    {title:'Kashmir Hero Landscape', image:'assets/travel/hero-kashmir-1600x520.jpg', tag:'Mountain'},
    {title:'Bali Cliffside Escape', image:'assets/travel/bali.jpg', tag:'International'},
    {title:'Bali Honeymoon Sunset', image:'assets/travel/offer-bali-1600x450.jpg', tag:'Honeymoon'},
    {title:'Darjeeling Tea Garden', image:'assets/travel/darjeeling.jpg', tag:'Hill Station'},
    {title:'Dubai Luxury Skyline', image:'assets/travel/dubai.jpg', tag:'Luxury'},
    {title:'Andaman Crystal Beach', image:'assets/travel/andaman.jpg', tag:'Island'},
    {title:'Sikkim Monastery View', image:'assets/travel/sikkim.jpg', tag:'North East'},
    {title:'Kerala Backwater Stay', image:'assets/travel/kerala.jpg', tag:'Backwater'},
    {title:'Goa Beach Holiday', image:'assets/travel/goa.jpg', tag:'Beach'}
  ],
  offers: [
    {id:'offer_1', title:'Free Houseboat Stay', coupon:'KASHMIRHBFREE', status:'active', image:'assets/travel/kashmir.jpg', destination:'Kashmir', tag:'Kashmir Group Tour', validity:'Limited seats', discount:'Free 1N Houseboat', description:'Book Kashmir group tour and get free 1 night houseboat stay + 1 hour Shikara ride.'},
    {id:'offer_2', title:'Bali Honeymoon Add-on', coupon:'BALIBFFREE', status:'active', image:'assets/travel/offer-bali-1600x450.jpg', destination:'Bali', tag:'Honeymoon Special', validity:'Selected Bali packages', discount:'Free Add-on', description:'Floating breakfast add-on for selected Bali honeymoon packages.'}
  ],
  queries: [
    {id:'#Q1248', name:'Rahul Sharma', phone:'9876543210', email:'rahul@example.com', package:'Adventure Explorer', destination:'Himachal', date:'2026-05-17', pax:'4 Adults', budget:'₹50,000', country:'India', source:'Website', status:'New', note:''},
    {id:'#Q1247', name:'Priya Mehta', phone:'9876501234', email:'priya@example.com', package:'Goa Beach Holiday', destination:'Goa', date:'2026-05-17', pax:'2 Adults', budget:'₹30,000', country:'India', source:'WhatsApp', status:'Replied', note:'Asked for sea-view hotel.'},
    {id:'#Q1246', name:'Amit Verma', phone:'9988776655', email:'amit@example.com', package:'Himalayan Trek', destination:'Himachal', date:'2026-05-16', pax:'6 Adults', budget:'₹90,000', country:'India', source:'Website', status:'Pending', note:''},
    {id:'#Q1245', name:'Sneha Iyer', phone:'9080706050', email:'sneha@example.com', package:'Kerala Backwaters', destination:'Kerala', date:'2026-05-16', pax:'Family 5', budget:'₹75,000', country:'India', source:'Website', status:'Replied', note:''},
    {id:'#Q1244', name:'Varun Singh', phone:'9000001111', email:'varun@example.com', package:'Dubai City Tour', destination:'Dubai', date:'2026-05-16', pax:'6 Adults', budget:'₹3,00,000', country:'India', source:'Facebook', status:'New', note:''}
  ],
  analytics: {
    visitors: 128540,
    interested: 23876,
    enquiries: 2341,
    users: 2350,
    traffic: [18000, 22000, 19500, 27000, 21000, 35000, 15000, 9000, 23000, 51000],
    pageViews: [4000, 12000, 7000, 17000, 9000, 21000, 8000, 4500, 11000, 64640],
    devices: {Desktop:48.2, Mobile:38.2, Tablet:10.6, Other:2.5},
    locations: {India:38.6, USA:18.2, UK:8.7, Australia:6.5, UAE:5.9}
  },
  notifications: [
    {id:'n1', title:'New enquiry received from Rahul Sharma', text:'Adventure Explorer package', time:'2 min ago', read:false},
    {id:'n2', title:'Email sent successfully', text:'Goa Package', time:'15 min ago', read:false},
    {id:'n3', title:'New user registered', text:'John Doe joined the platform', time:'1 hour ago', read:true},
    {id:'n4', title:'New lead reminder', text:'Follow up with 5 pending enquiries', time:'2 hours ago', read:true}
  ],
  activity: [
    {text:'Package update at an enquiry from Priya Mehta', time:'10 min ago'},
    {text:'Package “Adventure Explorer” updated', time:'35 min ago'},
    {text:'New offer “Summer Special” created', time:'2 hours ago'},
    {text:'Page “About Us” updated', time:'3 hours ago'},
    {text:'Homepage blocks updated', time:'4 hours ago'}
  ]
};


const TOURIM_IMAGE_FALLBACKS = [
  ['nagaland|hornbill|kisama|kohima|khonoma|dimapur', 'assets/travel/nagaland-hornbill-festival.jpg'],
  ['vaishno devi|katra', 'assets/travel/vaishno-devi.jpg'],
  ['kalimpong', 'assets/travel/kalimpong.jpg'],
  ['north bengal', 'assets/travel/north-bengal.jpg'],
  ['dooars', 'assets/travel/dooars.jpg'],
  ['alipurduar', 'assets/travel/alipurduar.jpg'],
  ['guwahati', 'assets/travel/guwahati.jpg'],
  ['meghalaya', 'assets/travel/meghalaya.jpg'],
  ['mauritius', 'assets/travel/mauritius.jpg'],
  ['thailand', 'assets/travel/thailand.jpg'],
  ['kashmir|srinagar|vaishno|jammu', 'assets/travel/kashmir.jpg'],
  ['goa|beach|puri|coastal', 'assets/travel/goa.jpg'],
  ['dubai|uae', 'assets/travel/dubai.jpg'],
  ['kerala|backwater|dooars|alipurduar|meghalaya|nature', 'assets/travel/kerala.jpg'],
  ['darjeeling|kalimpong|sittong|north bengal|hill|kolkata|purulia', 'assets/travel/darjeeling.jpg'],
  ['andaman|island|guwahati|tripura|sundarbans', 'assets/travel/andaman.jpg'],
  ['bali|mauritius|thailand|honeymoon', 'assets/travel/bali.jpg'],
  ['sikkim|himachal|himalayan|ladakh|arunachal|gujarat|rajasthan|mathura|braj', 'assets/travel/sikkim.jpg']
];
const TOURIM_FALLBACK_POOL = ['assets/travel/kashmir.jpg','assets/travel/goa.jpg','assets/travel/sikkim.jpg','assets/travel/dubai.jpg','assets/travel/kerala.jpg','assets/travel/darjeeling.jpg','assets/travel/andaman.jpg','assets/travel/bali.jpg'];
function tourimFallbackImageForItem(item){
  const hay=[item?.title,item?.name,item?.destination,item?.category,item?.region,item?.country,item?.short,item?.amenities,item?.description,item?.tag].filter(Boolean).join(' ').toLowerCase();
  for(const [keys,path] of TOURIM_IMAGE_FALLBACKS){
    if(keys.split('|').some(k=>hay.includes(k))) return path;
  }
  const seed=String(item?.id||item?.title||item?.name||hay||'tourim');
  let hash=0;
  for(let i=0;i<seed.length;i++) hash=(hash*31 + seed.charCodeAt(i)) >>> 0;
  return TOURIM_FALLBACK_POOL[hash % TOURIM_FALLBACK_POOL.length];
}
function tourimImageForItem(item){
  const raw=String((item&&item.image)||'').trim();
  if(raw && !/^linear-gradient|^radial-gradient/i.test(raw) && !(/^data:image/i.test(raw) && raw.length > 60000)) return tourimAssetUrl(raw);
  return tourimAssetUrl(tourimFallbackImageForItem(item));
}
function tourimNormalizeImages(db){
  ['packages','destinations','hotels','blogs','gallery','offers'].forEach(key=>{
    (db[key]||[]).forEach(item=>{ if(item && 'image' in item) item.image=tourimImageForItem(item); });
  });
  return db;
}

function safeSetItem(key, value){
  try{
    localStorage.setItem(key, value);
    return true;
  }catch(e){
    console.warn('TOURIM LocalStorage quota exceeded or write failed:', e);
    return false;
  }
}

function tourimGetDB(){
  const saved = localStorage.getItem('tourim_db_v1');
  if(!saved){
    const fresh=tourimNormalizeImages(structuredClone(TOURIM_DEFAULTS));
    safeSetItem('tourim_db_v1', JSON.stringify(fresh));
    return fresh;
  }
  try{
    const db = JSON.parse(saved);
    const merged={...structuredClone(TOURIM_DEFAULTS), ...db, settings:{...TOURIM_DEFAULTS.settings, ...(db.settings||{})}, homepage:{...TOURIM_DEFAULTS.homepage, ...(db.homepage||{})}};
    // Force-refresh the public photo/gallery section when old cached demo images exist.
    // This keeps user pages realistic and attractive after replacing files on hosting.
    if((db.photoVersion||0) < TOURIM_DEFAULTS.photoVersion || !(merged.gallery||[]).length || (merged.gallery||[]).some(g=>!g.image || /^linear-gradient|^radial-gradient/i.test(String(g.image)))){
      merged.gallery = structuredClone(TOURIM_DEFAULTS.gallery);
      merged.photoVersion = TOURIM_DEFAULTS.photoVersion;
    }
    if((db.offerVersion||0) < TOURIM_DEFAULTS.offerVersion || !(merged.offers||[]).length || (merged.offers||[]).some(o=>!o.image || /^linear-gradient|^radial-gradient/i.test(String(o.image)))){
      const defaultOffers = structuredClone(TOURIM_DEFAULTS.offers);
      merged.offers = (merged.offers||defaultOffers).map((offer, idx)=>{
        const base = defaultOffers.find(d=>d.id===offer.id) || defaultOffers[idx] || defaultOffers[0];
        return {...base, ...offer, image: offer.image || base.image, tag: offer.tag || base.tag, validity: offer.validity || base.validity, discount: offer.discount || base.discount, destination: offer.destination || base.destination};
      });
      merged.offerVersion = TOURIM_DEFAULTS.offerVersion;
    }
    const normalized=tourimNormalizeImages(merged);
    safeSetItem('tourim_db_v1', JSON.stringify(normalized));
    return normalized;
  }catch(e){
    const fresh=tourimNormalizeImages(structuredClone(TOURIM_DEFAULTS));
    safeSetItem('tourim_db_v1', JSON.stringify(fresh));
    return fresh;
  }
}
function tourimSaveDB(db){ safeSetItem('tourim_db_v1', JSON.stringify(tourimNormalizeImages(db))); }
function money(n){ return '₹' + Number(n||0).toLocaleString('en-IN'); }
function uid(prefix='id'){ return prefix + '_' + Math.random().toString(36).slice(2,9); }
function toast(message){
  let el = document.querySelector('.toast');
  if(!el){ el=document.createElement('div'); el.className='toast'; document.body.appendChild(el); }
  el.textContent = message;
  el.style.display = 'block';
  setTimeout(()=>{el.style.display='none'}, 2600);
}
function tourimRunWhenIdle(fn, timeout=1800){
  if('requestIdleCallback' in window) return requestIdleCallback(fn, {timeout});
  return setTimeout(fn, Math.min(timeout, 1200));
}

/* InfinityFree PHP sync layer: keeps localStorage fallback, but syncs live CMS data through api/db.php on hosted site. */
(function(){
  const originalGetDB = tourimGetDB;
  const originalSaveDB = tourimSaveDB;
  const apiUrl = tourimApiUrl('db.php');
  const isHosted = () => location.protocol === 'http:' || location.protocol === 'https:';
  const adminSession = () => localStorage.getItem('tourim_admin_session') === 'true';
  let remoteReady = false;

  async function api(action, payload, params=''){
    if(!isHosted()) return null;
    const res = await fetch(`${apiUrl}?action=${encodeURIComponent(action)}${params}`, {
      method: payload ? 'POST' : 'GET',
      credentials: 'same-origin',
      headers: payload ? {'Content-Type':'application/json'} : {},
      body: payload ? JSON.stringify(payload) : undefined
    });
    let data = null;
    try{ data = await res.json(); }catch(e){ data = {ok:false,error:'Invalid server response'}; }
    if(!res.ok || !data.ok) throw new Error(data.error || 'Server request failed');
    return data;
  }

  window.tourimLoadRemoteDB = async function(privateMode=false){
    if(!isHosted()) return originalGetDB();
    try{
      const data = await api('get', null, (privateMode || adminSession()) ? '&private=1' : '');
      if(data && data.db){
        const isServerAdmin = !!(data.db.settings && data.db.settings.adminPasswordHash);
        if (adminSession() && !isServerAdmin) {
          console.warn('TOURIM server session expired. Preserving local DB for login redirect.');
        } else {
          originalSaveDB(data.db);
        }
        remoteReady = true;
        window.dispatchEvent(new CustomEvent('tourimdb:ready', {detail:data.db}));
        return data.db;
      }
    }catch(err){
      console.warn('TOURIM remote DB unavailable:', err.message);
    }
    return originalGetDB();
  };

  tourimGetDB = function(){
    return originalGetDB();
  };

  tourimSaveDB = function(db){
    originalSaveDB(db);
    const isAdminPage = location.pathname.includes('admin') || !!document.getElementById('adminShell');
    if(!isHosted() || !adminSession() || !isAdminPage) return Promise.resolve({ok:true, db, mode:'local'});
    return api('save', {db}).then(data=>{
      if(data && data.db) originalSaveDB(data.db);
      return data;
    }).catch(err=>{
      console.warn('TOURIM remote save failed:', err.message);
      return {ok:false, error:err.message, db, mode:'local'};
    });
  };

  window.tourimRemoteLogin = async function(email, password){
    try{
      const data = await api('login', {email, password});
      if(!data || !data.ok) return false;
      if(data.db){ originalSaveDB(data.db); window.dispatchEvent(new CustomEvent('tourimdb:ready', {detail:data.db})); }
      return true;
    }catch(err){
      return false;
    }
  };

  window.tourimVerifyAdminPassword = async function(password){
    const settings = originalGetDB().settings || {};
    const login = settings.adminUsername || settings.adminEmail || 'tourimadmin';
    try{
      const data = await api('login', {email: login, password});
      return !!(data && data.ok);
    }catch(err){
      return false;
    }
  };

  window.tourimRemoteLogout = async function(){
    try{ await api('logout', {}); }catch(e){}
  };

  window.tourimSubmitRemoteEnquiry = async function(query){
    try{ return await api('enquiry', query); }catch(err){ console.warn('TOURIM enquiry sync failed:', err.message); return null; }
  };

  window.tourimTrackRemote = async function(event){
    try{ return await api('event', event); }catch(err){ return null; }
  };

  const adminMode = localStorage.getItem('tourim_admin_session') === 'true' || location.pathname.includes('admin');
  const loadRemote = () => window.tourimLoadRemoteDB(adminMode);
  if(adminMode) setTimeout(loadRemote, 0);
  else tourimRunWhenIdle(loadRemote, 2200);
})();

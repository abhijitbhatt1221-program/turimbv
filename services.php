<?php
/*
Template Name: TOURIM Services
*/
if (!defined('ABSPATH')) { exit; }

$service_groups = [
  [
    'id' => 'core-services',
    'icon' => '&#127757;',
    'title' => 'Core Services',
    'description' => 'Complete domestic, international and special-interest tour planning for every traveller type.',
    'items' => [
      'Domestic Tour Packages',
      'International Tour Packages',
      'Honeymoon Packages',
      'Family Holiday Packages',
      'Group Tours',
      'Corporate Tours',
      'Educational & School Excursions',
      'Pilgrimage Tours',
      'Backpacking & Budget Tours',
      'Offbeat & Adventure Tours',
      'Weekend Getaways',
      'Cruise Holidays',
    ],
  ],
  [
    'id' => 'hotel-stay-services',
    'icon' => '&#127976;',
    'title' => 'Hotel & Stay Services',
    'description' => 'Comfortable stays from budget rooms to premium villas, resorts and nature stays.',
    'items' => [
      'Hotel Booking',
      'Resort Booking',
      'Homestay Booking',
      'Luxury Villas',
      'Camping & Glamping',
      'Guest House Booking',
    ],
  ],
  [
    'id' => 'transport-services',
    'icon' => '&#128662;',
    'title' => 'Transport Services',
    'description' => 'Travel connection support for flights, trains, buses, cabs and group transport.',
    'items' => [
      'Flight Booking',
      'Train Ticket Assistance',
      'Bus Booking',
      'Private Cab Rental',
      'Airport Transfers',
      'Tempo Traveller',
      'Luxury Coach Booking',
      'Self Drive Car Rental',
    ],
  ],
  [
    'id' => 'visa-documentation',
    'icon' => '&#128196;',
    'title' => 'Visa & Documentation',
    'description' => 'Practical documentation assistance for smoother international travel preparation.',
    'items' => [
      'Visa Assistance',
      'Passport Assistance',
      'Travel Insurance',
      'Forex Assistance',
      'International SIM/eSIM',
      'Invitation Letter Assistance',
      'Documentation Support',
    ],
  ],
  [
    'id' => 'special-packages',
    'icon' => '&#127881;',
    'title' => 'Special Packages',
    'description' => 'Personal celebration trips and traveller-specific packages with thoughtful planning.',
    'items' => [
      'Destination Wedding',
      'Anniversary Trips',
      'Birthday Tours',
      'Proposal Packages',
      'Couple Special Tours',
      'Senior Citizen Tours',
      'Women Only Tours',
      'Solo Traveller Packages',
    ],
  ],
  [
    'id' => 'business-services',
    'icon' => '&#127970;',
    'title' => 'Business Services',
    'description' => 'Professional travel handling for corporate teams, events and incentive journeys.',
    'items' => [
      'Corporate Travel Management',
      'MICE (Meetings, Incentives, Conferences & Exhibitions)',
      'Employee Incentive Tours',
      'Dealer Meet Arrangements',
    ],
  ],
  [
    'id' => 'adventure-activities',
    'icon' => '&#127807;',
    'title' => 'Adventure Activities',
    'description' => 'Adventure experiences, safaris and water activities planned with destination guidance.',
    'items' => [
      'Trekking',
      'River Rafting',
      'Scuba Diving',
      'Snorkelling',
      'Bungee Jumping',
      'Paragliding',
      'Skiing',
      'Desert Safari',
      'Camel Safari',
      'Jungle Safari',
      'River Cruise',
      'Houseboat Booking',
    ],
  ],
  [
    'id' => 'activity-attraction-booking',
    'icon' => '&#127915;',
    'title' => 'Activity & Attraction Booking',
    'description' => 'Ticketing and permit support for sightseeing, shows, parks and local experiences.',
    'items' => [
      'Theme Park Tickets',
      'Museum Tickets',
      'Local Sightseeing Tours',
      'Ropeway Tickets',
      'Adventure Park Booking',
      'Cultural Show Tickets',
      'Wildlife Safari Permits',
    ],
  ],
  [
    'id' => 'international-travel-services',
    'icon' => '&#9992;&#65039;',
    'title' => 'International Travel Services',
    'description' => 'Popular international holidays with hotels, transfers, activities and guidance.',
    'items' => [
      'Dubai Packages',
      'Thailand Packages',
      'Bali Packages',
      'Singapore Packages',
      'Maldives Packages',
      'Europe Tours',
      'UK Tours',
      'Mauritius Packages',
      'Vietnam Packages',
      'Bhutan Packages',
      'Nepal Packages',
      'Sri Lanka Packages',
    ],
  ],
  [
    'id' => 'premium-services',
    'icon' => '&#128718;&#65039;',
    'title' => 'Premium Services',
    'description' => 'Personalised luxury planning and VIP assistance for high-comfort journeys.',
    'items' => [
      'Customised Itinerary',
      'Luxury Travel Planning',
      'VIP Airport Assistance',
      'Meet & Greet Services',
      'Private Guide',
      'Chauffeur Service',
      'Concierge Service',
    ],
  ],
  [
    'id' => 'online-services',
    'icon' => '&#128187;',
    'title' => 'Online Services',
    'description' => 'Digital tools and online support options to make planning easier.',
    'items' => [
      'Request a Quote',
      'Book Now',
      'Pay Online',
      'EMI Payment Option',
      'Live Chat',
      'WhatsApp Support',
      'Travel Cost Calculator',
      'Tour Comparison Tool',
      'Package Wishlist',
      'Download Itinerary PDF',
    ],
  ],
  [
    'id' => 'customer-services',
    'icon' => '&#128226;',
    'title' => 'Customer Services',
    'description' => 'Reliable support before booking, during travel and after the journey.',
    'items' => [
      '24x7 Customer Support',
      'Emergency Travel Assistance',
      'Trip Modification',
      'Cancellation & Refund Support',
      'Complaint Management',
      'Feedback & Reviews',
    ],
  ],
  [
    'id' => 'membership-loyalty',
    'icon' => '&#127873;',
    'title' => 'Membership & Loyalty',
    'description' => 'Savings, offers and loyalty benefits for individuals, groups and companies.',
    'items' => [
      'Referral Rewards',
      'Membership Plans',
      'Coupon & Promo Codes',
      'Seasonal Offers',
      'Gift Cards',
      'Corporate Discounts',
    ],
  ],
  [
    'id' => 'travel-information',
    'icon' => '&#128218;',
    'title' => 'Travel Information',
    'description' => 'Planning resources and destination information for confident travel decisions.',
    'items' => [
      'Travel Blog',
      'Destination Guide',
      'Visa Guide',
      'Travel Tips',
      'Packing Checklist',
      'Weather Information',
      'Festival Calendar',
      'FAQs',
    ],
  ],
];

$total_services = array_sum(array_map(static fn($group) => count($group['items']), $service_groups));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Services | TOURIM</title>
  <meta name="description" content="Explore TOURIM travel services including packages, hotels, transport, visa assistance, activities, corporate travel, online support and customer service." />
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/styles.css?v=20260814-v5">
  <?php tourim_wp_template_meta(); ?>
  <?php wp_head(); ?>
</head>
<body data-page="services">

<header class="site-header">
  <div class="container nav">
    <a class="logo" href="index.html"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" alt="TOURIM logo"></a>
    <nav class="nav-links" id="navLinks">
      <a href="index.html">Home</a>
      <a href="destinations.html">Destinations</a>
      <a href="packages.html">Packages</a>
      <a href="services.html">Services</a>
      <a href="hotels.html">Hotels</a>
      <a href="blogs.html">Blogs</a>
      <a href="offers.html">Offers</a>
      <a href="contact.html">Get Quote</a>
    </nav>
    <div class="nav-actions">
      <a class="pill phone-pill" data-tel href="tel:7384732179">&#9742; <span data-phone>7384732179</span></a>
      <a class="btn primary" href="contact.html">Plan Trip</a>
      <button class="btn ghost mobile-menu" id="mobileMenuBtn">&#9776;</button>
    </div>
  </div>
</header>

<main class="services-page">
  <section class="page-hero services-hero">
    <div class="container">
      <div class="breadcrumb"><a href="index.html">Home</a><span>&rsaquo;</span><span>Services</span></div>
      <h1>All TOURIM travel services in one place</h1>
      <p>From tour packages and hotel stays to visas, transport, adventure activities, corporate travel and 24x7 support, TOURIM helps travellers plan the complete journey.</p>
      <div class="services-hero-actions">
        <a class="btn primary" href="contact.html?service=Customised%20Itinerary">Request Custom Plan</a>
        <a class="btn ghost" href="#serviceDirectory">Browse Services</a>
      </div>
      <div class="services-stats" aria-label="TOURIM service summary">
        <div><b><?php echo esc_html((string) count($service_groups)); ?></b><span>Service Categories</span></div>
        <div><b><?php echo esc_html((string) $total_services); ?>+</b><span>Travel Services</span></div>
        <div><b>24x7</b><span>Customer Support</span></div>
      </div>
    </div>
  </section>

  <section class="page-section services-overview" id="serviceDirectory">
    <div class="container">
      <div class="section-title">
        <div>
          <span class="badge teal">Service Directory</span>
          <h2>Choose what you need</h2>
          <p>Each service can be requested directly. Share your destination, date, guests and budget so TOURIM can prepare the right quotation.</p>
        </div>
        <a class="btn ghost" href="contact.html">Talk to TOURIM</a>
      </div>

      <div class="services-jump-grid" aria-label="Service categories">
        <?php foreach ($service_groups as $group): ?>
          <a href="#<?php echo esc_attr($group['id']); ?>">
            <span aria-hidden="true"><?php echo wp_kses_post($group['icon']); ?></span>
            <b><?php echo esc_html($group['title']); ?></b>
            <small><?php echo esc_html((string) count($group['items'])); ?> services</small>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="services-grid">
        <?php foreach ($service_groups as $group): ?>
          <article class="service-card" id="<?php echo esc_attr($group['id']); ?>">
            <div class="service-card-head">
              <span class="service-icon" aria-hidden="true"><?php echo wp_kses_post($group['icon']); ?></span>
              <div>
                <h3><?php echo esc_html($group['title']); ?></h3>
                <p><?php echo esc_html($group['description']); ?></p>
              </div>
            </div>
            <div class="service-chip-list">
              <?php foreach ($group['items'] as $item): ?>
                <a class="service-chip" href="contact.html?service=<?php echo rawurlencode($item); ?>"><?php echo esc_html($item); ?></a>
              <?php endforeach; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="page-section services-policy-summary" aria-labelledby="policySummaryTitle">
    <div class="container">
      <div class="section-title">
        <div>
          <span class="badge purple">Policy &amp; Terms Summary</span>
          <h2 id="policySummaryTitle">Before you book with TOURIM</h2>
          <p>Please review the key points below before confirming any tour, hotel, transport, visa, activity or custom travel service.</p>
        </div>
        <a class="btn ghost" href="policy.html">Read Full Policy</a>
      </div>

      <div class="policy-summary-grid">
        <article class="policy-summary-card">
          <span>1</span>
          <h3>Booking Confirmation</h3>
          <p>Bookings are confirmed only after required traveller details, selected service details and applicable advance payment are received.</p>
          <a href="policy.html#booking-terms-and-conditions">Booking Terms</a>
        </article>
        <article class="policy-summary-card">
          <span>2</span>
          <h3>Payment Safety</h3>
          <p>Package price, inclusions, exclusions, due dates and payment proof should be checked carefully before final confirmation.</p>
          <a href="policy.html#payment-policy">Payment Policy</a>
        </article>
        <article class="policy-summary-card">
          <span>3</span>
          <h3>Cancellation &amp; Refund</h3>
          <p>Cancellation, date change and refund rules depend on supplier policies, booking stage, season, ticketing and hotel deadlines.</p>
          <a href="policy.html#cancellation-refund-and-last-moment-cancellation-policy">Refund Support</a>
        </article>
        <article class="policy-summary-card">
          <span>4</span>
          <h3>Documents &amp; Visa</h3>
          <p>Travellers must share correct names, IDs, passport details, visa documents and permits on time wherever required.</p>
          <a href="policy.html#privacy-policy">Data &amp; Documents</a>
        </article>
        <article class="policy-summary-card">
          <span>5</span>
          <h3>Third-Party Services</h3>
          <p>Hotels, airlines, trains, vehicles, activities, visas and insurance may follow their own availability and operational rules.</p>
          <a href="policy.html">Service Conditions</a>
        </article>
        <article class="policy-summary-card">
          <span>6</span>
          <h3>Customer Support</h3>
          <p>TOURIM supports trip modifications, emergencies, complaints and feedback through phone, WhatsApp and email assistance.</p>
          <a href="contact.html">Contact Support</a>
        </article>
      </div>
    </div>
  </section>

  <section class="page-section services-cta-band">
    <div class="container">
      <div class="offer-banner">
        <div>
          <span class="badge orange">Custom Travel Help</span>
          <h2>Need a service not listed here?</h2>
          <p>Send your travel requirement to TOURIM. The team can combine packages, hotels, transport, tickets, visa support and customer assistance into one plan.</p>
        </div>
        <a class="btn primary" href="contact.html">Get Free Quote</a>
      </div>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" alt="TOURIM" style="height:64px;background:#fff;border-radius:14px;padding:4px"><p data-tagline>See the world, Feel with TOURIM</p><div class="action-row"><a class="btn teal" data-wa target="_blank">WhatsApp</a></div></div>
      <div><h4>Explore</h4><a href="packages.html">Packages</a><br><a href="services.html">Services</a><br><a href="destinations.html">Destinations</a><br><a href="hotels.html">Hotels</a><br><a href="blogs.html">Blogs</a><br><a href="gallery.html">Gallery</a></div>
      <div><h4>Contact</h4><p>Phone: <span data-phone>7384732179</span><br>Email: <span data-email>owner.tourim@gmail.com</span></p></div>
      <div><h4>Address</h4><p data-address>Ashokenagar Kachua More, North 24 Parganas, 743272</p></div>
    </div>
    <div class="footer-legal" aria-label="TOURIM policy links">
      <b>Policy &amp; Terms</b>
      <div class="footer-legal-links">
        <a href="policy.html">Combined Policy Manual</a>
        <a href="policy.html#privacy-policy">Privacy Policy</a>
        <a href="policy.html#booking-terms-and-conditions">Terms &amp; Conditions</a>
        <a href="policy.html#cancellation-refund-and-last-moment-cancellation-policy">Cancellation &amp; Refund</a>
        <a href="policy.html#payment-policy">Payment Safety</a>
      </div>
    </div>
    <div class="copyright">&copy; 2026 TOURIM Travel Agency. All rights reserved.</div>
  </div>
</footer>
<div class="toast"></div>
<script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/data.js?v=20260624-v4"></script>
<script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/pages.js?v=20260624-v4"></script>
<script>
const mobileMenuBtn=document.getElementById('mobileMenuBtn');
mobileMenuBtn?.addEventListener('click',()=>document.getElementById('navLinks')?.classList.toggle('is-open'));
document.querySelectorAll('.nav-links a').forEach(a=>{if(location.pathname.endsWith(a.getAttribute('href')))a.classList.add('active-page')});
</script>
  <?php wp_footer(); ?>
</body>
</html>

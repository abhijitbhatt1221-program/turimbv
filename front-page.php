<?php
/*
Template Name: TOURIM Home
*/
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TOURIM - See the world, Feel with TOURIM</title>
  <meta name="description" content="TOURIM premium travel marketplace for domestic and international tour packages." />
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="shortcut icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" type="image/png" />
  <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/styles.css?v=20260814-v5">
  <?php tourim_wp_template_meta(); ?>
  <?php wp_head(); ?>
</head>
<body>
  <header class="site-header">
    <div class="container nav">
      <a class="logo" href="#home"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" alt="TOURIM logo"></a>
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
        <a class="pill phone-pill" id="phoneLink" href="https://wa.me/917384732179?text=Hi%20TOURIM%2C%20I%20want%20to%20plan%20a%20tour" target="_blank" rel="noopener">7384732179</a>
        <a class="btn primary" href="contact.html">Plan Trip</a>
        <button class="btn ghost mobile-menu" id="mobileMenuBtn">☰</button>
      </div>
    </div>
  </header>

  <main id="home">
    <section class="hero" id="heroSection">
      <div class="container">
        <div>
          <div class="hero-kicker">✈ Premium Travel Marketplace</div>
          <h1 id="heroTitle">Discover beautiful journeys with TOURIM</h1>
          <p id="heroSubtitle">Premium domestic and international tour packages, customised for families, groups, honeymooners and corporate travellers.</p>
          <div class="hero-actions">
            <a class="btn primary" id="heroPrimary" href="packages.html">Explore Packages</a>
            <a class="btn teal" id="heroSecondary" href="contact.html">Get Free Quote</a>
          </div>
          <div class="search-panel" role="search">
            <input id="searchInput" placeholder="Search destination or package" />
            <select id="categoryFilter"><option value="">All Categories</option><option>Group Tour</option><option>Beach</option><option>Family</option><option>International</option></select>
            <button class="btn teal" id="searchBtn">Search</button>
          </div>
        </div>
        <aside class="glass-form" id="quote">
          <h3>Get a quick quotation</h3>
          <p>Submit your travel details. TOURIM will contact you soon.</p>
          <form class="form-grid" id="quoteForm">
            <input name="name" placeholder="Name" required />
            <input name="phone" placeholder="Phone / WhatsApp" required />
            <input name="email" placeholder="Email optional" />
            <select name="destination" required id="quoteDestination"><option value="">Select destination</option></select>
            <input name="date" type="date" />
            <input name="pax" placeholder="No. of guests e.g. 2 Adults + 1 Child" />
            <textarea name="message" placeholder="Tell us your budget, hotel preference and special request"></textarea>
            <button class="btn primary" type="submit">Submit Enquiry</button>
          </form>
        </aside>
      </div>
    </section>

    <div class="container trust-strip">
      <div class="trust-grid soft-card">
        <div class="trust-item"><span class="icon orange">✓</span><div><b>Verified Hotels</b><span>Curated stays and partners</span></div></div>
        <div class="trust-item"><span class="icon teal">☎</span><div><b>24×7 Support</b><span>Before and during travel</span></div></div>
        <div class="trust-item"><span class="icon purple">₹</span><div><b>Best Value</b><span>Transparent package pricing</span></div></div>
        <div class="trust-item"><span class="icon green">✈</span><div><b>Custom Trips</b><span>Domestic and international</span></div></div>
      </div>
    </div>

    <section class="page-section offers" id="offerSection">
      <div class="container">
        <div class="offer-banner">
          <div>
            <span class="badge orange">Limited Time Offer</span>
            <h2 id="offerTitle">Special Kashmir Group Tour</h2>
            <p id="offerSubtitle">Durga Puja departure from Kolkata. Limited seats with curated hotel, transport and sightseeing support.</p>
          </div>
          <a class="btn primary" id="offerButton" href="#quote">View Offer</a>
        </div>
      </div>
    </section>

    <section class="page-section" id="packages">
      <div class="container">
        <div class="section-title">
          <div><span class="badge purple">Popular Packages</span><h2>Tour packages made for every traveller</h2><p>Explore group tours, family holidays, honeymoon trips, hill escapes and international journeys.</p></div>
          <a href="contact.html" class="btn ghost">Request Custom Package</a>
        </div>
        <div class="cards-grid" id="packageGrid"></div>
      </div>
    </section>

    <section class="page-section" id="destinations">
      <div class="container">
        <div class="section-title">
          <div><span class="badge green">Destination Discovery</span><h2>Top destinations</h2><p>Choose from India and international destinations with packages, hotels and expert support.</p></div>
        </div>
        <div class="cards-grid" id="destinationGrid"></div>
      </div>
    </section>

    <section class="page-section" id="hotels">
      <div class="container">
        <div class="section-title">
          <div><span class="badge orange">Hotels & Stays</span><h2>Recommended stays</h2><p>Manageable hotel cards connected with destinations and tour packages.</p></div>
        </div>
        <div class="cards-grid" id="hotelGrid"></div>
      </div>
    </section>

    <section class="page-section" id="blogs">
      <div class="container">
        <div class="section-title">
          <div><span class="badge">Travel Stories</span><h2>Guides and travel ideas</h2><p>Useful travel content to help customers plan better trips.</p></div>
        </div>
        <div class="cards-grid" id="blogGrid"></div>
      </div>
    </section>


    <section class="page-section" id="galleryHome">
      <div class="container">
        <div class="section-title"><div><span class="badge orange">Travel Gallery</span><h2>Attractive tour photo section</h2><p>Curated stock-style reference photos give customers a clean, premium and trustworthy travel feel.</p></div><a class="btn ghost" href="gallery.html">Open Gallery</a></div>
        <div class="gallery-grid" data-gallery></div>
      </div>
    </section>

    <section class="page-section" id="reviewsFaq">
      <div class="container section-split">
        <div><div class="section-title"><div><span class="badge purple">Reviews</span><h2>Customer trust</h2></div></div><div class="cards-grid" data-testimonials></div></div>
        <div><div class="section-title"><div><span class="badge teal">FAQ</span><h2>Common questions</h2></div></div><div class="feature-list" data-faqs></div></div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" alt="TOURIM" style="height:64px;background:#fff;border-radius:14px;padding:4px">
          <p id="footerTagline">See the world, Feel with TOURIM</p>
        </div>
        <div><h4>Explore</h4><a href="#packages">Packages</a><br><a href="services.html">Services</a><br><a href="#destinations">Destinations</a><br><a href="#hotels">Hotels</a><br><a href="#blogs">Blogs</a></div>
        <div><h4>Contact</h4><p id="footerContact">Phone: 7384732179<br>Email: owner.tourim@gmail.com</p></div>
        <div><h4>Address</h4><p id="footerAddress">Ashokenagar Kachua More, North 24 Parganas, 743272</p></div>
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
      <div class="copyright">© 2026 TOURIM Travel Agency. All rights reserved. <span id="footerCredit">Developed by Abhijit Bhattacharjee</span></div>
    </div>
  </footer>

  <div class="modal-backdrop" id="detailModal"><div class="modal"><div class="modal-head"><div><span class="badge purple" id="modalBadge">Package</span><h2 id="modalTitle" style="margin:8px 0 0"></h2></div><button class="close-btn" id="closeModal">×</button></div><div id="modalBody"></div></div></div>
  <div class="toast"></div>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/data.js?v=20260624-v3"></script>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/site.js?v=20260624-v3"></script>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/pages.js?v=20260624-v3"></script>
  <?php wp_footer(); ?>
</body>
</html>

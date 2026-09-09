<?php
/*
Template Name: TOURIM Hotels
*/
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hotels | TOURIM</title>
  <meta name="description" content="Recommended TOURIM hotels and stays." />
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/styles.css?v=20260814-v5">
  <?php tourim_wp_template_meta(); ?>
  <?php wp_head(); ?>
</head>
<body data-page="hotels">

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
      <a class="pill phone-pill" data-tel href="tel:7384732179">☎ <span data-phone>7384732179</span></a>
      <a class="btn primary" href="contact.html">Plan Trip</a>
      <button class="btn ghost mobile-menu" id="mobileMenuBtn">☰</button>
    </div>
  </div>
</header>

<section class="page-hero">
  <div class="container"><div class="breadcrumb"><a href="index.html">Home</a><span>›</span><span>Hotels</span></div><h1>Recommended hotels and stays</h1><p>Browse curated stays by destination, category, meal plan and travel style.</p><div class="page-toolbar"><input id="pageSearch" placeholder="Search hotels by name, destination or category"></div></div>
</section>
<section class="page-section"><div class="container"><div class="cards-grid" data-grid></div></div></section>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" alt="TOURIM" style="height:64px;background:#fff;border-radius:14px;padding:4px"><p data-tagline>See the world, Feel with TOURIM</p><div class="action-row"><a class="btn teal" data-wa target="_blank">WhatsApp</a></div></div>
      <div><h4>Explore</h4><a href="packages.html">Packages</a><br><a href="services.html">Services</a><br><a href="destinations.html">Destinations</a><br><a href="hotels.html">Hotels</a><br><a href="blogs.html">Blogs</a><br><a href="gallery.html">Gallery</a></div>
      <div><h4>Contact</h4><p>Phone: <span data-phone>7384732179</span><br>Email: <span data-email>owner.tourim@gmail.com</span></p></div>
      <div><h4>Address</h4><p data-address>Kachua more, Ashoknagar to Jirat road, Habra</p></div>
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
    <div class="copyright">© 2026 TOURIM Travel Agency. All rights reserved.</div>
  </div>
</footer>
<div class="toast"></div>
<script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/data.js?v=20260624-v3"></script>
<script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/pages.js?v=20260624-v3"></script>
<script>
const mobileMenuBtn=document.getElementById('mobileMenuBtn');
mobileMenuBtn?.addEventListener('click',()=>document.getElementById('navLinks')?.classList.toggle('is-open'));
document.querySelectorAll('.nav-links a').forEach(a=>{if(location.pathname.endsWith(a.getAttribute('href')))a.classList.add('active-page')});
</script>
  <?php wp_footer(); ?>
</body>
</html>

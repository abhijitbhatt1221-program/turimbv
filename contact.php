<?php
/*
Template Name: TOURIM Contact
*/
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact | TOURIM</title>
  <meta name="description" content="Contact TOURIM and submit a travel enquiry." />
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/styles.css?v=20260814-v5">
  <?php tourim_wp_template_meta(); ?>
  <?php wp_head(); ?>
</head>
<body data-page="contact">

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

<section class="page-hero"><div class="container"><div class="breadcrumb"><a href="index.html">Home</a><span>›</span><span>Contact</span></div><h1>Get your custom travel quotation</h1><p>Share your destination, date, group size, budget and hotel preference. TOURIM will contact you with the best possible plan.</p></div></section>
<section class="page-section"><div class="container contact-grid"><div class="panel"><h2>Submit enquiry</h2><form class="admin-form" id="contactForm"><input name="name" placeholder="Full name" required><input name="phone" placeholder="Phone / WhatsApp" required><input name="email" placeholder="Email optional"><input name="destination" placeholder="Destination"><input name="package" placeholder="Package name"><input name="hotel" placeholder="Hotel preference optional"><input name="date" type="date"><input name="pax" placeholder="Guests e.g. 2 Adults + 1 Child"><input name="budget" placeholder="Approx budget"><textarea name="message" placeholder="Special request, meal plan, room type, route preference"></textarea><button class="btn primary" type="submit">Send Enquiry</button></form></div><aside class="contact-card"><h2>Direct contact</h2><p><b>Call:</b> <span data-phone>7384732179</span><br><b>Email:</b> <span data-email>owner.tourim@gmail.com</span><br><b>Office:</b> <span data-address>Kachua more, Ashoknagar to Jirat road, Habra</span></p><div class="action-row"><a class="btn teal" data-wa target="_blank">WhatsApp now</a><a class="btn ghost" data-tel>Call now</a></div><hr style="border:0;border-top:1px solid var(--line);margin:22px 0"><h3>Office services</h3><ul><li>Domestic package planning</li><li>International package planning</li><li>Hotel and vehicle support</li><li>Group tour and honeymoon support</li></ul></aside></div></section>

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

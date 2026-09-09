# TOURIM Complete System Design

## Architecture

```mermaid
flowchart TD
Customer[Customer Website] --> Frontend[HTML/CSS/JS UI]
Frontend --> API[PHP API]
API --> JSON[(JSON DB for MVP)]
Admin[Admin Dashboard] --> API
API --> Mail[SMTP Mail]
Frontend --> Analytics[Analytics Event Tracking]
Analytics --> JSON
```

## Production Migration

```mermaid
flowchart TD
Web[Next.js / React Frontend] --> Gateway[API Layer]
Gateway --> DB[(MySQL/PostgreSQL)]
Gateway --> Storage[Cloudinary/S3 Media Storage]
Gateway --> WhatsApp[WhatsApp Business API]
Gateway --> Email[SMTP/Email Provider]
Gateway --> Payment[Razorpay/Cashfree]
Admin[Admin CMS] --> Gateway
```

## Modules

- Public website: home, packages, package details, destinations, destination details, hotels, blogs, blog details, offers, gallery, about, contact.
- Admin CMS: dashboard, package manager, destination manager, hotel manager, blog manager, offer manager, enquiry manager, homepage manager, settings, notifications.
- API: get data, login, save CMS, submit enquiry, track analytics event, SMTP email.
- Data: settings, homepage, packages, destinations, hotels, blogs, offers, enquiries, analytics, notifications, activity, testimonials, FAQs, gallery.

## Rationale

The build keeps the current PHP/static hosting compatibility but adds scalable structure. Shared hosting can run immediately, while the schema and docs prepare TOURIM for MySQL, role-based users, payment, WhatsApp API, and a full booking engine.

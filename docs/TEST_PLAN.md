# TOURIM Test Plan

## Public website

- Home page loads.
- Packages page search works.
- Package details page opens from card.
- Destinations page opens and detail page works.
- Hotel enquiry link works.
- Blog detail page opens.
- Offer claim link opens contact page.
- Contact form saves enquiry.
- Mobile menu works.
- WhatsApp and call buttons open correctly.

## Admin

- `/ADMIN` opens admin panel.
- Login works on hosted PHP.
- Package CRUD works.
- Destination CRUD works.
- Hotel CRUD works.
- Blog CRUD works.
- Offer CRUD works.
- Query status and note update works.
- CSV export works.
- Homepage manager draft/preview/commit works.
- Settings update hashes password server-side.
- Notifications render.

## Security

- `data/tourim_db.json` blocked by browser.
- Wrong admin password fails.
- Public API does not return `adminPassword` or `adminPasswordHash`.
- SMTP password is not committed in ZIP.

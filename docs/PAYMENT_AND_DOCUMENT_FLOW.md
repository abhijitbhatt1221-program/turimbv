# TOURIM Payment, Invoice and Voucher Flow

1. Admin opens `production-tools.html`.
2. Sales team saves booking details.
3. Admin records advance or balance payment.
4. Admin generates invoice.
5. Admin generates travel voucher.
6. Document opens in browser and can be printed or saved as PDF.
7. Payment and document records are stored in the TOURIM database.

This does not charge money automatically. Razorpay keys are prepared in `.env.example`; live gateway integration can be attached later without changing the admin workflow.

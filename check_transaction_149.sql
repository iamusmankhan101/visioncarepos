-- Check transaction 149 and its contact details
SELECT 
    t.id as transaction_id,
    t.invoice_no,
    t.contact_id,
    t.business_id,
    c.name as contact_name,
    c.mobile,
    c.email,
    nt.template_for,
    nt.auto_send_wa_notif,
    nt.whatsapp_text
FROM transactions t
LEFT JOIN contacts c ON t.contact_id = c.id
LEFT JOIN notification_templates nt ON nt.business_id = t.business_id 
WHERE t.id = 149 
AND nt.template_for IN ('order_ready', 'order_delivered');
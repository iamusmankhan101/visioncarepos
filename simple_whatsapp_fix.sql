-- Simple WhatsApp Fix - Run this SQL directly in your database

-- First, check what notification templates exist
SELECT id, business_id, template_for, auto_send_wa_notif, 
       CASE WHEN LENGTH(whatsapp_text) > 0 THEN 'HAS TEXT' ELSE 'EMPTY' END as whatsapp_status
FROM notification_templates 
WHERE template_for IN ('order_ready', 'order_delivered');

-- Create Order Ready template if it doesn't exist
INSERT IGNORE INTO notification_templates 
(business_id, template_for, subject, email_body, sms_body, whatsapp_text, auto_send, auto_send_sms, auto_send_wa_notif, cc, bcc, created_at, updated_at)
SELECT 
    1 as business_id,  -- Change this to your business ID
    'order_ready' as template_for,
    'Order Ready - {business_name}' as subject,
    '<p>Dear {contact_name},</p><p>Your order {invoice_number} is ready for pickup!</p>' as email_body,
    'Dear {contact_name}, Your order {invoice_number} is ready! {business_name}' as sms_body,
    'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}' as whatsapp_text,
    0 as auto_send,
    0 as auto_send_sms,
    1 as auto_send_wa_notif,  -- Enable WhatsApp
    '' as cc,
    '' as bcc,
    NOW() as created_at,
    NOW() as updated_at;

-- Create Order Delivered template if it doesn't exist
INSERT IGNORE INTO notification_templates 
(business_id, template_for, subject, email_body, sms_body, whatsapp_text, auto_send, auto_send_sms, auto_send_wa_notif, cc, bcc, created_at, updated_at)
SELECT 
    1 as business_id,  -- Change this to your business ID
    'order_delivered' as template_for,
    'Order Delivered - {business_name}' as subject,
    '<p>Dear {contact_name},</p><p>Your order {invoice_number} has been delivered!</p>' as email_body,
    'Dear {contact_name}, Your order {invoice_number} has been delivered! {business_name}' as sms_body,
    'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}' as whatsapp_text,
    0 as auto_send,
    0 as auto_send_sms,
    1 as auto_send_wa_notif,  -- Enable WhatsApp
    '' as cc,
    '' as bcc,
    NOW() as created_at,
    NOW() as updated_at;

-- Update existing templates to enable WhatsApp
UPDATE notification_templates 
SET auto_send_wa_notif = 1,
    whatsapp_text = CASE 
        WHEN template_for = 'order_ready' THEN 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}'
        WHEN template_for = 'order_delivered' THEN 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}'
        ELSE whatsapp_text
    END
WHERE template_for IN ('order_ready', 'order_delivered');

-- Check the results
SELECT id, business_id, template_for, auto_send_wa_notif, 
       CASE WHEN LENGTH(whatsapp_text) > 0 THEN 'HAS TEXT' ELSE 'EMPTY' END as whatsapp_status
FROM notification_templates 
WHERE template_for IN ('order_ready', 'order_delivered');

-- Check transaction 152 details
SELECT t.id, t.invoice_no, t.business_id, t.shipping_status, 
       c.name as customer_name, c.mobile
FROM transactions t
LEFT JOIN contacts c ON t.contact_id = c.id
WHERE t.id = 152;
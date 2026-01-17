-- Complete WhatsApp Fix with Database Selection
-- Run these commands in your MySQL/phpMyAdmin

-- Step 1: Select the database
USE homestead;

-- Step 2: Check what notification templates currently exist
SELECT id, business_id, template_for, auto_send_wa_notif, 
       CASE WHEN LENGTH(whatsapp_text) > 0 THEN 'HAS TEXT' ELSE 'EMPTY' END as whatsapp_status
FROM notification_templates 
WHERE template_for IN ('order_ready', 'order_delivered');

-- Step 3: Check transaction 152 details
SELECT t.id, t.invoice_no, t.business_id, t.shipping_status, 
       c.name as customer_name, c.mobile
FROM transactions t
LEFT JOIN contacts c ON t.contact_id = c.id
WHERE t.id = 152;

-- Step 4: Get all business IDs to create templates for all businesses
SELECT id, name FROM businesses;

-- Step 5: Create notification templates for ALL businesses
-- This will create templates for every business in your system
INSERT INTO notification_templates 
(business_id, template_for, subject, email_body, sms_body, whatsapp_text, auto_send, auto_send_sms, auto_send_wa_notif, cc, bcc, created_at, updated_at)
SELECT 
    b.id as business_id,
    'order_ready' as template_for,
    'Order Ready - {business_name}' as subject,
    '<p>Dear {contact_name},</p><p>Your order {invoice_number} is ready for pickup!</p><p>Total amount: {total_amount}</p><p>Please come to collect your order.</p><p>{business_logo}</p>' as email_body,
    'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}' as sms_body,
    'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}' as whatsapp_text,
    0 as auto_send,
    0 as auto_send_sms,
    1 as auto_send_wa_notif,
    '' as cc,
    '' as bcc,
    NOW() as created_at,
    NOW() as updated_at
FROM businesses b
WHERE NOT EXISTS (
    SELECT 1 FROM notification_templates nt 
    WHERE nt.business_id = b.id AND nt.template_for = 'order_ready'
);

INSERT INTO notification_templates 
(business_id, template_for, subject, email_body, sms_body, whatsapp_text, auto_send, auto_send_sms, auto_send_wa_notif, cc, bcc, created_at, updated_at)
SELECT 
    b.id as business_id,
    'order_delivered' as template_for,
    'Order Delivered - {business_name}' as subject,
    '<p>Dear {contact_name},</p><p>Your order {invoice_number} has been delivered!</p><p>Total amount: {total_amount}</p><p>Thank you for choosing us.</p><p>{business_logo}</p>' as email_body,
    'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}' as sms_body,
    'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}' as whatsapp_text,
    0 as auto_send,
    0 as auto_send_sms,
    1 as auto_send_wa_notif,
    '' as cc,
    '' as bcc,
    NOW() as created_at,
    NOW() as updated_at
FROM businesses b
WHERE NOT EXISTS (
    SELECT 1 FROM notification_templates nt 
    WHERE nt.business_id = b.id AND nt.template_for = 'order_delivered'
);

-- Step 6: Verify templates were created
SELECT id, business_id, template_for, auto_send_wa_notif, 
       CASE WHEN LENGTH(whatsapp_text) > 0 THEN 'HAS TEXT' ELSE 'EMPTY' END as whatsapp_status
FROM notification_templates 
WHERE template_for IN ('order_ready', 'order_delivered')
ORDER BY business_id, template_for;

-- Step 7: Check if customer for transaction 152 has mobile number
SELECT t.id, t.invoice_no, t.business_id, 
       c.name as customer_name, c.mobile,
       CASE WHEN c.mobile IS NULL OR c.mobile = '' THEN 'NO MOBILE' ELSE 'HAS MOBILE' END as mobile_status
FROM transactions t
LEFT JOIN contacts c ON t.contact_id = c.id
WHERE t.id = 152;
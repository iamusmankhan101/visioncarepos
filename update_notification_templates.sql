-- Update notification templates to change New Booking to Ready and New Quotation to Delivered

-- First, let's see what notification templates currently exist
SELECT id, business_id, template_for, subject FROM notification_templates 
WHERE template_for IN ('new_booking', 'new_quotation', 'order_ready', 'order_delivered');

-- Add Order Ready template for all businesses
INSERT INTO notification_templates (business_id, template_for, subject, email_body, sms_body, whatsapp_text, auto_send, auto_send_sms, auto_send_wa_notif, cc, bcc, created_at, updated_at)
SELECT 
    id as business_id,
    'order_ready' as template_for,
    'Order Ready - {business_name}' as subject,
    '<p>Dear {contact_name},</p>

<p>Your order {invoice_number} is ready for pickup!</p>

<p>Total amount: {total_amount}</p>

<p>Please come to collect your order at your earliest convenience.</p>

<p>{business_logo}</p>' as email_body,
    'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}' as sms_body,
    'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}' as whatsapp_text,
    0 as auto_send,
    0 as auto_send_sms,
    0 as auto_send_wa_notif,
    '' as cc,
    '' as bcc,
    NOW() as created_at,
    NOW() as updated_at
FROM businesses 
WHERE NOT EXISTS (
    SELECT 1 FROM notification_templates nt 
    WHERE nt.business_id = businesses.id 
    AND nt.template_for = 'order_ready'
);

-- Add Order Delivered template for all businesses
INSERT INTO notification_templates (business_id, template_for, subject, email_body, sms_body, whatsapp_text, auto_send, auto_send_sms, auto_send_wa_notif, cc, bcc, created_at, updated_at)
SELECT 
    id as business_id,
    'order_delivered' as template_for,
    'Order Delivered - {business_name}' as subject,
    '<p>Dear {contact_name},</p>

<p>Your order {invoice_number} has been delivered!</p>

<p>Total amount: {total_amount}</p>

<p>Thank you for choosing us.</p>

<p>{business_logo}</p>' as email_body,
    'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}' as sms_body,
    'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}' as whatsapp_text,
    0 as auto_send,
    0 as auto_send_sms,
    0 as auto_send_wa_notif,
    '' as cc,
    '' as bcc,
    NOW() as created_at,
    NOW() as updated_at
FROM businesses 
WHERE NOT EXISTS (
    SELECT 1 FROM notification_templates nt 
    WHERE nt.business_id = businesses.id 
    AND nt.template_for = 'order_delivered'
);

-- Verify the new templates were added
SELECT id, business_id, template_for, subject FROM notification_templates 
WHERE template_for IN ('order_ready', 'order_delivered')
ORDER BY business_id, template_for;
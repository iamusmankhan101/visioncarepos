-- Enable WhatsApp notifications for order status templates
UPDATE notification_templates 
SET auto_send_wa_notif = 1 
WHERE template_for IN ('order_ready', 'order_delivered');

-- Check the results
SELECT template_for, auto_send_wa_notif, whatsapp_text 
FROM notification_templates 
WHERE template_for IN ('order_ready', 'order_delivered');
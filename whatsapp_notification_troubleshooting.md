# WhatsApp Notification Troubleshooting Guide

## Issue: WhatsApp notifications not working for Order Status changes

### Understanding WhatsApp Notifications in this System

**Important**: This system doesn't send WhatsApp messages automatically. Instead, it:
1. Creates a WhatsApp web link with pre-filled message
2. Opens WhatsApp (web or app) with the message ready
3. **User still needs to manually click "Send"**

### Common Issues and Solutions

#### 1. **Auto-send WhatsApp is Disabled**
**Problem**: The notification template doesn't have WhatsApp auto-send enabled.

**Solution**: 
- Go to Admin Panel → Notification Templates
- Click on "Ready" or "Delivered" tab
- Check the "Auto send WhatsApp notification" checkbox
- Save the template

**Or run this script**: `php enable_whatsapp_notifications.php`

#### 2. **WhatsApp Text is Empty**
**Problem**: The WhatsApp message field is empty in the template.

**Solution**:
- Go to Notification Templates
- Fill in the "WhatsApp Text" field with your message
- Use tags like `{contact_name}`, `{invoice_number}`, `{business_name}`

#### 3. **Customer Has No Mobile Number**
**Problem**: The customer's contact doesn't have a mobile number.

**Solution**:
- Edit the customer's contact information
- Add a valid mobile number (with country code)
- Example: +1234567890

#### 4. **WhatsApp Link Not Opening**
**Problem**: The WhatsApp link is generated but doesn't open WhatsApp.

**Possible Causes**:
- Customer doesn't have WhatsApp installed
- Mobile number format is incorrect
- Browser blocking the WhatsApp link

#### 5. **Notification Not Triggered**
**Problem**: No notification is sent when order status changes.

**Check**:
- Order status is changing to "Ready" (packed) or "Delivered"
- Customer has a valid contact record
- Notification templates exist in database

### How to Test WhatsApp Notifications

1. **Run the debug script**: `php debug_whatsapp_notifications.php`
2. **Check a customer has mobile number**
3. **Change order status to "Ready" or "Delivered"**
4. **Look for WhatsApp link in logs or response**

### Manual Testing

You can test the WhatsApp link format manually:
```
https://wa.me/1234567890?text=Hello%20World
```

Replace:
- `1234567890` with customer's mobile number (no + or spaces)
- `Hello%20World` with URL-encoded message

### Expected Behavior

When order status changes to "Ready" or "Delivered":
1. System generates WhatsApp link
2. Link opens WhatsApp with pre-filled message
3. **User must manually click "Send" in WhatsApp**
4. Message is sent to customer

### Scripts Available

- `debug_whatsapp_notifications.php` - Diagnose issues
- `enable_whatsapp_notifications.php` - Enable auto-send
- `fix_notification_templates.php` - Fix template issues

### Important Notes

- WhatsApp notifications are **not fully automatic**
- They require manual action to send the final message
- Customer must have WhatsApp installed
- Mobile number must be valid and include country code
- Some browsers may block WhatsApp links for security

### Alternative Solutions

If WhatsApp web links don't work well, consider:
1. **SMS notifications** (fully automatic)
2. **Email notifications** (fully automatic)
3. **WhatsApp Business API** (requires paid service)
4. **Manual WhatsApp messaging**
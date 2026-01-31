#!/bin/bash

echo "📱 DEPLOYING WHATSAPP ORDER STATUS FIX"
echo "======================================"
echo ""

echo "1. Enabling WhatsApp notifications..."
php enable_order_status_whatsapp.php

echo ""
echo "2. Testing WhatsApp functionality..."
php test_whatsapp_order_status.php

echo ""
echo "3. Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo ""
echo "✅ WHATSAPP ORDER STATUS FIX DEPLOYED!"
echo "====================================="
echo ""

echo "📱 What's now working:"
echo "1. ✅ Order status modal popup (both sales and shipments pages)"
echo "2. ✅ WhatsApp notification templates enabled"
echo "3. ✅ Auto-send enabled for order status changes"
echo "4. ✅ WhatsApp link generation and display"
echo "5. ✅ Enhanced JavaScript handling for WhatsApp responses"
echo ""

echo "🎯 How it works:"
echo "1. Click order status button → Modal appears"
echo "2. Change status to 'Ready' or 'Delivered' → Status updates"
echo "3. WhatsApp notification automatically generated"
echo "4. WhatsApp popup appears with 'Open WhatsApp' button"
echo "5. Click button → WhatsApp opens with pre-filled message"
echo ""

echo "📋 Status triggers:"
echo "• 'Ready' (packed) → Sends 'order_ready' WhatsApp message"
echo "• 'Delivered' → Sends 'order_delivered' WhatsApp message"
echo "• 'Ordered' → No automatic WhatsApp (initial status)"
echo ""

echo "🔄 Test steps:"
echo "1. Go to pending shipments or sales page"
echo "2. Click any order status button"
echo "3. Change status to 'Ready' or 'Delivered'"
echo "4. Look for WhatsApp notification popup"
echo "5. Click 'Open WhatsApp' to send message"
echo ""

echo "🐛 If WhatsApp still not working:"
echo "1. Check browser console for JavaScript errors"
echo "2. Verify customer has mobile number"
echo "3. Check notification templates are enabled"
echo "4. Verify business WhatsApp settings"
echo ""
// Browser Console Test for WhatsApp Notifications
// Copy and paste this in your browser console (F12) on the sales page

console.log("=== WhatsApp Notification Test ===");

// Test 1: Check if the order status update is working
console.log("1. Testing order status update...");

// Simulate form submission (replace 152 with your transaction ID)
var testData = {
    shipping_status: 'packed'  // This should trigger "Ready" notification
};

$.ajax({
    url: '/sells/update-order-status/152',
    method: 'PUT',
    data: testData,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(result) {
        console.log("✅ Order status update successful:", result);
        
        if (result.whatsapp_link) {
            console.log("🎉 WhatsApp link found:", result.whatsapp_link);
            console.log("Opening WhatsApp...");
            window.open(result.whatsapp_link, '_blank');
        } else {
            console.log("❌ No WhatsApp link in response");
            console.log("Check if:");
            console.log("- Customer has mobile number");
            console.log("- WhatsApp auto-send is enabled");
            console.log("- Notification templates exist");
        }
    },
    error: function(xhr) {
        console.error("❌ Order status update failed:", xhr.responseText);
    }
});

// Test 2: Check current page for debugging info
console.log("\n2. Page debugging info:");
console.log("Current URL:", window.location.href);
console.log("CSRF Token:", $('meta[name="csrf-token"]').attr('content'));
console.log("jQuery version:", $.fn.jquery);

// Test 3: Manual WhatsApp link test
console.log("\n3. Manual WhatsApp link test:");
var testMobile = "1234567890";  // Replace with actual mobile
var testMessage = "Test message from browser console";
var testLink = "https://wa.me/" + testMobile + "?text=" + encodeURIComponent(testMessage);
console.log("Test WhatsApp link:", testLink);
console.log("Click this link to test WhatsApp:", testLink);
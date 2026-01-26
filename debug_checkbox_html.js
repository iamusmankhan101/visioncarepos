// Debug script to check checkbox HTML structure
// Run this in browser console on the Add User page

console.log('🔍 DEBUGGING CHECKBOX HTML STRUCTURE');
console.log('=====================================');

// Check if checkboxes exist in HTML
var allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
console.log('Total checkboxes found:', allCheckboxes.length);

var icheckCheckboxes = document.querySelectorAll('input[type="checkbox"].input-icheck');
console.log('iCheck checkboxes found:', icheckCheckboxes.length);

// List all checkboxes
console.log('\n📋 All checkboxes:');
allCheckboxes.forEach(function(checkbox, index) {
    var name = checkbox.name || checkbox.id || 'unnamed-' + index;
    var classes = checkbox.className;
    var visible = checkbox.offsetParent !== null;
    var computed = window.getComputedStyle(checkbox);
    
    console.log(`${index + 1}. ${name}`);
    console.log(`   Classes: ${classes}`);
    console.log(`   Visible: ${visible}`);
    console.log(`   Display: ${computed.display}`);
    console.log(`   Visibility: ${computed.visibility}`);
    console.log(`   Opacity: ${computed.opacity}`);
    console.log(`   Position: ${computed.position}`);
    console.log('   ---');
});

// Check for iCheck wrappers
var icheckWrappers = document.querySelectorAll('.icheckbox_square-blue, .iradio_square-blue');
console.log('\n🎨 iCheck wrappers found:', icheckWrappers.length);

// Check if jQuery and iCheck are loaded
console.log('\n🔧 Dependencies:');
console.log('jQuery loaded:', typeof jQuery !== 'undefined');
console.log('iCheck available:', typeof jQuery !== 'undefined' && typeof jQuery.fn.iCheck !== 'undefined');

// Check CSS files
console.log('\n📄 CSS Files:');
var stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
stylesheets.forEach(function(sheet) {
    if (sheet.href.includes('vendor') || sheet.href.includes('app')) {
        console.log('- ' + sheet.href);
    }
});

// Manual fix function
window.manualCheckboxFix = function() {
    console.log('🔧 MANUAL CHECKBOX FIX');
    
    var checkboxes = document.querySelectorAll('input[type="checkbox"].input-icheck');
    checkboxes.forEach(function(checkbox) {
        checkbox.style.display = 'inline-block';
        checkbox.style.visibility = 'visible';
        checkbox.style.opacity = '1';
        checkbox.style.position = 'static';
        checkbox.style.width = '16px';
        checkbox.style.height = '16px';
        checkbox.style.marginRight = '8px';
    });
    
    console.log('Fixed', checkboxes.length, 'checkboxes');
};

console.log('\n💡 To manually fix checkboxes, run: manualCheckboxFix()');
console.log('=====================================');
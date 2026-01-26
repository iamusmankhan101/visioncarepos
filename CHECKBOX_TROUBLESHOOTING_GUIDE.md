# Checkbox Troubleshooting Guide

## 🚨 EMERGENCY STEPS - If No Checkboxes Are Showing

### Step 1: Immediate Browser Console Check
1. Open the Add User page
2. Press F12 to open Developer Tools
3. Go to Console tab
4. Copy and paste this code:

```javascript
// Emergency checkbox visibility fix
var checkboxes = document.querySelectorAll('input[type="checkbox"].input-icheck');
console.log('Found', checkboxes.length, 'checkboxes');

checkboxes.forEach(function(checkbox, index) {
    var name = checkbox.name || checkbox.id || 'checkbox-' + index;
    console.log('Fixing:', name);
    
    checkbox.style.display = 'inline-block';
    checkbox.style.visibility = 'visible';
    checkbox.style.opacity = '1';
    checkbox.style.position = 'static';
    checkbox.style.width = '16px';
    checkbox.style.height = '16px';
    checkbox.style.marginRight = '8px';
    checkbox.style.zIndex = '999';
});

console.log('Emergency fix applied to', checkboxes.length, 'checkboxes');
```

### Step 2: Check HTML Structure
Run this in console to see if checkboxes exist in HTML:

```javascript
// Check checkbox HTML
var allInputs = document.querySelectorAll('input[type="checkbox"]');
console.log('Total checkboxes in HTML:', allInputs.length);

allInputs.forEach(function(input, i) {
    console.log(i + 1 + '.', input.name || input.id, '- Classes:', input.className);
});
```

### Step 3: Verify Dependencies
Check if required files are loaded:

```javascript
// Check dependencies
console.log('jQuery:', typeof jQuery !== 'undefined');
console.log('iCheck:', typeof jQuery !== 'undefined' && typeof jQuery.fn.iCheck !== 'undefined');

// Check CSS files
var css = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
    .map(link => link.href)
    .filter(href => href.includes('vendor') || href.includes('app'));
console.log('CSS files:', css);
```

## 🔍 Diagnostic Steps

### 1. Check Browser Network Tab
1. Open F12 Developer Tools
2. Go to Network tab
3. Reload the page
4. Look for these files:
   - `vendor.css` (should contain iCheck styles)
   - `app.js` (should contain iCheck plugin)
   - `blue.png` (iCheck checkbox image)

### 2. Check Console for Errors
Look for these error messages:
- `iCheck is not a function`
- `Cannot read property 'iCheck' of undefined`
- CSS loading errors
- JavaScript syntax errors

### 3. Inspect Element Structure
1. Right-click where checkboxes should be
2. Select "Inspect Element"
3. Look for:
   ```html
   <input type="checkbox" class="input-icheck" name="is_active">
   ```

## 🛠️ Common Fixes

### Fix 1: CSS Override
Add this to browser console:

```javascript
var style = document.createElement('style');
style.innerHTML = `
    input[type="checkbox"].input-icheck {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 16px !important;
        height: 16px !important;
        margin-right: 8px !important;
    }
`;
document.head.appendChild(style);
```

### Fix 2: Force iCheck Initialization
```javascript
if (typeof jQuery !== 'undefined' && typeof jQuery.fn.iCheck !== 'undefined') {
    jQuery('input[type="checkbox"].input-icheck').each(function() {
        var $this = jQuery(this);
        try {
            $this.iCheck('destroy');
        } catch(e) {}
        
        $this.iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });
    });
    console.log('iCheck reinitialized');
} else {
    console.log('iCheck not available');
}
```

### Fix 3: Remove iCheck Wrappers
If checkboxes are wrapped but hidden:

```javascript
jQuery('input[type="checkbox"].input-icheck').each(function() {
    var $input = jQuery(this);
    if ($input.parent().hasClass('icheckbox_square-blue')) {
        $input.unwrap();
        $input.show();
    }
});
```

## 🎯 Specific Checkbox Locations

### Status Checkbox
- **Name**: `is_active`
- **Location**: Top section, "Is active" label
- **Fix**: `document.querySelector('input[name="is_active"]').style.display = 'inline-block'`

### Allow Login Checkbox
- **Name**: `allow_login`
- **Location**: Roles section, "Allow Login" label
- **Fix**: `document.querySelector('input[name="allow_login"]').style.display = 'inline-block'`

### All Locations Checkbox
- **Name**: `access_all_locations`
- **Location**: Access Locations section
- **Fix**: `document.querySelector('input[name="access_all_locations"]').style.display = 'inline-block'`

### Individual Location Checkboxes
- **Name**: `location_permissions[]`
- **Location**: Below "All Locations" checkbox
- **Fix**: 
```javascript
document.querySelectorAll('input[name="location_permissions[]"]').forEach(function(cb) {
    cb.style.display = 'inline-block';
});
```

## 🚀 Permanent Fixes Applied

### 1. Emergency CSS Added
The view now includes emergency CSS that forces checkboxes to be visible.

### 2. JavaScript Fix Enhanced
Multiple initialization attempts with fallbacks.

### 3. Monitoring Added
Automatic detection and fixing of disappearing checkboxes.

## 📞 If Nothing Works

### Last Resort Manual Fix
1. Open browser console
2. Run: `manualCheckboxFix()` (if available)
3. Or copy the emergency fix code from Step 1 above

### Report Issue
If checkboxes still don't show:
1. Take screenshot of console errors
2. Check Network tab for failed file loads
3. Note browser version and type
4. Check if other pages have same issue

## ✅ Success Indicators

You'll know it's working when:
- Checkboxes are visible next to labels
- Clicking checkboxes toggles their state
- Console shows "Emergency fix applied" or "iCheck initialized"
- No JavaScript errors in console

## 🔄 Testing Checklist

- [ ] Status checkbox visible and clickable
- [ ] Allow Login checkbox visible and clickable
- [ ] All Locations checkbox visible and clickable
- [ ] Individual location checkboxes visible and clickable
- [ ] Service Staff Pin checkbox visible and clickable
- [ ] Selected Contacts checkbox visible and clickable
- [ ] No console errors
- [ ] Checkboxes maintain state when clicked
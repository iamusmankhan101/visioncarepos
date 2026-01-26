# Checkbox Label Text Fix Guide

## 🏷️ Problem: Checkbox Labels Not Showing

The checkboxes are visible but the text labels (like "Status for user", "Allow Login", etc.) are missing or hidden.

## 🚨 IMMEDIATE FIX - Browser Console

### Step 1: Quick Label Restore
Open browser console (F12) and paste this code:

```javascript
// Emergency label text fix
var labels = {
    'is_active': 'Status for user',
    'allow_login': 'Allow Login', 
    'is_enable_service_staff_pin': 'Enable service staff pin',
    'access_all_locations': 'All Locations',
    'selected_contacts': 'Allow selected contacts'
};

Object.keys(labels).forEach(function(name) {
    var input = document.querySelector('input[name="' + name + '"]');
    if (input) {
        var label = input.closest('label');
        if (label && !label.textContent.trim()) {
            var span = document.createElement('span');
            span.textContent = ' ' + labels[name];
            span.style.marginLeft = '5px';
            span.style.color = '#333';
            label.appendChild(span);
        }
    }
});

console.log('Labels restored!');
```

### Step 2: Fix Location Labels
```javascript
// Fix location permission labels
var locationInputs = document.querySelectorAll('input[name="location_permissions[]"]');
locationInputs.forEach(function(input, i) {
    var label = input.closest('label');
    if (label && !label.textContent.trim()) {
        var span = document.createElement('span');
        span.textContent = ' Location ' + (i + 1);
        span.style.marginLeft = '5px';
        span.style.color = '#333';
        label.appendChild(span);
    }
});
```

## 🔍 Diagnostic Steps

### Check Label Structure
```javascript
// Check if labels exist but are hidden
var checkboxes = document.querySelectorAll('input[type="checkbox"].input-icheck');
checkboxes.forEach(function(cb, i) {
    var label = cb.closest('label');
    console.log(i + 1 + '.', cb.name, 'Label text:', label ? label.textContent : 'NO LABEL');
});
```

### Check CSS Issues
```javascript
// Check if CSS is hiding labels
var labels = document.querySelectorAll('.checkbox label, .radio label');
labels.forEach(function(label, i) {
    var style = window.getComputedStyle(label);
    console.log('Label ' + i + ':', {
        display: style.display,
        visibility: style.visibility,
        color: style.color,
        fontSize: style.fontSize
    });
});
```

## 🛠️ Permanent Fixes Applied

### 1. Enhanced CSS
Added CSS to ensure label text is always visible:
```css
.checkbox label,
.radio label {
  font-weight: normal !important;
  color: #333 !important;
  font-size: 14px !important;
}

.checkbox label .label-text,
.radio label .label-text {
  display: inline !important;
  visibility: visible !important;
  color: #333 !important;
  margin-left: 5px !important;
}
```

### 2. JavaScript Label Preservation
Enhanced JavaScript to:
- Store original label text before iCheck initialization
- Restore label text after iCheck processes the checkboxes
- Monitor for missing labels and fix them automatically

### 3. Label Text Restoration
Added automatic restoration of label text that gets lost during iCheck initialization.

## 📋 Expected Labels

These are the labels that should be visible:

| Checkbox | Expected Label |
|----------|----------------|
| `is_active` | "Status for user" |
| `allow_login` | "Allow Login" |
| `is_enable_service_staff_pin` | "Enable service staff pin" |
| `access_all_locations` | "All Locations" |
| `location_permissions[]` | Location names (e.g., "Vision Care (BL0001)") |
| `selected_contacts` | "Allow selected contacts" |

## 🔧 Manual Fix Functions

### Available Console Commands

1. **Fix All Labels**: `fixCheckboxLabels()`
2. **Emergency Fix**: Copy the code from Step 1 above
3. **Check Label Status**: Use diagnostic code above

### CSS Override
If labels are still hidden, add this CSS:
```javascript
var style = document.createElement('style');
style.innerHTML = `
    .checkbox label, .radio label {
        display: inline-block !important;
        color: #333 !important;
        font-size: 14px !important;
        font-weight: normal !important;
    }
    .checkbox label span, .radio label span {
        display: inline !important;
        visibility: visible !important;
    }
`;
document.head.appendChild(style);
```

## 🎯 Testing Checklist

- [ ] "Status for user" text visible next to first checkbox
- [ ] "Allow Login" text visible in Roles section
- [ ] "Enable service staff pin" text visible
- [ ] "All Locations" text visible in Access Locations
- [ ] Individual location names visible (Vision Care, test, etc.)
- [ ] "Allow selected contacts" text visible in Sales section
- [ ] All labels are clickable and toggle checkboxes
- [ ] Text is readable (not too small or faded)

## 🚀 Success Indicators

You'll know it's working when:
- All checkbox labels are visible and readable
- Clicking label text toggles the checkbox
- Text appears in normal black color
- No console errors related to labels

## 📞 If Labels Still Missing

1. **Clear browser cache** and reload page
2. **Check browser console** for JavaScript errors
3. **Run emergency fix** from Step 1 above
4. **Inspect element** to see if text nodes exist but are hidden
5. **Try different browser** to rule out browser-specific issues

The label fix ensures that even if iCheck plugin interferes with the label text, it will be automatically restored and made visible.
// Emergency fix for missing checkbox label text
// Paste this in browser console if labels are missing

console.log('🚨 EMERGENCY LABEL FIX');

// Define the expected labels for each checkbox
var expectedLabels = {
    'is_active': 'Status for user',
    'allow_login': 'Allow Login',
    'is_enable_service_staff_pin': 'Enable service staff pin',
    'access_all_locations': 'All Locations',
    'selected_contacts': 'Allow selected contacts'
};

// Fix each checkbox
Object.keys(expectedLabels).forEach(function(inputName) {
    var input = document.querySelector('input[name="' + inputName + '"]');
    if (input) {
        var label = input.closest('label');
        if (label) {
            // Check if text is missing
            var textContent = label.textContent.trim();
            if (!textContent || textContent.length === 0) {
                console.log('Fixing label for: ' + inputName);
                
                // Add text span
                var textSpan = document.createElement('span');
                textSpan.textContent = ' ' + expectedLabels[inputName];
                textSpan.style.marginLeft = '5px';
                textSpan.style.color = '#333';
                textSpan.style.fontWeight = 'normal';
                label.appendChild(textSpan);
            }
        }
    }
});

// Fix location checkboxes
var locationInputs = document.querySelectorAll('input[name="location_permissions[]"]');
locationInputs.forEach(function(input, index) {
    var label = input.closest('label');
    if (label) {
        var textContent = label.textContent.trim();
        if (!textContent || textContent.length === 0) {
            console.log('Fixing location label: ' + index);
            
            // Get location name from value or use generic text
            var value = input.value || '';
            var locationName = value.replace('location.', '') || 'Location ' + (index + 1);
            
            var textSpan = document.createElement('span');
            textSpan.textContent = ' ' + locationName;
            textSpan.style.marginLeft = '5px';
            textSpan.style.color = '#333';
            textSpan.style.fontWeight = 'normal';
            label.appendChild(textSpan);
        }
    }
});

console.log('✅ Emergency label fix completed');
console.log('If labels are still missing, try refreshing the page');
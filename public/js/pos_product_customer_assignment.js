/**
 * POS Product-Customer Assignment Module
 * 
 * This module handles assigning specific products to specific customers
 * when multiple customers are selected in a POS transaction.
 * Automatically includes related customers (family members) based on phone number.
 */

(function() {
    'use strict';
    
    // Store selected customers globally
    window.posSelectedCustomers = [];
    
    // Store related customers cache
    var relatedCustomersCache = {};
    
    /**
     * Initialize product-customer assignment functionality
     */
    function initProductCustomerAssignment() {
        console.log('🎯 Initializing Product-Customer Assignment with Related Customers');
        
        // Listen for customer selection changes
        $(document).on('change', '#customer_id', function() {
            var customerId = $(this).val();
            if (customerId) {
                fetchAndAddRelatedCustomers(customerId);
            } else {
                updateSelectedCustomers();
            }
        });
        
        // Listen for product row additions
        $(document).on('DOMNodeInserted', '#pos_table tbody', function(e) {
            if ($(e.target).is('tr.product_row')) {
                populateCustomerDropdownForRow($(e.target));
            }
        });
        
        // Alternative: Use MutationObserver for better performance
        observeProductTableChanges();
        
        // Initial population if products already exist
        populateAllCustomerDropdowns();
    }
    
    /**
     * Fetch related customers from server and add them to selection
     */
    function fetchAndAddRelatedCustomers(customerId) {
        console.log('🔍 Fetching related customers for:', customerId);
        
        // Check cache first
        if (relatedCustomersCache[customerId]) {
            console.log('✅ Using cached related customers');
            addCustomersToSelection(relatedCustomersCache[customerId]);
            return;
        }
        
        // Fetch from server
        $.ajax({
            url: '/contacts/' + customerId + '/related-customers',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('📥 Related customers response:', response);
                
                if (response.success && response.has_related && response.customers) {
                    // Cache the results
                    relatedCustomersCache[customerId] = response.customers;
                    
                    // Add all related customers to selection
                    addCustomersToSelection(response.customers);
                    
                    // Show notification
                    if (typeof toastr !== 'undefined') {
                        var count = response.customers.length;
                        toastr.info('Found ' + count + ' related customer(s) - they have been added to the dropdown', 'Related Customers');
                    }
                } else {
                    // No related customers, just add the selected one
                    updateSelectedCustomers();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching related customers:', error);
                // Fallback to just adding the selected customer
                updateSelectedCustomers();
                
                // If it's a 500 error, might be database issue
                if (xhr.status === 500) {
                    console.warn('⚠️ Server error - database column might not exist yet. Run migration first.');
                }
            }
        });
    }
    
    /**
     * Add multiple customers to selection
     */
    function addCustomersToSelection(customers) {
        // Clear existing selections
        window.posSelectedCustomers = [];
        
        customers.forEach(function(customer) {
            var displayName = customer.name;
            
            // Add badge for primary customer
            if (customer.is_primary) {
                displayName += ' (Primary)';
            }
            
            // Add prescription summary if available
            if (customer.prescription_summary) {
                displayName += ' - ' + customer.prescription_summary;
            }
            
            window.posSelectedCustomers.push({
                id: customer.id,
                name: displayName,
                original_name: customer.name,
                mobile: customer.mobile,
                contact_id: customer.contact_id,
                is_primary: customer.is_primary || false,
                is_current: customer.is_current || false
            });
        });
        
        console.log('✅ Added customers to selection:', window.posSelectedCustomers);
        populateAllCustomerDropdowns();
    }
    
    /**
     * Update the list of selected customers (single customer fallback)
     */
    function updateSelectedCustomers() {
        var customerId = $('#customer_id').val();
        var customerText = $('#customer_id option:selected').text();
        
        if (customerId && customerText) {
            // Clear and add just this customer
            window.posSelectedCustomers = [{
                id: customerId,
                name: customerText,
                original_name: customerText,
                is_primary: true,
                is_current: true
            }];
            
            console.log('✅ Single customer added:', customerText);
            populateAllCustomerDropdowns();
        }
    }
    
    /**
     * Populate customer dropdown for a specific product row
     */
    function populateCustomerDropdownForRow($row) {
        var $dropdown = $row.find('.product_customer_assignment');
        
        if ($dropdown.length === 0) {
            return;
        }
        
        var currentValue = $dropdown.val();
        
        // Clear existing options except the first one
        $dropdown.find('option:not(:first)').remove();
        
        // Add customers to dropdown
        window.posSelectedCustomers.forEach(function(customer) {
            var $option = $('<option></option>')
                .val(customer.id)
                .text(customer.name);
            
            if (customer.id == currentValue) {
                $option.prop('selected', true);
            }
            
            $dropdown.append($option);
        });
        
        // Auto-select if only one customer
        if (window.posSelectedCustomers.length === 1 && !currentValue) {
            $dropdown.val(window.posSelectedCustomers[0].id);
        }
        
        // Auto-select primary customer if multiple customers but no assignment
        if (window.posSelectedCustomers.length > 1 && !currentValue) {
            var primaryCustomer = window.posSelectedCustomers.find(function(c) {
                return c.is_primary || c.is_current;
            });
            if (primaryCustomer) {
                $dropdown.val(primaryCustomer.id);
            }
        }
    }
    
    /**
     * Populate customer dropdowns for all product rows
     */
    function populateAllCustomerDropdowns() {
        $('#pos_table tbody tr.product_row').each(function() {
            populateCustomerDropdownForRow($(this));
        });
        
        console.log('📋 Updated customer dropdowns for all products');
    }
    
    /**
     * Observe changes to the product table using MutationObserver
     */
    function observeProductTableChanges() {
        var targetNode = document.querySelector('#pos_table tbody');
        
        if (!targetNode) {
            return;
        }
        
        var config = { childList: true, subtree: true };
        
        var callback = function(mutationsList, observer) {
            for (var mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && $(node).hasClass('product_row')) {
                            populateCustomerDropdownForRow($(node));
                        }
                    });
                }
            }
        };
        
        var observer = new MutationObserver(callback);
        observer.observe(targetNode, config);
        
        console.log('👀 Observing product table for changes');
    }
    
    /**
     * Get product-customer assignments for form submission
     */
    window.getProductCustomerAssignments = function() {
        var assignments = [];
        
        $('#pos_table tbody tr.product_row').each(function() {
            var $row = $(this);
            var productId = $row.find('.product_id').val();
            var variationId = $row.find('.row_variation_id').val();
            var customerId = $row.find('.product_customer_assignment').val();
            var quantity = $row.find('.pos_quantity').val();
            
            if (customerId) {
                assignments.push({
                    product_id: productId,
                    variation_id: variationId,
                    customer_id: customerId,
                    quantity: quantity,
                    row_index: $row.data('row_index')
                });
            }
        });
        
        return assignments;
    };
    
    /**
     * Add customer to the selection (for multi-customer feature)
     */
    window.addCustomerToSelection = function(customerId, customerName) {
        var exists = window.posSelectedCustomers.some(function(c) {
            return c.id == customerId;
        });
        
        if (!exists) {
            window.posSelectedCustomers.push({
                id: customerId,
                name: customerName,
                original_name: customerName
            });
            
            populateAllCustomerDropdowns();
            console.log('✅ Customer added to selection:', customerName);
        }
    };
    
    /**
     * Remove customer from selection
     */
    window.removeCustomerFromSelection = function(customerId) {
        window.posSelectedCustomers = window.posSelectedCustomers.filter(function(c) {
            return c.id != customerId;
        });
        
        // Clear assignments for this customer
        $('#pos_table tbody tr.product_row').each(function() {
            var $dropdown = $(this).find('.product_customer_assignment');
            if ($dropdown.val() == customerId) {
                $dropdown.val('');
            }
        });
        
        populateAllCustomerDropdowns();
        console.log('❌ Customer removed from selection');
    };
    
    /**
     * Clear all customer selections
     */
    window.clearCustomerSelections = function() {
        window.posSelectedCustomers = [];
        relatedCustomersCache = {};
        populateAllCustomerDropdowns();
        console.log('🗑️ All customer selections cleared');
    };
    
    /**
     * Refresh related customers for current selection
     */
    window.refreshRelatedCustomers = function() {
        var customerId = $('#customer_id').val();
        if (customerId) {
            // Clear cache and refetch
            delete relatedCustomersCache[customerId];
            fetchAndAddRelatedCustomers(customerId);
        }
    };
    
    /**
     * Validate that all products have customer assignments (if required)
     */
    window.validateProductCustomerAssignments = function() {
        if (window.posSelectedCustomers.length <= 1) {
            // No validation needed for single customer
            return true;
        }
        
        var unassignedProducts = [];
        
        $('#pos_table tbody tr.product_row').each(function() {
            var $row = $(this);
            var customerId = $row.find('.product_customer_assignment').val();
            var productName = $row.find('td:first').text().trim();
            
            if (!customerId) {
                unassignedProducts.push(productName);
            }
        });
        
        if (unassignedProducts.length > 0) {
            var message = 'Please assign the following products to customers:\n\n';
            message += unassignedProducts.join('\n');
            
            if (typeof toastr !== 'undefined') {
                toastr.warning(message);
            } else {
                alert(message);
            }
            
            return false;
        }
        
        return true;
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        initProductCustomerAssignment();
        
        // Add validation before form submission
        $('#add_pos_sell_form').on('submit', function(e) {
            // Only validate if multiple customers are selected
            if (window.posSelectedCustomers.length > 1) {
                if (!window.validateProductCustomerAssignments()) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
    
    console.log('✅ Product-Customer Assignment module loaded with Related Customers support');
})();

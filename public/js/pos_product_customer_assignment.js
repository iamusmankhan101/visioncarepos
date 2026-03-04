/**
 * POS Product-Customer Assignment Module
 * 
 * This module handles assigning specific products to specific customers
 * when multiple customers are selected in a POS transaction.
 */

(function() {
    'use strict';
    
    // Store selected customers globally
    window.posSelectedCustomers = [];
    
    /**
     * Initialize product-customer assignment functionality
     */
    function initProductCustomerAssignment() {
        console.log('🎯 Initializing Product-Customer Assignment');
        
        // Listen for customer selection changes
        $(document).on('change', '#customer_id', function() {
            updateSelectedCustomers();
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
     * Update the list of selected customers
     */
    function updateSelectedCustomers() {
        var customerId = $('#customer_id').val();
        var customerText = $('#customer_id option:selected').text();
        
        if (customerId && customerText) {
            // Check if customer is already in the list
            var exists = window.posSelectedCustomers.some(function(c) {
                return c.id == customerId;
            });
            
            if (!exists) {
                window.posSelectedCustomers.push({
                    id: customerId,
                    name: customerText
                });
                
                console.log('✅ Customer added:', customerText);
                populateAllCustomerDropdowns();
            }
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
                name: customerName
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
        populateAllCustomerDropdowns();
        console.log('🗑️ All customer selections cleared');
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
    
    console.log('✅ Product-Customer Assignment module loaded');
})();

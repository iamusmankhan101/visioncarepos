#!/bin/bash

echo "Deploying Shipments Debug Script"
echo "================================"

# Upload the debug script
echo "Uploading debug script..."
scp debug_shipments_table_response.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/

# Upload the updated shipments view (with minor column config change)
echo "Uploading updated shipments view..."
scp resources/views/sell/shipments.blade.php u102957485@digitrot.com:/home/u102957485/domains/digitrot.com/public_html/pos/resources/views/sell/

echo "Deployment completed!"
echo ""
echo "Testing Steps:"
echo "=============="
echo "1. Run the debug script to check shipments table response:"
echo "   php debug_shipments_table_response.php"
echo ""
echo "2. Check what the shipments endpoint returns:"
echo "   - If shipping_status contains HTML: Client-side issue"
echo "   - If shipping_status contains raw values: Server-side issue"
echo ""
echo "3. Compare with main sales table:"
echo "   - Order Status column (working) vs Shipping Status column (not working)"
echo ""
echo "Expected Results:"
echo "================"
echo "The debug should show if the /sells endpoint with only_shipments=true"
echo "is returning the same formatted HTML as the regular sales table."
echo ""
echo "If the response shows raw values like 'packed' instead of HTML buttons,"
echo "then the editColumn logic is not being applied to shipments requests."
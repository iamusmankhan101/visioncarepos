#!/bin/bash

# Deploy Commission Agents Fix
echo "🔧 Deploying Commission Agents Fix..."

# 1. Check current status
echo "1. Checking current commission agents status..."
echo "   Visit: http://your-domain/check_agents_now.php"

# 2. Fix the data
echo "2. Fixing commission agents data..."
echo "   Visit: http://your-domain/fix_agents_quick.php"

# 3. Test the dashboard
echo "3. Testing dashboard..."
echo "   Visit: http://your-domain/"

echo ""
echo "✅ Commission Agents Fix Deployment Complete!"
echo ""
echo "📋 Manual Steps:"
echo "1. Open: http://your-domain/check_agents_now.php"
echo "2. If no agents found, click 'Create Sample Agents'"
echo "3. Open: http://your-domain/fix_agents_quick.php"
echo "4. Refresh your dashboard to see the Sales Commission section with proper data"
echo ""
echo "🎯 Expected Result:"
echo "- Sales Commission section should show agent names instead of 'N/A'"
echo "- Commission percentages should display correctly"
echo "- Performance indicators should show based on sales data"
echo ""
#!/bin/bash

# Update HackathonAdmin News Index
echo "Updating HackathonAdmin News Index..."
sed -i "/import Default from/a import TwitterShareButton from '@/Components/TwitterShareButton.vue'" resources/js/Pages/HackathonAdmin/News/Index.vue

# Update TrackSupervisor News Index
echo "Updating TrackSupervisor News Index..."
sed -i "/import Default from/a import TwitterShareButton from '@/Components/TwitterShareButton.vue'" resources/js/Pages/TrackSupervisor/News/Index.vue

# Update HackathonAdmin1 News Index
echo "Updating HackathonAdmin1 News Index..."
sed -i "/import Default from/a import TwitterShareButton from '@/Components/TwitterShareButton.vue'" resources/js/Pages/HackathonAdmin1/News/Index.vue

echo "All News Index pages updated with Twitter share button import!"
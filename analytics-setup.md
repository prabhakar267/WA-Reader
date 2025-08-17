# Google Analytics Setup for WA Reader

## Overview
Google Analytics has been successfully integrated into the WA Reader project using Google Analytics 4 (GA4) tracking code.

## Setup Instructions

### 1. Get Your Google Analytics Tracking ID
1. Go to [Google Analytics](https://analytics.google.com/)
2. Create a new property for your website
3. Copy your Measurement ID (format: G-XXXXXXXXXX)

### 2. Update the Tracking ID
Replace `GA_MEASUREMENT_ID` in the following files with your actual Measurement ID:

**File: `index.html`**
- Line 16: `<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>`
- Line 21: `gtag('config', 'GA_MEASUREMENT_ID');`

### 3. Events Being Tracked

The following custom events are automatically tracked:

#### User Engagement Events
- **file_upload_attempt**: When user attempts to upload a chat file
- **chat_processed_successfully**: When chat is successfully processed and displayed
  - Includes the number of chat messages as a value
- **chat_download**: When user downloads the processed chat as JSON

#### Error Events
- **chat_processing_error**: When there's an error processing the uploaded file
  - Includes the error message as a label

### 4. Event Categories
- `engagement`: User interaction events
- `error`: Error tracking events

### 5. Testing
After updating the Measurement ID:
1. Open the website in a browser
2. Open browser developer tools
3. Go to Network tab
4. Perform actions (upload file, process chat, download)
5. Look for requests to `google-analytics.com` or `googletagmanager.com`

### 6. Privacy Considerations
- All chat processing happens client-side
- No chat content is sent to Google Analytics
- Only interaction events and error messages are tracked
- Consider adding a privacy notice about analytics tracking

## Files Modified
- `index.html`: Added GA4 tracking code in the head section
- `static/js/script.js`: Added event tracking for user interactions
- `analytics-setup.md`: This documentation file

## Reference Implementation
This implementation is based on the Google Analytics setup from:
https://github.com/prabhakar267/prabhakar267.github.io/blob/master/scripts/script.js

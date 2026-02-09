# Features

### Overview
This application is a **Social Proof & Marketing Widget Platform**. It allows users to embed a single script on their website to display various conversion-boosting widgets. It includes a dashboard for managing campaigns and viewing real-time analytics.

### 1. Core Widget Features (Client-Side)
*   **Single Script Installation**: A single JavaScript file (`/api/widget.js`) powers all features on the client site.
*   **Real-time Data Fetching**: The widget fetches configuration and data from the backend via an API.
*   **Magical Detection**: Automatically detects form submissions on the host website to track conversions without custom code.
*   **Heartbeat System**: Tracks active visitors in real-time by sending periodic heartbeats.
*   **Triggers & Rules**:
    *   **Delay**: Show widgets after a set time (seconds).
    *   **Exit Intent**: Trigger when the mouse leaves the window (desktop only).
    *   **URL Matching**: Show widgets only on specific pages (substring match).
    *   **Frequency Control**: "Every load" or "Once per session" (30-minute timeout).

### 2. Campaign Types
The platform supports multiple types of campaigns/widgets that can run simultaneously:

#### A. Live Visitors
*   **Function**: Displays a notification showing how many people are currently viewing the page (e.g., "55 people are viewing this page").
*   **Logic**: Counts unique visitor IDs active in the last 30 minutes.
*   **Customization**: Position, background color, text color.

#### B. Live Conversions (Notifications)
*   **Function**: Shows recent activity notifications (e.g., "John from New York just signed up").
*   **Data Sources**:
    1.  **Real Events**: Actual form submissions tracked via "Magical Detection" or API.
    2.  **Simulated Data**: Fake notifications created in the dashboard to boost perceived activity (e.g., "Someone from London purchased...").
*   **Historical Count**: Can display a summary like "150 people signed up in the last 7 days".

#### C. Coupons
*   **Function**: A popup modal offering a discount code.
*   **Features**:
    *   **Click-to-Copy**: Users can click a button to copy the code to their clipboard.
    *   **Design**: Customizable title, description, button text, colors.
    *   **Images**: Support for uploading banner images (top, left, right, or background styles).

#### D. Announcements
*   **Function**: A modal for important news or updates.
*   **Actions**: Can include a "Call to Action" button that links to a URL or closes the modal.
*   **Design**: Fully customizable text and colors.

#### E. Videos
*   **Function**: Embeds a video player in a popup.
*   **Supported Sources**: YouTube, Vimeo, and direct MP4/video file links.
*   **Layout**: Video on top with a title, message, and optional CTA button below.

#### F. Newsletters
*   **Function**: A lead generation form to collect visitor details.
*   **Fields**: Email (required), Name (optional toggle), Phone (optional toggle).
*   **Actions**:
    *   **On Success**: Show a message, redirect to a URL, or close the modal.
    *   **Webhooks**: Can send lead data to an external URL (e.g., Zapier) upon submission.
    *   **Export**: Admin can view and export leads (CSV likely) from the dashboard.

#### G. Socials
*   **Function**: A floating widget (bottom-left or bottom-right) listing social media profiles.
*   **Platforms**: Supports icons/links for Facebook, Twitter, Instagram, LinkedIn, YouTube, WhatsApp, TikTok, etc.
*   **Behavior**: Can be minimized/closed by the visitor for the session.

### 3. Admin Dashboard
*   **Authentication**: Secure Login/Logout system.
*   **Main Dashboard**:
    *   **Live Traffic**: Real-time count of active visitors.
    *   **Traffic Graph**: A chart showing visitor traffic over the last 24 hours (hourly buckets).
*   **Campaign Management**: CRUD (Create, Read, Update, Delete) interfaces for all campaign types listed above.
*   **Settings**:
    *   **Timezone**: Adjust reporting timezone.
    *   **Magical Detection**: Toggle the automatic form tracking feature.

### 4. Analytics & Reporting
*   **Traffic Snapshots**: The system logs visitor counts every 5 minutes for historical graphing.
*   **Campaign Analytics**: Tracks `view` and `click` (or `submit`) events for every campaign type.
*   **Conversion Tracking**: Logs `form_submit` events with payload data.

### 5. Technical Architecture
*   **Backend**: Pure PHP (no framework, uses `AltoRouter` for routing).
*   **Database**: MySQL.
*   **Frontend (Admin)**: PHP-generated HTML views.
*   **Frontend (Widget)**: Vanilla JavaScript injected via a single script tag.
*   **API**: REST-like endpoints for the widget to communicate with the server (CORS enabled).

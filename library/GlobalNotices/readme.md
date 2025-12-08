# Global Notices Feature  

## Feature Name  

### Description  
The **Global Notices** feature allows administrators to create and manage **site-wide notifications** that appear in different locations on the website. These notices can be displayed as:  

- **Banners** (full-width messages at the top of the page)  
- **Toasts** (small pop-up notifications)  
- **Content Notices** (embedded within page content)  

Each notice can include an **icon**, an **action button/link**, and be **dismissable** for a set period. Notices are managed from the WordPress admin panel and can be configured based on user roles, login status, and page type.  

---

## How to Utilize  

### For Administrators  
**Accessing the Feature:**  
- Navigate to **Global Notices** in the WordPress admin menu.  
- Requires the **edit_posts** capability to manage notices.  

**Creating a Notice:**  
1. Click **"Add New Notice"** and configure:  
   - **Message:** The main content of the notice.  
   - **Type:** Info, warning, error, or success.  
   - **Icon:** Optional visual indicator.  
   - **Action:** A button or link (optional).  
   - **Dismissable:** Set whether users can close the notice and how long it remains hidden.  
   - **Location:** Choose from **Toast, Banner, or Content**.  
   - **Constraints:** Restrict visibility based on:  
     - **User Role & Login Status** (logged-in users vs. guests).  
     - **Page Type** (frontpage vs. subpages).  
2. Click **Save & Publish** to make the notice live.  

### For Editors & Users  
- **Editors** (if they have the required permissions) can also create and manage notices.  
- **Users** will see notices based on the defined settings.  
- Dismissable notices remain hidden for the specified duration once closed.  
- Notices with **actions** (e.g., links/buttons) provide easy access to relevant information.  

---

## Purpose  

### Why This Feature Exists  
The **Global Notices** feature is designed to:  
✅ Provide a **centralized** way to display important messages across the website.  
✅ Allow **targeted messaging** based on user role, login status, and page type.  
✅ Improve **communication** between administrators and website visitors.  

### Who Benefits?  
- **Site Administrators**: Easily manage and schedule notices without modifying templates.  
- **Editors**: Display important updates without relying on developers.  
- **End Users**: Get relevant information at the right place and time, improving user experience.  

This feature eliminates the need for manually inserting notices on multiple pages and ensures consistency in messaging.  

---

## Meta  

- **Author:** Helsingborg Stad 
- **Initial Release Date:** February 2025  
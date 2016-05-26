Changelog
---------

The changelog aims to describe higher level changes for each version of the intranet. Multiple minor changes and/or adjusments not worth mentioning may also have been made.

Version 0.1.2 (not released)
============================
- Adds display options to the "intranet news" module
- Link the "page manager" user to the user profile (if logged in)
- Redirect user to referer on logout
- Target groups manager for network admins
- Metabox for settings tagrget group restrictions per post
- Restrict content to target groups if set (not working across all plugins since there's no good hook to make it general)

Version 0.1.1 (2016-05-25)
==========================
- Fixes broken "session timeout" login modal
- Fixes issue where multiple "network sites" pages were created on the main site
- Adds a news feature
- Adds "about me" field to edit profile
- Adds "manage subscriptions" function
- Handle private pages/posts with WP Core visibility setting
- Site default settings improved
- Include the "Customer Feedback" plugin (https://github.com/helsingborg-stad/Customer-feedback) via composer
- Rename the "author" metabox in admin (for pages) to "Page manager" and make it visible by default
- Ask for first name and last name when registering a new user via network admin

Version 0.1.0 (2016-05-20)
==========================
First beta-release

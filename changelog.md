Changelog -test 4
---------

The changelog aims to describe higher level changes for each version of the intranet. Multiple minor changes and/or adjusments not worth mentioning may also have been made.

Version 0.2.1 (tbd)
===================
- Fixes issue with special charachters in the "table of contents" list
- Require Modularity Dictionary extension (hard to understand words list)-
- Option to set a private page as blog page
- Google Plus support added to social media feed module
- Set default color scheme to purple
- Logged in user can choose personal color theme in settings

Version 0.2.0 (2016-06-09)
==========================

**BREAKING UPDATE:** This update will need a new database table structure for the "user system" functionality. To fix the issue remove the currently used table from the database and a new one will be created automatically. Also make sure to run the following sql query to remove old data. ```DELETE FROM intranet_usermeta WHERE meta_key = 'user_systems'```

- Adds backend (admin) settings for "my systems" link list
- Adds frontend user administration for the "my systems" link list
- Adds filter for internal user systems (only show internal systems when visiting from specified IP addresses)
- Adds table of contents page (/table-of-contents)
- Adds administration interface for administration units
- Adds file archive module
- Filter internally only systems from beeing displayed outside specified IP patterns
- Adds content scheduling plugin

Version 0.1.4 (2016-06-02)
==========================
- Fixes issue where "intranet news" module showed the "front page" as a news item
- Added Swedish translation
- Label that shows which network a news item is fetched from
- User search
- Adds Swedish keyword stemmer for Search WP
- Fixes inconsistency with the top search field among different browsers
- Made the top bar header responsive
- Fixes issue where network search did not work for logged out users
- Better listing of sites in network (no post object needed to create the page)
- General improvements to UI responsiveness

Version 0.1.3 (2016-05-31)
==========================
- Adds user links module for users to add their own links to
- Target user groups with modules
- Target content to specific user groups
- Add editor button for targeting post content to specific user groups
- Alarms/malfunction plugin
- Adds Search WP with multisite search modifications
- Adds option to search all, subscribed or current site (subscribed by default)

Version 0.1.2 (2016-05-27)
==========================
- Adds display options to the "intranet news" module
- Link the "page manager" user to the user profile (if logged in)
- Redirect user to referer on logout
- Target groups manager for network admins
- Metabox for settings tagrget group restrictions per post
- Restrict content to target groups if set (not working across all plugins since there's no good hook to make it general)
- Option to pin news to top ("Intranet news" module and news)
- Display date published for news
- Users who cant edit_posts will no longer be able to visit wp_admin and will no longer see the adminbar

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

# User Group Management and Content Restriction

### Description

This feature enhances user management and content control by introducing user groups and the ability to restrict access to content based on these groups.  It allows administrators to categorize users into groups (e.g., "Subscribers," "Members," "Employees") and then control which groups can view specific posts or attachments. This provides granular control over content visibility, enabling personalized experiences and secure information sharing.  The functionality integrates with Single Sign-On (SSO) for streamlined user login and redirection.

## How to Utilize

### For Administrators / Editors

1. **Access User Groups:** Navigate to "Users" -> "User Groups" in the WordPress admin menu.  This will take you to the taxonomy management screen where you can create, edit, and delete user groups.  *Requires the `manage_options` capability.*

2. **Assign Users to Groups:**  On a user's profile page (Users -> All Users -> Edit User), a new "User Group" field will be present.  Use this field to assign the user to a user group.

3. **Restrict Content Visibility:** When editing a post or attachment, a "User group visibility" meta box will appear in the "Publish" section of the editor (it may be hidden initially, check "Screen Options" at the top right).  Select the user groups that should have access to this content. If no groups are selected, the content will be visible to all logged-in users. *Requires the `edit_posts` capability (or `edit_attachments` for attachments).*

4. **SSO Redirection:** After a user logs in via SSO, they will be automatically redirected to a URL specific to their assigned user group, if configured. T

5. **Automatic mapping of User Groups** Usergroups will automaticly be mapped to the user, based on the "companyname" field in most sso-services.

### For Users

- **Frontend Experience:** Users will only see posts and attachments that are visible to their assigned user group(s). If a user is not logged in, or is logged in but does not belong to a group with access, they will not see the restricted content.
- **SSO Login:** Users can log in via SSO. If a redirect URL is set for their group, they will be redirected accordingly.

---

## Purpose

### Why This Feature Exists

This feature addresses the need for controlled content access based on user roles or affiliations.  It solves the problem of:

- **Creating internal knowledge bases:**  Sharing information only with specific departments or teams.
- **Personalizing user experiences:** Showing content relevant to a user's interests.
- **Simplifying SSO redirection:** Streamlining the post-login experience for users based on their group.

### Key Benefits

✅ **Centralizes functionality:** Manages user groups and content restrictions within WordPress.
✅ **Improves the user experience:**  Provides personalized content access and a streamlined SSO login flow.
✅ **Reduces manual work:** Automates content visibility based on user group assignments, eliminating the need for manual checks or workarounds.
✅ **Enhances security:** Protects sensitive information by restricting access to authorized user groups.

---

## Meta

- **Author:** Helsingborg Stad
- **Initial Release Date:** October, 2023
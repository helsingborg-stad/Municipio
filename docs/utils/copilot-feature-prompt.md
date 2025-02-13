#Copilot Feature Prompt
To generate documentation for a feature in the codebase, use the following prompt as a template. Replace the placeholder([folder]) with the relevant information from the codebase.
Then copy everything below and paste it into the documentation file for the feature.

@workspace #codebase
1. Generate markdown documentation for the feature located in the #file:library/[folder] directory.
2. Use the following markdown as a template for the documentation:
```markdown
# Feature Name

### Description  
Provide a **brief overview** of the feature and its functionality. Explain what it does and where it is used in the system.  

## How to Utilize  

### For Administrators / Editors 
- Explain how **administrators** can use the feature, including step-by-step instructions.  
- Mention any **capabilities/permissions** required (e.g., `edit_posts`, `manage_options`).  
- Detail where to find the settings (e.g., in the WordPress admin panel).  

### For Users  
- Describe how users will see or experience the feature on the frontend.  

---

## Purpose  

### Why This Feature Exists  
- Describe **what problem this feature solves** and why it was implemented.  
- Explain how it benefits different user groups (administrators, editors, and end users).  

### Key Benefits  
✅ Centralizes functionality or information.  
✅ Improves the user experience by [explain benefit].  
✅ Reduces manual work for [specific user roles].  

---

## Meta  

- **Author:** [Name or Team]  
- **Initial Release Date:** [Month, Year]
```
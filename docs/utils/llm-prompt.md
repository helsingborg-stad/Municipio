# Promt for Automated LLM Documentation
This is a "how-to" in order to generate automatic documentation in a LLM. Some features may be to large to input in a all LLM's. Small features will be possible to generate in OpenAI, and larger features has been successfully entered to Gemeni.

---

## How to Retrieve the Feature Code
To fetch the feature's code, run the following command in the feature directory:  
```bash
find . -type f -not -path '*/\.*' | while read -r file; do echo -e "\n=== File: $file ===\n"; cat "$file"; done | pbcopy
```  

---

# [Feature Name] 

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
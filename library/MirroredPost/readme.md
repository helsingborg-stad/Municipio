# Mirrored Post Feature

### Description  
The **Mirrored Post** feature enables the retrieval and display of posts from other blogs within a WordPress multisite network as if they were native to the current site. It provides the ability to "mirror" a single post from another blog into the current site's query, making cross-site content integration seamless. This is particularly useful for multisite installations where content sharing between sites is required.

## How to Utilize  

### Usage
To retrieve a mirrored post, the following example can be used for inspiration:

Let's say you have a multisite with two blogs: Blog A and Blog B. You want to display a post from Blog A on Blog B. For the sake of this example lets assume a post from blog A has the ID of 123 and that the blog(A) has the id of 456.

The following url can be used to retrieve the mirrored post:
```
https://blog-b.example.com/?p=123&fetch_from_blog=456
```

### For Users  
- Users will see mirrored posts from other blogs displayed on the current site as if they were local content. The integration is seamless, so users may not be aware that the content originates from another site within the multisite network.

---

## Purpose  

### Why This Feature Exists  
- The Mirrored Post feature addresses the need for sharing and displaying content across multiple sites in a WordPress multisite network without duplicating posts. It was implemented to streamline content management and reduce redundancy by allowing a single source of truth for shared posts.
- **Benefits:**
  - **Administrators:** Can manage content centrally and share it across sites, reducing duplication and maintenance overhead.
  - **Editors:** Gain flexibility to curate content from other blogs, enhancing editorial workflows.
  - **End Users:** Experience a unified content presentation, improving navigation and access to relevant information.

### Key Benefits  
✅ Centralizes functionality or information by allowing posts to be shared across sites.  
✅ Improves the user experience by providing seamless access to content from multiple sources.  
✅ Reduces manual work for administrators and editors by eliminating the need to duplicate or manually synchronize posts.
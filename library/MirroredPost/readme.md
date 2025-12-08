# Mirrored Post Feature

### Description  
The **Mirrored Post** feature enables the retrieval and display of posts from other blogs within a WordPress multisite network as if they were native to the current site. It allows "mirroring" a single post from another blog into the current site's query, making cross-site content integration seamless. This is particularly useful for multisite installations where content sharing between sites is required.

## How to Utilize  

### Usage
To retrieve a mirrored post, use a URL with both the post ID (`p`) and the blog ID query variable. For example, if you want to display a post from Blog A (ID: 456, Post ID: 123) on Blog B, use:

```
https://blog-b.example.com/?p=123&fetch_from_blog=456
```

- `p=123` is the post ID from Blog A.
- `fetch_from_blog=456` is the blog ID of Blog A.

The feature works by adding a custom query variable for the blog ID (see `BlogIdQueryVar`), switching to the correct blog in the query (`EnableSingleMirroredPostInWpQuery`), and decorating the post object so that links and data reference the correct source (`MirroredPostObject`).

### For Users  
- Users will see mirrored posts from other blogs displayed on the current site as if they were local content. The integration is seamless, so users may not be aware that the content originates from another site within the multisite network.

---

## Purpose  

### Why This Feature Exists  
- The Mirrored Post feature addresses the need for sharing and displaying content across multiple sites in a WordPress multisite network without duplicating posts. It streamlines content management and reduces redundancy by allowing a single source of truth for shared posts.
- **Benefits:**
  - **Administrators:** Manage content centrally and share it across sites, reducing duplication and maintenance overhead.
  - **Editors:** Curate content from other blogs, enhancing editorial workflows.
  - **End Users:** Experience unified content presentation, improving navigation and access to relevant information.

### Key Benefits  
✅ Centralizes functionality or information by allowing posts to be shared across sites.  
✅ Improves the user experience by providing seamless access to content from multiple sources.  
✅ Reduces manual work for administrators and editors by eliminating the need to duplicate or manually synchronize posts.
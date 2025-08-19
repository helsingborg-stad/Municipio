<style>
    .municipio-trash-page__posts {
        display: grid;
        grid-template-columns: repeat(auto-fill, 8.25rem);
        gap: 1.25rem;
    }

    .municipio-trash-page__post {
        width: 8.25rem;
        height: 8.25rem;
        overflow:hidden;
        margin-bottom:.25rem;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#f5f5f5;
        border:1px solid #ddd;
        position:relative;
    }

    .municipio-trash-page__post-actions {
        position: absolute;
        top: .25rem;
        right: 0;
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        width: 100%;
    }

    .municipio-trash-page__post-action-icon {
        background-color: white;
        border-radius: 50%;
        padding: 0.25rem;
        margin-right: 0.25rem;
        font-size: 20px;
        cursor: pointer;
    }

    .municipio-trash-page__post-image img {
        width: 8.25rem;
        height: 8.25rem;
        object-fit: cover;
    }

    .municipio-trash-page__post-image .c-image__image-wrapper {
        line-height: 0;
    }
</style>
<style>
    /* Page break */
    .pdf-page-break {
        page-break-before: always;
    }

    /* Pdf toc */
    .pdf-toc__list {
        width: 100%;
        list-style-type: none;
        margin-left: 0;
        padding-left: 0;
    }

    .pdf-toc__list .pdf-toc__list-item {
        width: 100%;
    }

    .pdf-toc__item {
        width: 100%;
        display: inline-flex;
        text-decoration: none;
        padding-bottom: 5px;
        color: black;
    }

    .pdf-toc__item::after {
        content: '';
        clear: both;
        display: table;
        width: 100%;
        margin-top: -7px;
        border-bottom: 2px dotted black;
    }
    .pdf-toc__item.pdf-toc__item--big::after {
        border-bottom: 2px dotted white;
    }
    
    .pdf-toc__title, .pdf-toc__number {
        background-color: white;
    }

    .pdf-toc__title {
        padding-right: 3px;
        float: left;
        margin-right: 2cm;
    }

    .pdf-toc__number {
        padding-left: 4px;
        float: right;
        position: absolute;
        width: 1.5cm;
    }

    /* Pdf Page/container */
    @page {
        margin: 2cm 2cm;
    }

    body {
        font-size: 16px;
    }  

    .pdf-container {
        padding: 2cm 2cm;
    }

    /* Pdf page covers */
    .pdf-frontpage,
    .pdf-backpage {
        width: 210mm;
        height: 297mm;
        background-size: cover;
        position: absolute;
        top: -2cm;
        left: -2cm;
    }

    /* Pdf posts */
    .pdf-post__featured-image {
        margin-bottom: 0.5cm;
    }


    /* Pdf Frontpage */
    .pdf-frontpage {
        background-color: {{ !empty($styles['color_palette_secondary']['base']) ? $styles['color_palette_secondary']['base'] : '#fff' }};
        color: {{!empty($styles['color_palette_secondary']['contrasting']) ? $styles['color_palette_secondary']['contrasting'] : '#000' }};
    }

    .pdf-frontpage .pdf-container {
        padding: 1cm 2cm;
    }

    .pdf-frontpage h1.pdf-frontpage__heading {
        font-size: 56px;
        line-height: 0.9;
        font-weight: 700;
        max-width: 55%;
    }

    .pdf-frontpage .pdf-frontpage__introduction {
        max-width: 55%;
        float: right;
    }

    .pdf-frontpage .pdf-frontpage__emblem {
        position: absolute; 
        width: 100px; 
        height: 100px; 
        top: 50%; 
        transform: translateY(-50%); 
        right: 2cm;
    }

    .pdf-frontpage .pdf-frontpage__introduction,
    .pdf-frontpage .pdf-frontpage__introduction * {
        text-align: right;
        
    }

    /* Images */
    .c-image {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100%;
        height: auto;
    }

    /* Footer */
    footer {
        position: fixed;
        bottom: -1.5cm;
        left: -3.5cm;
        right: -3.5cm;
        height: 1cm;
        text-align: center;
        line-height: 35px;
    }
    footer span:after {
        content: counter(page);
    }
    
    img {
        max-width: 100%;
    }

    /* Remove not printable elements */
    .pdf-post a {
        color: inherit;
        text-decoration: none;
    }

    .c-accordion .c-accordion__content[aria-hidden="true"],
    .c-accordion .c-accordion__content[aria-hidden="false"] {
        display: block;
    }

    .c-icon,
    .c-notice,
    .c-slider,
    .c-button,
    .nav-helper,
    .c-tooltip__container,
    .c-collection .c-collection__icon,
    .c-field.c-field--search,
    .c-card__footer,
    .c-card__image-background,
    .c-card__image,
    .c-modal,
    .mod-recommend,
    .c-acceptance__content,
    #sidebar-slider-area--container,
    #customer-feedback,
    #_hjRemoteVarsFrame,
    .vngage-banner,
    .c-signature,
    .modularity-edit-module,
    .modularity-edit-module:before,
    .modularity-edit-module:after,
    .modularity-edit-module a,
    .modularity-edit-module a:before,
    .modularity-edit-module a:after,
    .modularity-mod-player__wrapper,
    .menu-sidfotsmeny-container,
    .u-print-display--none {
        display: none !important;
    }
</style>
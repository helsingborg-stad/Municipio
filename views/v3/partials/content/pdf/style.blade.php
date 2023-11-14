<style>
    @font-face {
        font-family: 'helsingborg-sans-medium';
        font-display:swap;
        font-style: normal;
        src: url('https://media.helsingborg.se/uploads/networks/1/2023/04/helsingborg-sans-medium.woff') format("woff");
        font-weight: 600;
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    }
    
    /* Headings */
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin-top: 0;
        font-weight: 600;
        font-family: 'helsingborg-sans-medium', sans-serif;
    }

    h1,
    .pdf-heading--h1 {
        font-family: 'helsingborg-sans-medium', sans-serif;
        font-size: {{ !empty($styles['typography_h1']['font-size']) ? $styles['typography_h1']['font-size'] : '48px' }};
        line-height: {{ !empty($styles['typography_h1']['line-height']) ? $styles['typography_h1']['line-height'] : '1.25' }};
        font-weight: {{ !empty($styles['typography_h1']['variant']) ? $styles['typography_h1']['variant'] : 'bold'}}
    }

    h2 {
        font-size: {{ !empty($styles['typography_h2']['font-size']) ? $styles['typography_h2']['font-size'] : '32px' }};
        line-height: {{ !empty($styles['typography_h2']['line-height']) ? $styles['typography_h2']['line-height'] : '1.25' }};
        font-weight: {{ !empty($styles['typography_h2']['variant']) ? $styles['typography_h2']['variant'] : 'bold'}}
    }

    h3 {
        font-size: {{ !empty($styles['typography_h3']['font-size']) ? $styles['typography_h3']['font-size'] : '24px' }};
        line-height: {{ !empty($styles['typography_h3']['line-height']) ? $styles['typography_h3']['line-height'] : '1.25' }};
        font-weight: {{ !empty($styles['typography_h3']['variant']) ? $styles['typography_h3']['variant'] : 'bold'}}   
    }

    h4 {
        font-size: {{ !empty($styles['typography_h4']['font-size']) ? $styles['typography_h4']['font-size'] : '20px' }};
        line-height: {{ !empty($styles['typography_h4']['line-height']) ? $styles['typography_h4']['line-height'] : '1.25' }};
        font-weight: {{ !empty($styles['typography_h4']['variant']) ? $styles['typography_h4']['variant'] : 'bold'}}
    }

    h5 {
        font-size: {{ !empty($styles['typography_h5']['font-size']) ? $styles['typography_h5']['font-size'] : '18px' }};
        line-height: {{ !empty($styles['typography_h5']['line-height']) ? $styles['typography_h5']['line-height'] : '1.25' }};
        font-weight: {{ !empty($styles['typography_h5']['variant']) ? $styles['typography_h5']['variant'] : 'bold'}}
    }

    h5 {
        font-size: {{ !empty($styles['typography_h6']['font-size']) ? $styles['typography_h6']['font-size'] : '16px' }};
        line-height: {{ !empty($styles['typography_h6']['line-height']) ? $styles['typography_h6']['line-height'] : '1.25' }};
        font-weight: {{ !empty($styles['typography_h6']['variant']) ? $styles['typography_h6']['variant'] : 'bold'}}
    }

    h2.pdf-post__heading {
        color: {{ !empty($styles['color_palette_primary']['base']) ? $styles['color_palette_primary']['base'] : '#76232F' }};
        font-weight: bold;
    }

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
        border-bottom: 2px dotted black;
        width: 100%;
        margin-top: -7px;
    }
    
    .pdf-toc__title, .pdf-toc__number {
        background-color: white;
    }

    .pdf-toc__title {
        padding-right: 3px;
        float: left;
    }

    .pdf-toc__number {
        padding-left: 4px;
        float: right;
    }

    /* Pdf Page/container */
    @page {
        margin: 2cm 2cm;
    }

    body {
        font-size: 1rem;
    }  

    .pdf-container {
        margin: 2cm 2cm;
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

    /* Pdf Frontpage */
    .pdf-frontpage .pdf-heading {
        margin-bottom: 8px;
        font-weight: 700;
    }

    .pdf-frontpage__heading {  
        position: absolute; 
        top: 50%; 
        left: 0; 
        right: 0; 
        padding: 0 2cm;  
        transform: translateY(-50%);
    }

    footer {
        position: fixed;
        bottom: -1.5cm;
        left: -3.5cm;
        right: -3.5cm;
        height: 1cm;
        /** Extra personal styles **/
        text-align: center;
        line-height: 35px;
    }
    footer span:after {
        content: counter(page);
    }
    
    img {
        max-width: 100%;
    }
</style>
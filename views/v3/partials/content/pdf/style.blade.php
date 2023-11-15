<style>

    /* Typography */
    article,
    section:not(.pdf-toc),
    p,
    .pdf-frontpage__introduction {
        font-family: 'Roboto', sans-serif;
        font-weight: 400;
    }

    .lead {
        font-weight: bold;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin-top: 0;
        font-weight: 700;
        font-family: 'Roboto', sans-serif;
    }

    h1,
    .pdf-heading--h1 {
        font-size: {{ !empty($styles['typography_h1']['font-size']) ? $styles['typography_h1']['font-size'] : '48px' }};
        line-height: 1.1;
    }

    h2 {
        font-size: {{ !empty($styles['typography_h2']['font-size']) ? $styles['typography_h2']['font-size'] : '32px' }};
        line-height: {{ !empty($styles['typography_h2']['line-height']) ? $styles['typography_h2']['line-height'] : '1.25' }};
    }

    h3 {
        font-size: {{ !empty($styles['typography_h3']['font-size']) ? $styles['typography_h3']['font-size'] : '24px' }};
        line-height: {{ !empty($styles['typography_h3']['line-height']) ? $styles['typography_h3']['line-height'] : '1.25' }};
    }

    h4 {
        font-size: {{ !empty($styles['typography_h4']['font-size']) ? $styles['typography_h4']['font-size'] : '20px' }};
        line-height: {{ !empty($styles['typography_h4']['line-height']) ? $styles['typography_h4']['line-height'] : '1.25' }};
    }

    h5 {
        font-size: {{ !empty($styles['typography_h5']['font-size']) ? $styles['typography_h5']['font-size'] : '18px' }};
        line-height: {{ !empty($styles['typography_h5']['line-height']) ? $styles['typography_h5']['line-height'] : '1.25' }};
    }

    h6 {
        font-size: {{ !empty($styles['typography_h6']['font-size']) ? $styles['typography_h6']['font-size'] : '16px' }};
        line-height: {{ !empty($styles['typography_h6']['line-height']) ? $styles['typography_h6']['line-height'] : '1.25' }};
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
        line-height: 1;
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
        text-align: right:
        
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
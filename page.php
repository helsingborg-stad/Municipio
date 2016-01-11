<?php get_header(); ?>

<div class="container">
    <div class="grid">
        <div class="grid-lg-12">
            <ul class="breadcrumbs">
                <li><a href="#">Parent node</a></li>
                <li><a href="#">First child node</a></li>
                <li>Current node</li>
            </ul>
        </div>
    </div>

    <div class="grid no-margin-top">
        <nav class="grid-md-4 grid-lg-3">
            <a href="#menu-open" id="menu-open" class="hidden-sm hidden-md hidden-lg menu-trigger"><span class="menu-icon"></span></a>
            <ul class="nav-aside hidden-xs">
                <li><a href="#">Link 1</a></li>
                <li class="has-children"><a href="#">Link 2</a></li>
                <li class="current-node has-children">
                    <a href="#">Link 3</a>
                    <ul class="sub-menu">
                        <li><a href="#">Sublink 1</a></li>
                        <li class="current"><a href="#">Sublink 2</a></li>
                        <li><a href="#">Sublink 3</a></li>
                    </ul>
                </li>
                <li><a href="#">Link 4</a></li>
            </ul>
        </nav>

        <article class="grid-md-8 grid-lg-6">
            <h1>Våra värderingar</h1>
            <p class="lead">
                Helsingborgs stad är en politiskt styrd organisation och en viktig del i det demokratiska samhälle vi lever i. Flera av oss som arbetar i Helsingborgs stad arbetar med vård, omsorg och utbildning. Andra är bibliotekarier, arkitekter, ingenjörer eller socialsekreterare. Det här är bara några av de många yrken som behövs för att få en stad som Helsingborg att fungera.
            </p>
            <p>
                Tillsammans är vi nio förvaltningar och åtta bolag som arbetar för staden. Vi är kring 10 000 medarbetare inom cirka 740 yrken. Oavsett vilket yrke vi har är vår gemensamma uppgift att leverera välfärd, service och tjänster av hög kvalitet till de som bor, verkar i och besöker Helsingborg. För att vi ska kunna uppnå detta satsar vi mycket på att se till att våra medarbetare trivs på sina arbetsplatser, har stimulerande arbetsuppgifter och anställningsvillkor och dessutom har möjlighet att växa och utvecklas.
            </p>
            <p>
                Men vi nöjer oss inte med det. Helsingborgs stad har som mål att vara den mest attraktiva staden för människor och företag. År 2035 ska Helsingborg vara den skapande, pulserande, gemensamma, globala och balanserade staden för människor och företag.
            </p>
            <p>
                Därför siktar vi högt i vår strävan att vara en attraktiv, intressant och hållbar arbetsgivare. Det innebär ett konstant förändringsarbete som skapar en spännande och dynamisk organisationsutveckling.
            </p>
            <p>
                Det finns fem områden som vi tycker är viktiga för att lyckas i vårt arbete med att attrahera, rekrytera, utveckla och behålla kompetenta medarbetare.
            </p>
        </article>

        <aside class="grid-lg-3 grid-md-12">
            <div class="box box-filled-3">
                <div class="box-content">
                    <h4>Kontakt</h4>
                    <p><strong>HR-avdelningens support</strong></p>
                    <p><strong>Besök oss</strong><br>Järnvägsgatan 14</p>
                    <p><strong>Telefonnummer</strong><br>042-10 40 00</p>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php get_footer(); ?>

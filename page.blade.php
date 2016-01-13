@extends('templates.scaffolding')

@section('content')

<div class="container">
    @include('templates.partials.breadcrumbs')

    <div class="grid no-margin-top">
        @include('templates.partials.sidebar-left')

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

        @include('templates.partials.sidebar-right')
    </div>
</div>

@stop

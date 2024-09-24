@extends('templates.single')

@section('article.content')
<pre>
Category: {!! $category !!}
Title: {!! $post->schemaObject['name'] !!}
Status: {!! $status !!}
Progress: {!! $progress !!}
</pre>
<pre>
Content: {{$post->postContent}}
</pre>
@stop

@section('sidebar.right-sidebar.before')
<pre>
Drivs av
{!! $department !!}
</pre>
<pre>
Kategori
{!! $category !!}
</pre>
<pre>
Teknologier
{!! $technology !!}
</pre>
<pre>
Kontakt
{!! $post->schemaObject['employee']['alternateName'] !!}
</pre>
@stop
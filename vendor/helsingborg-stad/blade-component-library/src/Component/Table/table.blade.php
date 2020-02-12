<!-- table.blade.php -->
@if($list)
  <div id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
    <div class="{{$baseClass}}__inner">
      <table class="{{$baseClass}}__table">
        @if($showCaption)
        <caption>{{ $caption }}</caption>
        @endif

        @if($showHeader)
        <thead class="{{$baseClass}}__head">
          <tr class="{{$baseClass}}__line">
            @foreach($headings as $heading)
              <th scope="col" class="{{$baseClass}}__column {{$baseClass}}__column-{{ $loop->index }}">{{ $heading }}</th>
            @endforeach
          </tr>
        </thead>
        @endif

        <tbody class="{{$baseClass}}__body">
          @foreach($list as $row) 
            <tr class="{{$baseClass}}__line {{$baseClass}}__line-{{ $loop->index }}">
              @foreach($row as $column) 
                <td scope="row" class="{{$baseClass}}__column {{$baseClass}}__column-{{ $loop->index }}">{{ $column }}</td>
              @endforeach
            </tr>
          @endforeach
        </tbody>

        @if($showFooter)
          <tfoot class="{{$baseClass}}__foot">
            <tr class="{{$baseClass}}__line">
              @foreach($headings as $heading)
                <th scope="col" class="{{$baseClass}}__column {{$baseClass}}__column-{{ $loop->index }}">{{ $heading }}</th>
              @endforeach
            </tr>
          </tfoot>
        @endif
      </table>
    </div>
  </div>
@else
  <!-- No table list data -->
@endif
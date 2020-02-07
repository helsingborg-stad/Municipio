<table class="table table--striped {{$class}}">
    @if(isset($table['footer']))
        <thead>
            <tr>
                <th>{{ implode("</th><th>", $table['footer']) }}</th>
            </tr>
        </thead>
    @endif

    @if(isset($table['content']))
        <tbody>
            @foreach($table['content'] as $row)
                <tr>
                    <td>{{ implode("</td><td>", $row) }}</td>
                </tr>
            @endif
        </tbody>
    @endif

    @if(isset($table['footer']))
        <tfoot>
            <tr>
                <td>{{ implode("</td><td>", $table['footer']) }}</td>
            </tr>
        </tfoot>
    @endif
</table>

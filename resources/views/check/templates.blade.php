<h2>Cheque Templates</h2>

<a href="/cheque/template/create">Create New Template</a>

<ul>
@foreach($templates as $t)
    <li>
        {{ $t->name }} |
        <a href="/cheque/template/design/{{ $t->id }}">Design</a> |
        <a href="/cheque/print-form/{{ $t->id }}">Print</a>
    </li>
@endforeach
</ul>

<h2>Print Cheque: {{ $template->name }}</h2>

<form method="POST" action="/cheque/print/{{ $template->id }}">
@csrf

<input name="date" placeholder="Date" required><br>
<input name="account_title" placeholder="Account Title" required><br>
<input name="amount" placeholder="Amount" required><br>

<button>Generate Cheque PDF</button>
</form>

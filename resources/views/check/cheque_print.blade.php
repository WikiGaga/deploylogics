<h2>Print Cheque: {{ $layout->name }}</h2>
<form method="POST" action="{{ route('cheque.print', $layout->id) }}">
@csrf
<label>Date:</label>
<input name="date" placeholder="Date"><br>
<label>Account Title:</label>
<input name="account_title" placeholder="Account Title"><br>
<label>Amount:</label>
<input name="amount" placeholder="Amount"><br>
<button type="submit">Print Cheque</button>
</form>

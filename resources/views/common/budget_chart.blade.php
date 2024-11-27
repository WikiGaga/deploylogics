<div class="container">
    <div class="row mx-5 my-5">
        <h3 class="mb-3">Accounts Budget Stats</h3>
        @if(isset($data['budgets']) && count($data['budgets']) > 0)
            @foreach($data['budgets'] as $budget)
                @php
                    $bg = 'bg-success';
                    if($budget['toAddpercentage'] < 25) { $bg = 'bg-success'; } 
                    if($budget['toAddpercentage'] > 25 && $budget['toAddpercentage'] <= 75) { $bg = 'bg-warning'; } 
                    if($budget['toAddpercentage'] > 75) { $bg = 'bg-danger'; } 
                @endphp
                <div class="col-md-12 mb-3">
                    <div class="kt-widget15__item">
                        <div class="d-flex justify-content-between">
                            {{-- <span class="kt-widget15__stats">
                                {{ number_format($budget['percentage']) }}% Budget will exceed
                            </span>--}}
                            <span class="kt-widget15__text">
                                <b>{{ $budget['accountName'] }}</b>
                            </span>
                        </div>
                        <div class="kt-space-10"></div>
                        <div class="progress" style="height: 22px;">
                            <div class="progress-bar bg-dark" role="progressbar" style="font-size: 12px;width: {{ number_format($budget['usedBalance']) }}%" aria-valuenow="{{ number_format($budget['usedBalance']) }}" aria-valuemin="0" aria-valuemax="100">{{ $budget['usedBalance'] }}</div>
                            <div class="progress-bar {{ $bg }}" role="progressbar" style="font-size: 12px;width: {{ number_format($budget['toAddpercentage']) }}%" aria-valuenow="{{ number_format($budget['toAddpercentage']) }}" aria-valuemin="0" aria-valuemax="100">{{ $budget['addAmount'] }}</div>
                        </div>
                        <div class="kt-space-10"></div>
                        <div class="d-flex justify-content-between">
                            <span class="kt-widget15__stats">
                                Credit Limit : <b>{{ number_format($budget['creditLimit'] , 3) }}</b>
                            </span>
                            <span class="kt-widget15__stats">
                                Debit Limit : <b>{{ number_format($budget['debitLimit'] , 3) }}</b>
                            </span>
                            <span class="kt-widget15__stats">
                                Used Budget : <b>{{ number_format($budget['usedBalance'] , 3) }}</b>
                            </span>
                            <span class="kt-widget15__stats @if($budget['balance'] < 0) bg-danger text-white @endif">
                                Remaning Balance : <b>{{ number_format($budget['balance'] , 3) }}</b>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

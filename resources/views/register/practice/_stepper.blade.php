@php
    $data = [
        [
            'id' => 'account',
            'title' => 'Account'
        ],
        /*[
            'id' => 'industry',
            'title' => 'Industry'
        ],*/
        [
            'id' => 'info',
            'title' =>'Practice Info'
        ],
        [
            'id' => 'insurance',
            'title' => 'Insurance'
        ],
        [
            'id' => 'finish',
            'title' => 'Finish'
        ],
    ];
    $find = false;
@endphp

<div class="container-stepper">
    <ul class="stepper stepper25">
        @foreach($data as $item)
            @if ($item['id'] == $active)
                <li class="active">{{ $item['title'] }}</li>
                @php
                    $find = true;
                @endphp
            @elseif ($item['id'] != $active && !$find)
                <li class="find">{{ $item['title'] }}</li>
            @else
                <li>{{ $item['title'] }}</li>
            @endif

        @endforeach
    </ul>
</div>
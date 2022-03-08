<ul class="nav nav-tabs" id="myTab" role="tablist">
    @foreach($stepsGroups as $key => $steps)
    <li class="nav-item @if ($key == count($stepsGroups)) active @endif">
        <a class="nav-link @if ($key == count($stepsGroups)) active show @endif" id="t{{ $key }}-tab" data-toggle="tab" href="#t{{ $key }}" role="tab" aria-controls="t{{ $key }}" aria-selected="true">Match iteration {{ $key }}</a>
    </li>
    @endforeach
</ul>
<div class="tab-content" id="myTabContent">
    @foreach($stepsGroups as $key => $steps)
    <div class="tab-pane fade @if ($key == count($stepsGroups)) show active @endif" id="t{{ $key }}" role="tabpanel" aria-labelledby="t{{ $key }}-tab">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Step</th>
                <th>Title</th>
                <th>Data</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($steps as $step)
            <tr>
                <td>{{ $step->try }}</td>
                <td>{{ $step->title }}</td>
                <td>{{ $step->data }}</td>
                <td>{{ formatedTimestamp($step->created_at) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>

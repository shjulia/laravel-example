<div class="row">
    @if ($shift->isHasReviewFromPractice())
        <div class="col-md-6">
            <h4>Review from practice</h4>
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>Date</th><td>{{ date('Y-m-d H:i', $shift->reviewFromPractice[0]->date) }}</td>
                </tr>
                <tr>
                    <th>Stars</th><td>{{ $shift->reviewFromPractice[0]->score . ' / 5' }}</td>
                </tr>
                <tr>
                    <th>Text</th><td>{{ $shift->reviewFromPractice[0]->text }}</td>
                </tr>
                <tr>
                    <th>{{ $providerBubbles[$shift->reviewFromPractice[0]->score - 1] }}</th><td>{{ implode(', ', $shift->reviewFromPractice[0]->providerReview->scores->pluck('title')->toArray()) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
    @if ($shift->isHasReviewFromProvider())
        <div class="col-md-6">
            <h4>Review from provider</h4>
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>Date</th><td>{{ date('Y-m-d H:i', $shift->reviewFromProvider[0]->date) }}</td>
                </tr>
                <tr>
                    <th>Stars</th><td>{{ $shift->reviewFromProvider[0]->score . ' / 5' }}</td>
                </tr>
                <tr>
                    <th>Text</th><td>{{ $shift->reviewFromProvider[0]->text }}</td>
                </tr>
                <tr>
                    <th>{{ $practiceBubbles[$shift->reviewFromProvider[0]->score - 1] }}</th><td>{{ implode(', ', $shift->reviewFromProvider[0]->practiceReview->scores->pluck('title')->toArray()) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>

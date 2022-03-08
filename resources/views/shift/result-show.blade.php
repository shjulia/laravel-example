@extends('layouts.main')

@section('content')
    <div>
        <div class="container-fluid shift-result">
            <div class="row title">
                <div class="col-md-12">
                    <h1 class="">Job info</h1>
                </div>
            </div>
            <div class="container">
                <div class="row desc">
                    <div class="col-sm-6 col-6">
                        <p class="title">Total cost</p>
                        <p class="title_val">${{ $shift->cost_for_practice }}</p>
                    </div>
                    <div class="col-sm-6 col-6 text-right">
                        <p class="title">Arrival time</p>
                        <p class="title_val">{{ round($shift->arrival_time / 60) . ' mins' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="hire hire-center shift-result-hire">
            <div class="centralform">
                <p class="provider-ava text-center">
                    <img src="{{ $provider->photo ? $provider->photo_url : '/img/anonim.jpg' }}">
                </p>
                <div class="inputs">
                    <span class="title"></span>
                    <div>
                        <p class="name">{{ $user->full_name }}</p>
                        <p class="position">
                            <span class="pos">{{ $provider->position->title }}</span>
                            @foreach($shift->tasksList as $task)
                                <span class="spec">{{ $task->title }}</span>
                            @endforeach
                        </p>
                        <p class="position"><b>Requested time: </b>{{ $shift->date . ' | ' . $shift->from_time . ' - ' . $shift->to_time }}</p>
                        @if ($user->phone)
                            <div class="row only-mobile">
                                <div class="col-sm-6 col-6">
                                    <a href="{{'sms:' . $user->phone }}" class="btn message"><i class="fa fa-comment" aria-hidden="true"></i> Message</a>
                                </div>
                                <div class="col-sm-6 col-6">
                                    <a href="{{'tel:' . $user->phone }}" class="btn call"><i class="fa fa-phone" aria-hidden="true"></i> Call</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @can('can-review-to-provider', $shift)
                    <div class="form-group row">
                        <div class="col-12">
                            <a href="{{ route('shifts.reviews.review', $shift) }}" class="btn form-button">Finish job and rate provider</a>
                        </div>
                    </div>
                @endcan
                @can('can-watch-review-to-provider', $shift)
                    <div class="form-group row">
                        <div class="col-12">
                            <a href="{{ route('shifts.reviews.watchReviewToProvider', $shift) }}" class="btn form-button">Watch own review</a>
                        </div>
                    </div>
                @endcan
                @can('can-watch-review-to-practice', $shift)
                    <div class="form-group">
                        <a href="{{ route('shifts.reviews.watchReviewToPractice', $shift) }}" class="btn form-button">Show provider review</a>
                    </div>
                @endcan
            </div>
        </div>
    </div>
@endsection

@extends('layouts.main')

@section('content')
    <div class="hire hire-center review">
        <review-to-practice
                inline-template
                :init-rating="{{ $review->score }}"
                v-cloak
        >
            <div class="centralform">
                <a href="{{ route($isPractice ? 'shifts.resultShow' : 'shifts.provider.resultShow', $shift) }}" class="back-x">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
                    <div class="inputs">
                        <div class="form-group">
                            <a href="{{ route($isPractice ? 'shifts.resultShow' : 'shifts.provider.resultShow' , $shift) }}" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                            <span class="title">Rating</span>
                            <hr class="full-hr"/>
                            <div class="starssdiv">
                                <p class="star-title">@{{ title }}</p>
                                <star-rating
                                        :rating="{{ $review->score }}"
                                        :read-only="true"
                                        :show-rating="false"
                                        :star-size="30"
                                        :padding="18"
                                        active-color="#f47f1e"
                                        :rounded-corners="true"
                                ></star-rating>
                            </div>
                        </div>
                        <div class="bubles" v-if="rating >= 3">
                            <p class="star-title">@{{ questionTitle }}</p>
                            <span class="{{ $review->practiceReview->score_friendly_team ? 'active' : '' }}">Friendly team</span>
                            <span class="{{ $review->practiceReview->score_cool_office ? 'active' : '' }}">Cool office</span>
                            <span class="{{ $review->practiceReview->score_great_patient ? 'active' : '' }}">Great Patient</span>
                            <span class="{{ $review->practiceReview->score_well_organized ? 'active' : '' }}">Well Organized</span>
                        </div>
                        <div class="form-group">
                            <textarea name="text" class="form-control" rows="3" readonly>{{ $review->text }}</textarea>
                        </div>
                    </div>
            </div>
        </review-to-practice>
    </div>
@endsection
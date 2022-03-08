@extends('layouts.main')

@section('content')
    <div class="hire hire-center review">
        <review-to-provider
                :init-rating="{{ $review->score }}"
                inline-template
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
                        <div class="bubles">
                            <p class="star-title">@{{ questionTitle }}</p>
                            <span class="{{ $review->providerReview->score_patient_care ? 'active' : '' }}">Patient Care</span>
                            <span class="{{ $review->providerReview->score_friendly ? 'active' : '' }}">Friendly</span>
                            <span class="{{ $review->providerReview->score_hard_worker ? 'active' : '' }}">Hard Worker</span>
                            <span class="{{ $review->providerReview->score_works_well_with_team ? 'active' : '' }}">Works well with team</span>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="3" readonly>{{ $review->text }}</textarea>
                        </div>
                    </div>

            </div>
        </review-to-provider>
    </div>
@endsection
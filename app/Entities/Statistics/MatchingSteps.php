<?php

namespace App\Entities\Statistics;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MatchingSteps - Object for store shift matching steps.
 * In object of these class we can inspect why matching algorithm invited or didn't invite someone.
 *
 * @package App\Entities\Statistics
 * @property int $id
 * @property int $shift_id
 * @property int $try iteration number
 * @property string $title bases by constants of this class
 * @property string $data providers ids
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class MatchingSteps extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /** @var string */
    public const BASE = "Base finding by position and state";
    /** @var string */
    public const BY_HIRED = "Remove hired before providers";
    /** @var string */
    public const AVAILABILITIES = "Remove not available providers by availability settings";
    /** @var string */
    public const PROVIDER_REVIEWS = "Remove providers by reviews from practice with score <=3 stars";
    /** @var string */
    public const PRACTICE_REVIEWS = "Remove providers by reviews on practice with score <=3 stars";
    /** @var string */
    public const BY_HOLIDAY = "Remove providers by availability at holiday";
    /** @var string */
    public const BY_RATES = "Remove providers by rates";
    /** @var string */
    public const AVERAGE = "Remove providers that have average stars < than average stars given from practice "
    . " to providers";
    /** @var string */
    public const BY_ZIP = "Remove providers that have another zip code";
    /** @var string */
    public const BY_CITY = "Remove providers that have another city";
    /** @var string */
    public const BY_AREA = "Remove providers that have another area";
    /** @var string */
    public const BY_30M = "Remove providers whose distance is more than 30m";
    /** @var string */
    public const BY_1H = "Remove providers whose distance is more than 1h";
    /** @var string */
    public const BY_TASKS = "Remove providers that have other routine tasks";
    /** @var string */
    public const NO_IN_PROVIDERS_DISTANCE = "There are no providers by providers distance settings";
    /** @var string */
    public const BY_PROVIDERS_DISTANCE = "Remove providers by providers distance settings";
    /** @var string */
    public const CLOSEST_NOT_FOUND = "Out of radius but close providers not found";
    /** @var string */
    public const CLOSEST_PROVIDERS = "Remove not close providers out of radius";
    /** @var string */
    public const NO_IN_DISTANCE = "There are no providers in 1h distance";
    /** @var string */
    public const LICENSE_STATE = "Remove providers with licenses in another state";
    /** @var string */
    public const NO_HIRES_PRACTICE = "Practice first time hires a Provider. Finding a Provider that has a high rating";
    /** @var string */
    public const FINAL_MATCHING = "Final Matching. Random selection from results";
    /** @var string */
    public const FIRST_JOB = "Provider first time gets matched";
    /** @var string */
    public const EMPTY_RESULT = "Empty Result. Going to previous result";
    /** @var string */
    public const ONE_RESULT = "Only one Result.";
    /** @var string */
    public const BY_MANAGEMENT_TOOLS = "The same practice management software";
}

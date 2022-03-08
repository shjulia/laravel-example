<?php

namespace App\Providers;

use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerPermissions();
        //
    }

    private function registerPermissions()
    {
        Gate::define('admin-panel', function (User $user) {
            return $user->isAdmin() || $user->isSuperAdmin() || $user->isCustomerSuccess() || $user->isAccountant();
        });
        Gate::define('admin-analytics', function (User $user) {
            return $user->isSuperAdmin();
        });
        Gate::define('manage-users', function (User $user) {
            return $user->isSuperAdmin() || $user->isCustomerSuccess();
        });
        Gate::define('view-users', function (User $user) {
            return $user->isSuperAdmin() || $user->isCustomerSuccess() || $user->isAccountant();
        });
        Gate::define('manage-data', function (User $user) {
            return $user->isSuperAdmin();
        });
        Gate::define('manage-shifts', function (User $user) {
            return $user->isSuperAdmin();
        });
        Gate::define('view-shifts', function (User $user) {
            return $user->isSuperAdmin() || $user->isAccountant();
        });
        Gate::define('manage-machine-learning', function (User $user) {
            return $user->isSuperAdmin();
        });
        Gate::define('view-transactions', function (User $user) {
            return $user->isSuperAdmin() || $user->isAccountant();
        });
        Gate::define('login-as', function (User $user) {
            return $user->isSuperAdmin();
        });
        Gate::define('provider-account-details', function (User $user) {
            return $user->isProvider();
        });
        Gate::define('provider-shift', function (User $user) {
            return $user->isProvider();
        });
        Gate::define('practice-details', function (User $user) {
            return $user->isPractice() && isset($user->practice->id) && $user->practice->id;
        });
        Gate::define('can-review-to-provider', function (User $user, Shift $shift) {
            if (!$user->isPractice()) {
                return false;
            }
            return ($user->practice->id == $shift->practice_id)
                //&& (date('Y-m-d') > $shift->date)
                && !$shift->isHasReviewFromPractice();
        });
        Gate::define('can-review-to-practice', function (User $user, Shift $shift) {
            if (!$user->isProvider()) {
                return false;
            }
            return ($user->specialist->user_id ==  $shift->provider_id)
                //&& (date('Y-m-d') > $shift->date)
                && !$shift->isHasReviewFromProvider();
        });
        Gate::define('can-watch-review-to-provider', function (User $user, Shift $shift) {
            $isProvider = $user->isProvider();
            $isPractice = $user->isPractice();
            if (!$isProvider && !$isPractice) {
                return false;
            }
            if ($isProvider && $user->id !== $shift->provider_id) {
                return false;
            } elseif ($isPractice && $user->practice->id !== $shift->practice_id) {
                return false;
            }
            return $shift->isHasReviewFromPractice();
        });
        Gate::define('can-watch-review-to-practice', function (User $user, Shift $shift) {
            $isProvider = $user->isProvider();
            $isPractice = $user->isPractice();
            if (!$isProvider && !$isPractice) {
                return false;
            }
            if ($isProvider && $user->id !== $shift->provider_id) {
                return false;
            } elseif ($isPractice && $user->practice->id !== $shift->practice_id) {
                return false;
            }
            return $shift->isHasReviewFromProvider();
        });
        Gate::define('can-hire', function (User $user) {
            //return $user->isActive() && $user->isPractice() && $user->practice->isSetPaymentInfo();
            return $user->isPractice() && $user->practice->isApproved() && $user->practice->isSetPaymentInfo();
        });
        Gate::define('can-edit-shift', function (User $user, Shift $shift) {
            return $user->practice->id === $shift->practice_id;
        });
        Gate::define('provider-view-shift', function (User $user, Shift $shift) {
            return $user->id === $shift->provider_id;
        });
        Gate::define('can-referral', function (User $user) {
            return isset($user->referral->user_id);
        });

        Gate::define('can-edit-shift-admin', function (User $user, Shift $shift) {
            return $shift->isNoPrividerFoundStatus()
                || $shift->isMatchingStatus()
                || $shift->isCreatingStatus()
                || $shift->isWaitingStatus();
        });

        Gate::define('edit-time-admin', function (User $user, Shift $shift) {
            if ($shift->multi_days) {
                return false;
            }
            return !$shift->isNoPrividerFoundStatus()
                && !$shift->isCanceledStatus()
                && !$shift->isArchived();
        });

        Gate::define('edit-bonus-admin', function (User $user, Shift $shift) {
            return !$shift->isCompleted() && !$shift->isFinishedStatus();
        });

        Gate::define('start-shift', function (User $user, Shift $shift) {
            return !$shift->is_started_by_provider &&
                $shift->provider_id == $user->id &&
                $shift->startsInHours() >= -0.2 &&
                $shift->startsInHours() < 2;
        });

        Gate::define('finish-shift', function (User $user, Shift $shift) {
            return $shift->startsInHours() <= -0.2 &&
                !$shift->isCompleted() &&
                $shift->isAcceptedByProviderStatus();
        });

        Gate::define('deactivate-user', function (User $currentUser, User $user) {
            return $currentUser->isSuperAdmin() || $user->id == $currentUser->id;
        });

        Gate::define('active-for-matching', function (User $user) {
            return $user->isAccountActive();
        });
    }
}

<?php

namespace App\Repositories\User;

use App\Entities\User\License;
use App\Entities\User\Provider\Checkr;
use App\Entities\User\Role;
use App\Entities\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use function Clue\StreamFilter\fun;

/**
 * Class UserRepository
 * @package App\Repositories\User
 */
class UserRepository
{
    /**
     * @param int $id
     * @return User
     */
    public function getById(int $id): User
    {
        if (!$user = User::where('id', $id)->first()) {
            throw new \DomainException('User not found');
        }
        return $user;
    }

    /**
     * @param string $uuid
     * @return User
     */
    public function getByUuid(string $uuid): User
    {
        if (!$user = User::where('uuid', $uuid)->first()) {
            throw new \DomainException('User not found');
        }
        return $user;
    }

    /**
     * @param string $id
     * @return User
     */
    public function getByWalletClientId(string $id): User
    {
        if (
            !$user = User::whereHas('wallet', function ($query) use ($id) {
                $query->where('wallet_client_id', $id);
            })->first()
        ) {
            throw new \DomainException('User not found');
        }
        return $user;
    }

    /**
     * @param string $email
     * @param string $role
     * @return User|null
     */
    public function findByEmailAndRole(string $email, ?string $role = null): ?User
    {
        $query = User::where('email', $email);
        if ($role) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('type', '!=', $role);
            });
        }
        $user = $query->first();
        return $user;
    }

    /**
     * @param null|string $code
     * @return User
     */
    public function getProviderByTmpCode(?string $code): User
    {
        if (!$code) {
            throw new \DomainException('Code expired or not found');
        }
        $user = User::where('tmp_token', $code)->with('specialist.licenses')->first();
        if (!$user) {
            throw new \DomainException('Your previous data not found');
        }
        return $user;
    }

    /**
     * @param null|string $code
     * @return User
     */
    public function getPartnerByTmpCode(?string $code): User
    {
        if (!$code) {
            throw new \DomainException('Type base info first');
        }
        $user = User::where('tmp_token', $code)->first();
        if (!$user) {
            throw new \DomainException('Your previous data not found');
        }
        return $user;
    }

    /**
     * @param null|string $code
     * @return User
     */
    public function getPracticeByTmpCode(?string $code): User
    {
        if (!$code) {
            throw new \DomainException('Code expired or not found');
        }
        $user = User::where('tmp_token', $code)->first();
        if (!$user || !$user->practice) {
            throw new \DomainException('Your previous data not found');
        }
        $user->setAppends(['practice']);
        return $user;
    }

    /**
     * @param int $id
     * @return User
     */
    public function getUserWithPractice(int $id): User
    {
        $user = User::where('id', $id)->first();
        $user->setAppends(['practice']);
        if (!$user || !$user->practice) {
            throw new \DomainException('User not found');
        }
        return $user;
    }

    /**
     * @param Request $request
     * @param bool|null $isTestUsers
     * @param bool|null $withRejected
     * @param bool|null $deactivated
     * @return mixed
     */
    public function findByQueryParams(
        Request $request,
        ?bool $isTestUsers = false,
        ?bool $withRejected = false,
        ?bool $deactivated = false
    ) {
        $query = User::orderByDesc('id')
            ->with('roles')
            ->where('is_test_account', $isTestUsers);

        if (!$withRejected) {
            $query->active();
        } else {
            $query->rejected();
        }

        if ($deactivated) {
            $query->deactivated();
        }

        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('first_name'))) {
            $query->where('first_name', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('last_name'))) {
            $query->where('last_name', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('email'))) {
            $query->where('email', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('status'))) {
            $query->where(function ($q) use ($value) {
                $q->whereHas('specialist', function ($query) use ($value) {
                    $query->where('approval_status', $value);
                })
                ->orWhereHas('practices', function ($query) use ($value) {
                    $query->where('approval_status', $value);
                });
            });
            //$query->where('status', $value);
        }
        if (!empty($value = $request->get('role'))) {
            $query->whereHas('roles', function ($query) use ($value) {
                $query->where('id', $value);
            });
        }
        if (!empty($value = $request->get('position'))) {
            $query->where(function ($query) use ($value) {
                $query->whereHas('specialist', function ($query) use ($value) {
                    $query->where('position_id', $value);
                });
            });
        }
        //if (!empty($value = $request->get('state'))) {
        $state = $request->get('state', null);
        $query->where(function ($query) use ($state) {
            $query->whereHas('specialist', function ($query) use ($state) {
                if ($state) {
                    $query->where('driver_state', $state);
                }
            })->orWhereHas('practices', function ($query) use ($state) {
                if ($state) {
                    $query->where('state', $state);
                }
                $query->where('practice_role', Role::PRACTICE_ADMINISTRATOR);
            });
        });
        //}
        return $query->paginate(20)->appends(
            $request->only(['id', 'first_name', 'last_name', 'email', 'status', 'role', 'position', 'state'])
        );
    }

    /**
     * @param User $userBase
     * @return User
     */
    public function getUserWithFullData(User $userBase): User
    {
        $user = User::where('id', $userBase->id)->with('passwordSetup');
        if ($userBase->isProvider()) {
            $user->with([
                'specialist' => function ($query) {
                    $query->with([
                        'industry',
                        'position',
                        'specialities',
                        'availabilities',
                        'holidays',
                        'licenses'
                    ]);
                }
            ]);
        }
        if ($userBase->isPractice()) {
            $user->with('practices.industry');
        }

        $user = $user->with('referral')->first();
        if ($userBase->isPractice()) {
            $user->practices->each->setAppends(['practice_photo_url']);
        }
        if (!$user) {
            throw new \DomainException('User not found');
        }
        return $user;
    }

    /**
     * @param int $id
     * @return User
     */
    public function getUserFullData(int $id): User
    {
        /** @var User $user */
        $user = User::where('id', $id)->with([
                'roles',
                'specialist.industry',
                'practices.industry'
        ])->first();
        $user->setAppends(['practice']);
        //dd($user);
        return $user;
    }

    /**
     * @param int $id
     * @return User
     */
    public function getUserPracticeFullData(int $id): User
    {
        /** @var User $user */
        $user = User::where('id', $id)->with(['practices.industry'])->first();
        $user->setAppends(['practice']);
        return $user;
    }

    /**
     * @param int $id
     * @return User
     */
    public function getUserProviderFullData(int $id): User
    {
        /** @var User $user */
        $user = User::where('id', $id)->with(['roles'])->first();
        return $user;
    }

    /**
     * @param int $time
     * @param int $userId
     */
    public function setTimeDifference(string $time, int $userId): void
    {
        $user = $this->getById($userId);
        $user->update([
            'tz' => $time
        ]);
    }

    /**
     * @return User
     */
    public function getAdmin(): User
    {
        return User::whereHas('roles', function ($query) {
            $query->where('type', Role::ROLE_SUPER_ADMIN);
        })->first();
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findEmailLogs(User $user)
    {
        return $user->emailLogs()
            ->with('user')
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }

    /**
     * @return array
     */
    public function findListsForApproval(): array
    {
        $red = User::whereIn('signup_step', [
            'provider:industry',
            'practice:industry',
            'practice:base',
            'provider:identity',
            'provider:license',
            'provider:check',
            'practice:insurance'
        ])
            ->active()
            ->where('is_test_account', 0)
            ->with('roles')
            ->orderBy('last_signup_action_date', 'DESC')
            ->get();

        $yellow = User::where('signup_step', null)
            ->where('status', User::WAITING)
            ->where('is_test_account', 0)
            ->active()
            ->whereHas('roles', function ($query) {
                $query->whereIn('type', [Role::ROLE_PRACTICE, Role::ROLE_PROVIDER]);
            })
            ->whereHas('specialist.licenses', function ($query) {
                $query->where('approved_status', '!=', License::STATUS_APPROVED)
                    ->where(function ($query) {
                        $query->where('expiration_date', '<=', Carbon::now()->addDays(60)->format('Y-m-d'))
                            ->orWhere('expiration_date', null);
                    });
            })
            ->with('roles')
            ->orderBy('last_signup_action_date', 'DESC')
            ->get();

        $green = User::where('signup_step', null)
            ->where('status', User::WAITING)
            ->where('is_test_account', 0)
            ->whereHas('roles', function ($query) {
                $query->whereIn('type', [Role::ROLE_PRACTICE, Role::ROLE_PROVIDER]);
            })
            ->active()
            ->whereHas('specialist', function ($query) {
                $query->whereHas('licenses', function ($query) {
                        $query->where('approved_status', License::STATUS_APPROVED)
                            ->where(function ($query) {
                                $query->where('expiration_date', '>=', Carbon::now()->addDays(61)->format('Y-m-d'))
                                    ->orWhere('expiration_date', null);
                            });
                })
                    ->whereHas('checkr', function ($query) {
                        $query->where('checkr_status', Checkr::CHECKR_CLEAR);
                    });
            })
            ->with('roles')
            ->orderBy('last_signup_action_date', 'DESC')
            ->get();

        return [
            'red' => $red,
            'yellow' => $yellow,
            'green' => $green
        ];
    }

    public function findUsersWithFullWallet()
    {
        return User::whereHas('wallet', function ($query) {
            $query->where('balance', '>', 0);
        })
            ->get();
    }
}

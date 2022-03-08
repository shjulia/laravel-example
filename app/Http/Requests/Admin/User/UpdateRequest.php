<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\FormRequest;
use App\Repositories\User\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Admin\User
 */
class UpdateRequest extends FormRequest
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CreateRequest constructor.
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->userRepository = $userRepository;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $user = $this->userRepository->getById($this->user_id);
        if ($user->isProvider()) {
            return [
                'first_name' => 'nullable|string:255',
                'last_name' => 'nullable|string:255',
                'middle_name' => 'nullable|string:255',
                'address' => 'required|string:255',
                'city' => 'required|string:255',
                'state' => 'required|string:20',
                'zip' => 'required|string:10',
                'dob' => 'required|date',
                'expiration_date' => 'required|date',
                'gender' => ['nullable', 'string:2', Rule::in(['M', 'F'])],
                'license' => 'required|string:255'
            ];
        }
        return [

        ];
    }
}

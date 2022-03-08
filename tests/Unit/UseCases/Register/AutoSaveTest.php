<?php


namespace Tests\Unit\UseCases\Register;


use App\Entities\DTO\UserBase;
use App\Entities\User\SignupAutosave;
use App\UseCases\Auth\AutoSaveService;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AutoSaveTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Generator
     */
    private $faker;
    /**
     * @var AutoSaveService
     */
    private $autoSaveService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->autoSaveService = app(AutoSaveService::class);
        $this->faker = Factory::create();
    }

    public function testAutoSave()
    {
        $email = $this->faker->safeEmail;
        $name = $this->faker->firstName;
        $lname = $this->faker->lastName;
        $this->autoSaveService->save($email, $name, $lname);
        $save = SignupAutosave::where(['email' => $email, 'first_name' => $name, 'last_name' => $lname])->first();
        $this->assertNotNull($save);
        $email = $this->faker->safeEmail;
        $this->autoSaveService->save($email);
        $save = SignupAutosave::where(['email' => $email])->first();
        $this->assertNotNull($save);
    }
}

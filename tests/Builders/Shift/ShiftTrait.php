<?php


namespace Tests\Builders\Shift;


use App\Entities\Shift\Shift;
use App\Http\Requests\Shift\ShiftBaseRequest;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\MatchNowJob;
use App\Repositories\Industry\PositionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait ShiftTrait
{
    private function getNewShift(?bool $notime = false): Shift
    {
        $data = new ShiftBaseRequest();
        $position = app()->get(PositionRepository::class)->getByTitle('Dentist');
        $data->merge(['position' => $position->id]);
        $shift = $this->shiftService->createBase($this->testUser->practice, $this->testUser, $data, true);
        $data = new TimeRequest();
        $from = $notime ? Carbon::yesterday()->addMinutes(3)->format('H:i') : '09:30';
        $to = $notime ? Carbon::yesterday()->addHour(3)->format('H:i') :'15:30';
        $startDate = $notime ? Carbon::yesterday()->format('Y-m-d') : Carbon::now()->addDays(1)->format('Y-m-d');
        $endDate = $notime ? Carbon::yesterday()->format('Y-m-d') : Carbon::now()->addDays(1)->format('Y-m-d');
        $data->merge([
            'time_from' => $from,
            'time_to' => $to,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'shift_time' => 360,
            'lunch_break' => 0
        ]);
        $this->shiftService->setTime($shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->expectsJobs(MatchNowJob::class);
        $this->shiftService->startMatching($shift);
        return $shift = $this->shiftRepository->getById($shift->id);
    }

    private function match(Shift $shift): Shift
    {
        $job = new MatchNowJob($shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->shiftService, $this->shiftRepository);
        return $this->shiftRepository->getById($shift->id);
    }

    private function acceptByProvider(Shift $shift): Shift
    {
        $data = new Request();
        $data->merge(['answer' => 'now']);
        $potentialProvider = $shift->potentialProvider;
        $this->providerService->accept($shift, $data, $potentialProvider);
        return $this->shiftRepository->getById($shift->id);
    }
}

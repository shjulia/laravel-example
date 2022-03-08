<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Analytics;

use App\Entities\Notification\EmailLog;

/**
 * Class EmailsRepository
 * @package App\Repositories\Admin\Analytics
 */
class EmailsRepository
{
    /**
     * @return array
     */
    public function findMarketingEmailsAmount(): array
    {
        $emails = [];
        $i = 1;
        foreach (EmailLog::MARKETING_SUBJECTS as $subject) {
            $query = EmailLog::where('subject', $subject);
            $emails[$subject] = [
                'amount' => $query->count(),
                'openedAmount' => $query->where('last_status', EmailLog::OPENED)->count(),
                'key' => $i
            ];
            $i++;
        }
        return $emails;
    }

    /**
     * @param string $subject
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMarketingEmailData(string $subject)
    {
        return EmailLog::where('subject', $subject)
            ->orderBy('id', 'DESC')
            ->with('user')
            ->paginate();
    }
}

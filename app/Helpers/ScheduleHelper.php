<?php

namespace App\Helpers;

class ScheduleHelper {
    /**
     * Get pastel color for therapist column
     *
     * @param int $index Index terapis (0, 1, 2, ...)
     * @return array Array dengan kunci: headerBg, cellBg, calendarBg, avatarBg, borderColor
     */
    public static function getTherapistPastelColor($index) {
        $colors = [
            [
                'headerBg' => 'bg-rose-100 dark:bg-rose-900/20',
                'cellBg' => 'bg-rose-50 dark:bg-rose-900/10',
                'calendarBg' => 'bg-rose-100/50 dark:bg-rose-900/20',
                'avatarBg' => 'bg-rose-500',
                'borderColor' => 'border-rose-500'
            ],
            [
                'headerBg' => 'bg-blue-100 dark:bg-blue-900/20',
                'cellBg' => 'bg-blue-50 dark:bg-blue-900/10',
                'calendarBg' => 'bg-blue-100/50 dark:bg-blue-900/20',
                'avatarBg' => 'bg-blue-500',
                'borderColor' => 'border-blue-500'
            ],
            [
                'headerBg' => 'bg-purple-100 dark:bg-purple-900/20',
                'cellBg' => 'bg-purple-50 dark:bg-purple-900/10',
                'calendarBg' => 'bg-purple-100/50 dark:bg-purple-900/20',
                'avatarBg' => 'bg-purple-500',
                'borderColor' => 'border-purple-500'
            ],
            [
                'headerBg' => 'bg-emerald-100 dark:bg-emerald-900/20',
                'cellBg' => 'bg-emerald-50 dark:bg-emerald-900/10',
                'calendarBg' => 'bg-emerald-100/50 dark:bg-emerald-900/20',
                'avatarBg' => 'bg-emerald-500',
                'borderColor' => 'border-emerald-500'
            ],
            [
                'headerBg' => 'bg-cyan-100 dark:bg-cyan-900/20',
                'cellBg' => 'bg-cyan-50 dark:bg-cyan-900/10',
                'calendarBg' => 'bg-cyan-100/50 dark:bg-cyan-900/20',
                'avatarBg' => 'bg-cyan-500',
                'borderColor' => 'border-cyan-500'
            ],
            [
                'headerBg' => 'bg-amber-100 dark:bg-amber-900/20',
                'cellBg' => 'bg-amber-50 dark:bg-amber-900/10',
                'calendarBg' => 'bg-amber-100/50 dark:bg-amber-900/20',
                'avatarBg' => 'bg-amber-500',
                'borderColor' => 'border-amber-500'
            ],
            [
                'headerBg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/20',
                'cellBg' => 'bg-fuchsia-50 dark:bg-fuchsia-900/10',
                'calendarBg' => 'bg-fuchsia-100/50 dark:bg-fuchsia-900/20',
                'avatarBg' => 'bg-fuchsia-500',
                'borderColor' => 'border-fuchsia-500'
            ],
            [
                'headerBg' => 'bg-teal-100 dark:bg-teal-900/20',
                'cellBg' => 'bg-teal-50 dark:bg-teal-900/10',
                'calendarBg' => 'bg-teal-100/50 dark:bg-teal-900/20',
                'avatarBg' => 'bg-teal-500',
                'borderColor' => 'border-teal-500'
            ]
        ];

        return $colors[$index % count($colors)];
    }
}

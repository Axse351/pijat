<?php

namespace App\Helpers;

use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\TherapistAttendance;
use Carbon\Carbon;

class AttendanceHelper
{
    /**
     * Validate attendance berdasarkan schedule
     * Cek apakah terapis seharusnya masuk hari ini
     */
    public static function isScheduledToday($therapistId): bool
    {
        $schedule = TherapistSchedule::getTodaySchedule($therapistId);

        if (!$schedule) {
            // Jika tidak ada jadwal, anggap tidak dijadwalkan
            return false;
        }

        return $schedule->isWorking();
    }

    /**
     * Get expected check-in time
     */
    public static function getExpectedCheckInTime($therapistId): ?Carbon
    {
        $schedule = TherapistSchedule::getTodaySchedule($therapistId);

        if (!$schedule || !$schedule->isWorking()) {
            return null;
        }

        // Combine tanggal hari ini dengan waktu start_time dari jadwal
        return Carbon::today()->setTimeFromTimeString($schedule->start_time->format('H:i'));
    }

    /**
     * Get expected check-out time
     */
    public static function getExpectedCheckOutTime($therapistId): ?Carbon
    {
        $schedule = TherapistSchedule::getTodaySchedule($therapistId);

        if (!$schedule || !$schedule->isWorking()) {
            return null;
        }

        return Carbon::today()->setTimeFromTimeString($schedule->end_time->format('H:i'));
    }

    /**
     * Check if attendance is late
     */
    public static function isLate($therapistId, $checkInTime): bool
    {
        $expectedCheckInTime = self::getExpectedCheckInTime($therapistId);

        if (!$expectedCheckInTime) {
            return false;
        }

        return $checkInTime->isAfter($expectedCheckInTime);
    }

    /**
     * Get attendance status based on schedule
     */
    public static function determineAttendanceStatus($therapistId, $checkInTime): string
    {
        $schedule = TherapistSchedule::getTodaySchedule($therapistId);

        // Tidak ada jadwal = absen
        if (!$schedule) {
            return 'absent';
        }

        // Jadwal libur = tidak perlu absen
        if ($schedule->isOff()) {
            return 'absent'; // atau bisa 'off' jika ada status itu
        }

        // Check if late
        if (self::isLate($therapistId, $checkInTime)) {
            return 'late';
        }

        return 'present';
    }

    /**
     * Get schedule summary untuk terapis hari ini
     */
    public static function getTodayScheduleSummary($therapistId): ?array
    {
        $schedule = TherapistSchedule::getTodaySchedule($therapistId);

        if (!$schedule) {
            return null;
        }

        return [
            'date' => $schedule->schedule_date,
            'status' => $schedule->status,
            'label' => $schedule->getStatusLabel(),
            'is_working' => $schedule->isWorking(),
            'start_time' => $schedule->getStartTimeFormatted(),
            'end_time' => $schedule->getEndTimeFormatted(),
            'working_hours' => $schedule->working_hours,
            'notes' => $schedule->notes,
        ];
    }

    /**
     * Get therapist dengan status schedule hari ini
     */
    public static function getTherapistsWithTodaySchedule()
    {
        $therapists = Therapist::with('attendances')->get();

        return $therapists->map(function ($therapist) {
            return [
                'therapist' => $therapist,
                'schedule' => TherapistSchedule::getTodaySchedule($therapist->id),
                'attendance' => $therapist->attendances->first(),
                'should_work' => self::isScheduledToday($therapist->id),
            ];
        });
    }

    /**
     * Generate attendance report bulanan
     */
    public static function getMonthlyAttendanceReport($therapistId, $month, $year)
    {
        $schedules = TherapistSchedule::getMonthSchedule($therapistId, $year, $month);
        $attendances = TherapistAttendance::where('therapist_id', $therapistId)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get()
            ->keyBy('attendance_date');

        $report = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'off' => 0,
            'total_working_days' => 0,
            'total_hours' => 0,
            'details' => [],
        ];

        foreach ($schedules as $schedule) {
            $dateStr = $schedule->schedule_date->format('Y-m-d');
            $attendance = $attendances->get($dateStr);

            if ($schedule->isWorking()) {
                $report['total_working_days']++;
                $report['total_hours'] += $schedule->working_hours ?? 0;

                if ($attendance) {
                    $status = $attendance->status;
                    $report[$status]++;
                } else {
                    $report['absent']++;
                }
            } else {
                $report['off']++;
            }

            $report['details'][] = [
                'date' => $schedule->schedule_date,
                'schedule_status' => $schedule->status,
                'attendance_status' => $attendance?->status ?? 'absent',
                'check_in_time' => $attendance?->getCheckInTimeFormatted(),
                'check_out_time' => $attendance?->getCheckOutTimeFormatted(),
                'notes' => $schedule->notes,
            ];
        }

        return $report;
    }
}

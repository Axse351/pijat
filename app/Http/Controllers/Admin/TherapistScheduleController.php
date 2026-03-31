<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TherapistScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $therapists = Therapist::orderBy('name')->get();
        $selectedTherapist = $request->input('therapist_id', $therapists->first()?->id);

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $schedules = collect();

        if ($selectedTherapist) {
            $schedules = TherapistSchedule::getMonthSchedule($selectedTherapist, $year, $month);
        }

        $calendarDays = $this->generateCalendarDays($year, $month, $schedules);

        return view('admin.schedules.index', [
            'therapists'        => $therapists,
            'selectedTherapist' => $selectedTherapist,
            'month'             => $month,
            'year'              => $year,
            'currentDate'       => $currentDate,
            'schedules'         => $schedules,
            'calendarDays'      => $calendarDays,
        ]);
    }

    // ── BARU: Semua terapis sekaligus ──────────────────────────────────
    public function allSchedules(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year',  Carbon::now()->year);

        $therapists = Therapist::orderBy('name')->get();

        $allSchedules = TherapistSchedule::whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->get();

        $firstDay = Carbon::createFromDate($year, $month, 1);
        $lastDay  = $firstDay->clone()->endOfMonth();
        $firstDow = $firstDay->dayOfWeek;

        // Kelompokkan: [therapist_id][Y-m-d] = schedule
        $schedByTherapistDate = [];
        foreach ($allSchedules as $s) {
            $dateKey = Carbon::parse($s->schedule_date)->format('Y-m-d');
            $schedByTherapistDate[$s->therapist_id][$dateKey] = $s;
        }

        $days = [];
        $date = $firstDay->clone();
        while ($date <= $lastDay) {
            $dateKey  = $date->format('Y-m-d');
            $daySched = [];
            foreach ($therapists as $t) {
                $daySched[$t->id] = $schedByTherapistDate[$t->id][$dateKey] ?? null;
            }
            $days[] = ['date' => $date->clone(), 'schedules' => $daySched];
            $date->addDay();
        }

        return view('admin.schedules.all', [
            'therapists'   => $therapists,
            'allSchedules' => $allSchedules,
            'days'         => $days,
            'firstDow'     => $firstDow,
            'month'        => $month,
            'year'         => $year,
        ]);
    }

    public function create(Request $request): View
    {
        $therapists = Therapist::orderBy('name')->get();
        $month = $request->input('month', Carbon::now()->month);
        $year  = $request->input('year',  Carbon::now()->year);

        // Pre-fill tanggal jika dari link "tambah" di kalender all
        $defaultDate = $request->input(
            'date',
            Carbon::createFromDate($year, $month, 1)->format('Y-m-d')
        );

        return view('admin.schedules.create', [
            'therapists'  => $therapists,
            'month'       => $month,
            'year'        => $year,
            'defaultDate' => $defaultDate,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id'  => 'required|exists:therapists,id',
            'schedule_date' => 'required|date',
            'status'        => 'required|in:working,off,sick,vacation,cuti_bersama',
            'start_time'    => 'nullable|date_format:H:i|required_if:status,working',
            'end_time'      => 'nullable|date_format:H:i|required_if:status,working|after:start_time',
            'notes'         => 'nullable|string|max:500',
        ]);

        $scheduleDate = Carbon::createFromFormat('Y-m-d', $validated['schedule_date']);

        if ($validated['status'] === 'working') {
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time'])->toTimeString();
            $endTime   = Carbon::createFromFormat('H:i', $validated['end_time'])->toTimeString();
        } else {
            $startTime = null;
            $endTime   = null;
        }

        TherapistSchedule::updateOrCreate(
            [
                'therapist_id'  => $validated['therapist_id'],
                'schedule_date' => $scheduleDate->toDateString(),
            ],
            [
                'day_of_week' => $scheduleDate->dayOfWeek,
                'status'      => $validated['status'],
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'notes'       => $validated['notes'] ?? null,
                'created_by'  => auth()->id(),
            ]
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', __('Jadwal berhasil disimpan'));
    }

    // ── DIPERBARUI: tambah prevSchedule & nextSchedule ─────────────────
    public function edit(TherapistSchedule $schedule): View
    {
        $therapists = Therapist::orderBy('name')->get();

        $prevSchedule = TherapistSchedule::where('therapist_id', $schedule->therapist_id)
            ->where('schedule_date', '<', $schedule->schedule_date)
            ->orderBy('schedule_date', 'desc')
            ->first();

        $nextSchedule = TherapistSchedule::where('therapist_id', $schedule->therapist_id)
            ->where('schedule_date', '>', $schedule->schedule_date)
            ->orderBy('schedule_date', 'asc')
            ->first();

        return view('admin.schedules.edit', [
            'schedule'     => $schedule,
            'therapists'   => $therapists,
            'prevSchedule' => $prevSchedule,
            'nextSchedule' => $nextSchedule,
        ]);
    }

    public function update(Request $request, TherapistSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id'  => 'required|exists:therapists,id',
            'schedule_date' => 'required|date',
            'status'        => 'required|in:working,off,sick,vacation,cuti_bersama',
            'start_time'    => 'nullable|date_format:H:i|required_if:status,working',
            'end_time'      => 'nullable|date_format:H:i|required_if:status,working|after:start_time',
            'notes'         => 'nullable|string|max:500',
        ]);

        $scheduleDate = Carbon::createFromFormat('Y-m-d', $validated['schedule_date']);
        $validated['day_of_week'] = $scheduleDate->dayOfWeek;

        if ($validated['status'] === 'working') {
            $validated['start_time'] = Carbon::createFromFormat('H:i', $validated['start_time'])->toTimeString();
            $validated['end_time']   = Carbon::createFromFormat('H:i', $validated['end_time'])->toTimeString();
        } else {
            $validated['start_time'] = null;
            $validated['end_time']   = null;
        }

        $schedule->update($validated);

        // Redirect kembali ke all jika referer dari all, atau ke index
        $redirectMonth = Carbon::parse($schedule->schedule_date)->month;
        $redirectYear  = Carbon::parse($schedule->schedule_date)->year;

        return redirect()
            ->route('admin.schedules.all', ['month' => $redirectMonth, 'year' => $redirectYear])
            ->with('success', __('Jadwal berhasil diperbarui'));
    }

    public function destroy(TherapistSchedule $schedule): RedirectResponse
    {
        $month = Carbon::parse($schedule->schedule_date)->month;
        $year  = Carbon::parse($schedule->schedule_date)->year;

        $schedule->delete();

        return redirect()
            ->route('admin.schedules.all', ['month' => $month, 'year' => $year])
            ->with('success', __('Jadwal berhasil dihapus'));
    }

    public function generateMonthSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'month'        => 'required|integer|min:1|max:12',
            'year'         => 'required|integer|min:2000',
            'working_days' => 'required|array|min:1|max:7',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'off_dates'    => 'nullable|array',
        ]);

        $therapistId = $validated['therapist_id'];
        $month       = $validated['month'];
        $year        = $validated['year'];
        $workingDays = $validated['working_days'];
        $startTime   = $validated['start_time'];
        $endTime     = $validated['end_time'];
        $offDates    = $validated['off_dates'] ?? [];

        TherapistSchedule::where('therapist_id', $therapistId)
            ->whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->delete();

        $date    = Carbon::createFromDate($year, $month, 1);
        $endDate = $date->clone()->endOfMonth();

        while ($date <= $endDate) {
            $dayOfWeek = $date->dayOfWeek;
            $dateStr   = $date->format('Y-m-d');

            if (in_array($dateStr, $offDates)) {
                $status = 'off';
                $st = null;
                $et = null;
            } elseif (in_array($dayOfWeek, $workingDays)) {
                $status = 'working';
                $st = $startTime;
                $et = $endTime;
            } else {
                $status = 'off';
                $st = null;
                $et = null;
            }

            TherapistSchedule::create([
                'therapist_id'  => $therapistId,
                'schedule_date' => $date->clone(),
                'day_of_week'   => $dayOfWeek,
                'status'        => $status,
                'start_time'    => $st,
                'end_time'      => $et,
                'created_by'    => auth()->id(),
            ]);

            $date->addDay();
        }

        return redirect()
            ->route('admin.schedules.index', ['therapist_id' => $therapistId, 'month' => $month, 'year' => $year])
            ->with('success', __('Jadwal otomatis berhasil dibuat untuk bulan ini'));
    }

    private function generateCalendarDays($year, $month, $schedules)
    {
        $firstDay = Carbon::createFromDate($year, $month, 1);
        $endDay   = $firstDay->clone()->endOfMonth();

        $schedulesKeyed = $schedules->keyBy(function ($s) {
            return Carbon::parse($s->schedule_date)->format('Y-m-d');
        });

        $days = [];

        for ($i = 0; $i < $firstDay->dayOfWeek; $i++) {
            $days[] = ['date' => null, 'schedule' => null];
        }

        $date = $firstDay->clone();
        while ($date <= $endDay) {
            $days[] = [
                'date'     => $date->clone(),
                'schedule' => $schedulesKeyed->get($date->format('Y-m-d')),
            ];
            $date->addDay();
        }

        for ($i = $endDay->dayOfWeek; $i < 6; $i++) {
            $days[] = ['date' => null, 'schedule' => null];
        }

        return $days;
    }
}

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
    /**
     * Tampilkan jadwal bulanan
     */
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

        // Generate array untuk calendar view
        $calendarDays = $this->generateCalendarDays($year, $month, $schedules);

        return view('admin.schedules.index', [
            'therapists' => $therapists,
            'selectedTherapist' => $selectedTherapist,
            'month' => $month,
            'year' => $year,
            'currentDate' => $currentDate,
            'schedules' => $schedules,
            'calendarDays' => $calendarDays,
        ]);
    }

    /**
     * Form buat jadwal baru
     */
    public function create(Request $request): View
    {
        $therapists = Therapist::orderBy('name')->get();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        return view('admin.schedules.create', [
            'therapists' => $therapists,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Simpan jadwal baru
     */
    /**
     * Simpan jadwal baru — jika tanggal sudah ada, UPDATE (bukan insert duplikat)
     */
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

        // Handle time format
        if ($validated['status'] === 'working') {
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time'])->toTimeString();
            $endTime   = Carbon::createFromFormat('H:i', $validated['end_time'])->toTimeString();
        } else {
            $startTime = null;
            $endTime   = null;
        }

        // Cari jadwal existing, update kalau ada — buat baru kalau belum ada
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

    /**
     * Edit jadwal
     */
    public function edit(TherapistSchedule $schedule): View
    {
        $therapists = Therapist::orderBy('name')->get();

        return view('admin.schedules.edit', [
            'schedule' => $schedule,
            'therapists' => $therapists,
        ]);
    }

    /**
     * Update jadwal
     */
    public function update(Request $request, TherapistSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'schedule_date' => 'required|date',
            'status' => 'required|in:working,off,sick,vacation,cuti_bersama',
            'start_time' => 'nullable|date_format:H:i|required_if:status,working',
            'end_time' => 'nullable|date_format:H:i|required_if:status,working|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $scheduleDate = Carbon::createFromFormat('Y-m-d', $validated['schedule_date']);
        $validated['day_of_week'] = $scheduleDate->dayOfWeek;

        // Handle time format
        if ($validated['status'] === 'working') {
            $validated['start_time'] = Carbon::createFromFormat('H:i', $validated['start_time'])->toTimeString();
            $validated['end_time'] = Carbon::createFromFormat('H:i', $validated['end_time'])->toTimeString();
        } else {
            $validated['start_time'] = null;
            $validated['end_time'] = null;
        }

        $schedule->update($validated);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', __('Jadwal berhasil diperbarui'));
    }

    /**
     * Hapus jadwal
     */
    public function destroy(TherapistSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('success', __('Jadwal berhasil dihapus'));
    }

    /**
     * Generate jadwal otomatis untuk bulan ini
     */
    public function generateMonthSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'working_days' => 'required|array|min:1|max:7', // Hari-hari kerja (0-6)
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'off_dates' => 'nullable|array',
        ]);

        $therapistId = $validated['therapist_id'];
        $month = $validated['month'];
        $year = $validated['year'];
        $workingDays = $validated['working_days'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        $offDates = $validated['off_dates'] ?? [];

        // Hapus jadwal yang sudah ada untuk bulan ini
        TherapistSchedule::where('therapist_id', $therapistId)
            ->whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->delete();

        // Generate jadwal untuk setiap hari dalam bulan
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->clone()->endOfMonth();

        $date = $startDate->clone();
        while ($date <= $endDate) {
            $dayOfWeek = $date->dayOfWeek;
            $dateStr = $date->format('Y-m-d');

            // Cek apakah hari ini masuk ke hari libur manual
            if (in_array($dateStr, $offDates)) {
                TherapistSchedule::create([
                    'therapist_id' => $therapistId,
                    'schedule_date' => $date->clone(),
                    'day_of_week' => $dayOfWeek,
                    'status' => 'off',
                    'created_by' => auth()->id(),
                ]);
            }
            // Cek apakah hari ini masuk ke hari kerja
            elseif (in_array($dayOfWeek, $workingDays)) {
                TherapistSchedule::create([
                    'therapist_id' => $therapistId,
                    'schedule_date' => $date->clone(),
                    'day_of_week' => $dayOfWeek,
                    'status' => 'working',
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'created_by' => auth()->id(),
                ]);
            }
            // Hari lainnya adalah off
            else {
                TherapistSchedule::create([
                    'therapist_id' => $therapistId,
                    'schedule_date' => $date->clone(),
                    'day_of_week' => $dayOfWeek,
                    'status' => 'off',
                    'created_by' => auth()->id(),
                ]);
            }

            $date->addDay();
        }

        return redirect()
            ->route('admin.schedules.index', ['therapist_id' => $therapistId, 'month' => $month, 'year' => $year])
            ->with('success', __('Jadwal otomatis berhasil dibuat untuk bulan ini'));
    }

    /**
     * Generate calendar days array untuk view
     */
    /**
     * Generate calendar days array untuk view
     * FIX: gunakan Carbon::parse() agar perbandingan date string selalu cocok
     * meskipun schedule_date disimpan sebagai datetime/Carbon object
     */
    private function generateCalendarDays($year, $month, $schedules)
    {
        $firstDay = Carbon::createFromDate($year, $month, 1);
        $endDay   = $firstDay->clone()->endOfMonth();

        // Key schedules by Y-m-d string untuk O(1) lookup
        $schedulesKeyed = $schedules->keyBy(function ($s) {
            return Carbon::parse($s->schedule_date)->format('Y-m-d');
        });

        $days = [];

        // Empty cells before first day (Sun = 0)
        for ($i = 0; $i < $firstDay->dayOfWeek; $i++) {
            $days[] = ['date' => null, 'schedule' => null];
        }

        // Days of month
        $date = $firstDay->clone();
        while ($date <= $endDay) {
            $dateStr = $date->format('Y-m-d');

            $days[] = [
                'date'     => $date->clone(),
                'schedule' => $schedulesKeyed->get($dateStr),
            ];

            $date->addDay();
        }

        // Empty cells after last day
        for ($i = $endDay->dayOfWeek; $i < 6; $i++) {
            $days[] = ['date' => null, 'schedule' => null];
        }

        return $days;
    }
}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tambah Jadwal Terapis') }}
            </h2>
            <a href="{{ route('admin.schedules.index') }}"
                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition">
                ← {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <!-- Alert Errors -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300 font-semibold mb-2">{{ __('Terjadi Kesalahan:') }}</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.schedules.store') }}" method="POST" x-data="scheduleForm()">
                        @csrf

                        <!-- Pilih Terapis -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Terapis') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="therapist_id" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 @error('therapist_id') border-red-500 @enderror">
                                <option value="">-- Pilih Terapis --</option>
                                @foreach ($therapists as $therapist)
                                    <option value="{{ $therapist->id }}"
                                        {{ old('therapist_id', request('therapist_id')) == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapist->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('therapist_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Tanggal') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="schedule_date" required
                                value="{{ old('schedule_date', \Carbon\Carbon::createFromDate($year, $month, 1)->format('Y-m-d')) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 @error('schedule_date') border-red-500 @enderror">
                            @error('schedule_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Status') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required x-model="status"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                <option value="">-- Pilih Status --</option>
                                <option value="working" {{ old('status') === 'working' ? 'selected' : '' }}>Kerja
                                </option>
                                <option value="off" {{ old('status') === 'off' ? 'selected' : '' }}>Libur
                                </option>
                                <option value="sick" {{ old('status') === 'sick' ? 'selected' : '' }}>Sakit
                                </option>
                                <option value="vacation" {{ old('status') === 'vacation' ? 'selected' : '' }}>
                                    Liburan</option>
                                <option value="cuti_bersama" {{ old('status') === 'cuti_bersama' ? 'selected' : '' }}>
                                    Cuti Bersama</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Kerja (hanya muncul kalau status = working) -->
                        <div x-show="status === 'working'" x-transition class="mb-5 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Jam Masuk') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}"
                                    :required="status === 'working'"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 @error('start_time') border-red-500 @enderror">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Jam Keluar') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}"
                                    :required="status === 'working'"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 @error('end_time') border-red-500 @enderror">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Catatan') }}
                            </label>
                            <textarea name="notes" rows="3" placeholder="{{ __('Catatan tambahan (opsional)') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.schedules.index', ['therapist_id' => request('therapist_id'), 'month' => $month, 'year' => $year]) }}"
                                class="px-5 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit"
                                class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition">
                                {{ __('Simpan Jadwal') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function scheduleForm() {
            return {
                status: '{{ old('status', '') }}',
            }
        }
    </script>
</x-app-layout>

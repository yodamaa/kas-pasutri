@props(['budgets'])

@if (count($budgets) > 0)
    <div class="space-y-3 sm:space-y-4">
        @foreach ($budgets as $budget)
            @php
                $persentase = min((float) $budget['persentase'], 100);
                $color = $budget['persentase'] >= 100 ? 'bg-danger-500' : ($budget['persentase'] >= 80 ? 'bg-warning-500' : 'bg-success-500');
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 truncate mr-2">
                        {{ $budget['nama'] }}
                    </span>
                    <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $budget['persentase'] }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5 dark:bg-gray-700">
                    <div class="{{ $color }} h-2 sm:h-2.5 rounded-full transition-all duration-300"
                         style="width: {{ $persentase }}%"></div>
                </div>
                <div class="flex justify-between mt-0.5">
                    <span class="text-[10px] sm:text-xs text-gray-400 dark:text-gray-500">
                        Rp {{ number_format($budget['terpakai'], 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-gray-400 dark:text-gray-500">
                        Rp {{ number_format($budget['anggaran'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 text-center py-4">
        Belum ada anggaran untuk bulan ini.
    </p>
@endif
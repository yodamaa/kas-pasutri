@if($this->isActiveSuperadmin())
<x-filament-widgets::widget>
    <div class="fi-sidebar-section px-3 py-2">
        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 block">
            Lihat Data Pasangan
        </label>
        <select
            wire:model.live="activeCoupleId"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
        >
            <option value="">-- Pilih Pasangan --</option>
            @foreach($this->getCouples() as $id => $nama)
                <option value="{{ $id }}" {{ $activeCoupleId == $id ? 'selected' : '' }}>
                    {{ $nama }}
                </option>
            @endforeach
        </select>
    </div>
</x-filament-widgets::widget>
@endif

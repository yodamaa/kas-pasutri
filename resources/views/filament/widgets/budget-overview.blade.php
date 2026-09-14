<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Anggaran Bulan {{ $this->getMonthLabel() }}
        </x-slot>

        <x-budget-progress :budgets="$this->getBudgets()" />
    </x-filament::section>
</x-filament-widgets::widget>
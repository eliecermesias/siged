<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label for="year">Año</flux:label>
        <flux:input id="year" name="year" type="number" min="1900" max="9999" :value="old('year', $period?->year)" required />
        <flux:error name="year" />
    </flux:field>

    <flux:field>
        <flux:label for="period">Período</flux:label>
        <flux:input id="period" name="period" :value="old('period', $period?->period)" placeholder="Ej. 2026A" required maxlength="5" autocomplete="off" />
        <p class="text-sm text-slate-500 dark:text-slate-300">Usa el año seguido de A o B, por ejemplo, 2026A.</p>
        <flux:error name="period" />
    </flux:field>

    <flux:field>
        <flux:label for="start_date">Fecha de inicio</flux:label>
        <flux:input id="start_date" name="start_date" type="date" :value="old('start_date', $period?->start_date?->format('Y-m-d'))" />
        <flux:error name="start_date" />
    </flux:field>

    <flux:field>
        <flux:label for="end_date">Fecha final</flux:label>
        <flux:input id="end_date" name="end_date" type="date" :value="old('end_date', $period?->end_date?->format('Y-m-d'))" />
        <flux:error name="end_date" />
    </flux:field>

    <flux:field variant="inline">
        <input type="hidden" name="is_active" value="0">
        <flux:checkbox id="is_active" name="is_active" value="1" :checked="(bool) old('is_active', $period?->is_active ?? true)" />
        <flux:label for="is_active">Período activo</flux:label>
        <flux:error name="is_active" />
    </flux:field>
</div>

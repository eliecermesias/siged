<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label for="person_id">Persona</flux:label>
        <flux:select id="person_id" name="person_id" required>
            <flux:select.option value="">Selecciona una persona</flux:select.option>
            @foreach ($people as $person)
                <flux:select.option
                    value="{{ $person->id }}"
                    :selected="(string) old('person_id', $coordinator?->person_id) === (string) $person->id"
                >
                    {{ $person->last_name }}, {{ $person->first_name }} — {{ $person->document }}
                </flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="person_id" />
    </flux:field>

    <flux:field>
        <flux:label for="code">Código coordinador</flux:label>
        <flux:input id="code" name="code" :value="old('code', $coordinator?->code)" required maxlength="10" autocomplete="off" />
        <flux:error name="code" />
    </flux:field>

    <flux:field variant="inline">
        <input type="hidden" name="is_active" value="0">
        <flux:checkbox id="is_active" name="is_active" value="1" :checked="(bool) old('is_active', $coordinator?->is_active ?? true)" />
        <flux:label for="is_active">Coordinador activo</flux:label>
        <flux:error name="is_active" />
    </flux:field>
</div>

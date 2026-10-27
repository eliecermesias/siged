<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label for="first_name">Nombres</flux:label>
        <flux:input id="first_name" name="first_name" :value="old('first_name', $person?->first_name)" required autofocus autocomplete="given-name" />
        <flux:error name="first_name" />
    </flux:field>

    <flux:field>
        <flux:label for="last_name">Apellidos</flux:label>
        <flux:input id="last_name" name="last_name" :value="old('last_name', $person?->last_name)" required autocomplete="family-name" />
        <flux:error name="last_name" />
    </flux:field>

    <flux:field>
        <flux:label for="document_type_id">Tipo de documento</flux:label>
        <flux:select id="document_type_id" name="document_type_id" required>
            <flux:select.option value="">Selecciona una opción</flux:select.option>
            @foreach ($documentTypes as $documentType)
                <flux:select.option value="{{ $documentType->id }}" :selected="(string) old('document_type_id', $person?->document_type_id) === (string) $documentType->id">
                    {{ $documentType->name }}
                </flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="document_type_id" />
    </flux:field>

    <flux:field>
        <flux:label for="document">Número de documento</flux:label>
        <flux:input id="document" name="document" :value="old('document', $person?->document)" required maxlength="20" autocomplete="off" />
        <flux:error name="document" />
    </flux:field>

    <flux:field>
        <flux:label for="email">Correo electrónico</flux:label>
        <flux:input id="email" name="email" type="email" :value="old('email', $person?->email)" required autocomplete="email" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label for="phone">Teléfono</flux:label>
        <flux:input id="phone" name="phone" :value="old('phone', $person?->phone)" maxlength="20" autocomplete="tel" />
        <flux:error name="phone" />
    </flux:field>

    <flux:field>
        <flux:label for="birth_date">Fecha de nacimiento</flux:label>
        <flux:input id="birth_date" name="birth_date" type="date" :value="old('birth_date', $person?->birth_date?->format('Y-m-d'))" max="{{ now()->toDateString() }}" />
        <flux:error name="birth_date" />
    </flux:field>

    <flux:field>
        <flux:label for="address">Dirección</flux:label>
        <flux:input id="address" name="address" :value="old('address', $person?->address)" maxlength="255" autocomplete="street-address" />
        <flux:error name="address" />
    </flux:field>
</div>

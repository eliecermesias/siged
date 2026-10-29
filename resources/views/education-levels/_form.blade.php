<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label for="name">Nombre</flux:label>
        <flux:input id="name" name="name" :value="old('name', $educationLevel?->name)" required maxlength="255" autocomplete="off" />
        <flux:error name="name" />
    </flux:field>
    <flux:field>
        <flux:label for="description">Descripción</flux:label>
        <flux:input id="description" name="description" :value="old('description', $educationLevel?->description)" maxlength="255" />
        <flux:error name="description" />
    </flux:field>
    <flux:field variant="inline">
        <input type="hidden" name="is_active" value="0">
        <flux:checkbox id="is_active" name="is_active" value="1" :checked="(bool) old('is_active', $educationLevel?->is_active ?? true)" />
        <flux:label for="is_active">Nivel activo</flux:label>
        <flux:error name="is_active" />
    </flux:field>
</div>

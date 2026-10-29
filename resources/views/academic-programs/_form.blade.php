<div class="grid gap-6 md:grid-cols-2">
    <flux:field>
        <flux:label for="name">Nombre del programa</flux:label>
        <flux:input id="name" name="name" :value="old('name', $academicProgram?->name)" required maxlength="255" autocomplete="off" />
        <flux:error name="name" />
    </flux:field>
    <flux:field>
        <flux:label for="snies">Código SNIES</flux:label>
        <flux:input id="snies" name="snies" :value="old('snies', $academicProgram?->snies)" required maxlength="10" autocomplete="off" />
        <flux:error name="snies" />
    </flux:field>
    <flux:field>
        <flux:label for="education_level_id">Nivel de formación</flux:label>
        <flux:select id="education_level_id" name="education_level_id" required>
            <flux:select.option value="">Selecciona un nivel</flux:select.option>
            @foreach ($educationLevels as $educationLevel)
                <flux:select.option value="{{ $educationLevel->id }}" :selected="(string) old('education_level_id', $academicProgram?->education_level_id) === (string) $educationLevel->id">{{ $educationLevel->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="education_level_id" />
    </flux:field>
    <flux:field>
        <flux:label for="knowledge_area_id">Área del conocimiento</flux:label>
        <flux:select id="knowledge_area_id" name="knowledge_area_id" required>
            <flux:select.option value="">Selecciona un área</flux:select.option>
            @foreach ($knowledgeAreas as $knowledgeArea)
                <flux:select.option value="{{ $knowledgeArea->id }}" :selected="(string) old('knowledge_area_id', $academicProgram?->knowledge_area_id) === (string) $knowledgeArea->id">{{ $knowledgeArea->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="knowledge_area_id" />
    </flux:field>
    <flux:field>
        <flux:label for="description">Descripción</flux:label>
        <flux:input id="description" name="description" :value="old('description', $academicProgram?->description)" maxlength="255" />
        <flux:error name="description" />
    </flux:field>
    <flux:field>
        <flux:label for="resolution">Resolución</flux:label>
        <flux:input id="resolution" name="resolution" :value="old('resolution', $academicProgram?->resolution)" maxlength="255" />
        <flux:error name="resolution" />
    </flux:field>
    <flux:field variant="inline">
        <input type="hidden" name="is_active" value="0">
        <flux:checkbox id="is_active" name="is_active" value="1" :checked="(bool) old('is_active', $academicProgram?->is_active ?? true)" />
        <flux:label for="is_active">Programa activo</flux:label>
        <flux:error name="is_active" />
    </flux:field>
</div>

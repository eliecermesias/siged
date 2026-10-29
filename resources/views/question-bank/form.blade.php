<x-layouts::app :title="($record ? 'Editar ' : 'Agregar ').Str::lower($resourceTitle)">
    <section @class([
        'w-full' => $indexRoute !== 'questions.index',
        'flex w-full flex-col gap-8 px-5 py-8 sm:px-8 sm:py-10 lg:px-10' => $indexRoute === 'questions.index',
    ])>
        @if ($indexRoute !== 'questions.index')
            @include('partials.settings-heading')
        @endif

        <div class="flex items-start max-md:flex-col">
            @if ($indexRoute !== 'questions.index')
                <aside class="me-10 w-full pb-4 md:w-[220px]">
                    @include('partials.settings.question-bank-navigation')
                </aside>
            @endif

            <div class="flex-1 self-stretch max-md:pt-6">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item :href="route('dashboard', ['current_team' => request()->route('current_team')])">Inicio</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>Banco de preguntas</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item :href="route($indexRoute, ['current_team' => request()->route('current_team')])">{{ Str::title($resourceTitle) }}</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>{{ $record ? 'Editar' : 'Agregar' }}</flux:breadcrumbs.item>
                </flux:breadcrumbs>

                <div class="mt-8 overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-unicuces-blue-300/25 dark:bg-unicuces-blue-dark/60">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8 dark:border-unicuces-blue-300/20">
                <flux:heading size="lg">{{ $record ? 'Editar' : 'Agregar' }} {{ Str::lower($resourceTitle) }}</flux:heading>
                <flux:subheading>Completa la información del registro.</flux:subheading>
            </div>
            <form
                method="POST"
                action="{{ $record ? route($updateRoute, ['current_team' => request()->route('current_team'), $routeParameter => $record]) : route($storeRoute, ['current_team' => request()->route('current_team')]) }}"
                class="space-y-8 px-6 py-7 sm:px-8"
            >
                @csrf
                @if ($record)
                    @method('PUT')
                @endif

                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($fields as $field)
                        @php
                            $fieldValue = old($field['name'], data_get($record, $field['name'], $field['default'] ?? ''));
                            $fieldType = $field['type'] ?? 'text';
                        @endphp
                        @if ($fieldType === 'checkbox')
                            <flux:field variant="inline">
                                <input type="hidden" name="{{ $field['name'] }}" value="0">
                                <flux:checkbox
                                    id="{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    value="1"
                                    :checked="(bool) $fieldValue"
                                />
                                <flux:label for="{{ $field['name'] }}">{{ $field['label'] }}</flux:label>
                                <flux:error name="{{ $field['name'] }}" />
                            </flux:field>
                        @else
                            <flux:field class="{{ $fieldType === 'textarea' ? 'md:col-span-2' : '' }}">
                                <flux:label for="{{ $field['name'] }}">{{ $field['label'] }}</flux:label>
                                @if ($fieldType === 'select')
                                    <flux:select id="{{ $field['name'] }}" name="{{ $field['name'] }}" :required="$field['required'] ?? false">
                                        <flux:select.option value="">Selecciona una opción</flux:select.option>
                                        @foreach ($field['options'] as $value => $label)
                                            <flux:select.option value="{{ $value }}" :selected="(string) $fieldValue === (string) $value">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @elseif ($fieldType === 'textarea')
                                    <textarea
                                        id="{{ $field['name'] }}"
                                        name="{{ $field['name'] }}"
                                        rows="4"
                                        @required($field['required'] ?? false)
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-unicuces-blue focus:outline-none focus:ring-2 focus:ring-unicuces-blue/20 dark:border-unicuces-blue-300/30 dark:bg-unicuces-blue-dark dark:text-white"
                                    >{{ $fieldValue }}</textarea>
                                @else
                                    <flux:input
                                        id="{{ $field['name'] }}"
                                        name="{{ $field['name'] }}"
                                        :type="$fieldType"
                                        :value="$fieldType === 'date' && $fieldValue instanceof \Illuminate\Support\Carbon ? $fieldValue->format('Y-m-d') : $fieldValue"
                                        :required="$field['required'] ?? false"
                                        :maxlength="$fieldType === 'text' ? ($field['max'] ?? null) : null"
                                        :min="$field['min'] ?? null"
                                        :max="$field['max'] ?? null"
                                        autocomplete="off"
                                    />
                                @endif
                                <flux:error name="{{ $field['name'] }}" />
                            </flux:field>
                        @endif
                    @endforeach
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-unicuces-blue-300/20">
                    <flux:button variant="filled" :href="route($indexRoute, ['current_team' => request()->route('current_team')])">Cancelar</flux:button>
                    <flux:button variant="primary" type="submit" icon="check">{{ $record ? 'Guardar cambios' : 'Guardar' }}</flux:button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts::app>

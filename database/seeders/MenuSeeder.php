<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        $rootMenuIds = [];

        foreach ($this->menus() as $menu) {
            $rootMenuIds[] = $this->upsertMenu($menu)->id;
        }

        $this->deleteMissingMenus(null, $rootMenuIds);
    }

    /**
     * @param  array{name: string, icon: string, url: string, slug: string, current: string, priority: int, roles: array<int, string>, children?: array<int, array{name: string, icon: string, url: string, slug: string, current: string, priority: int, roles: array<int, string>}>}  $menu
     */
    private function upsertMenu(array $menu, ?Menu $parent = null): Menu
    {
        $children = $menu['children'] ?? [];
        unset($menu['children']);

        $menu['menu_id'] = $parent?->id;

        $storedMenu = Menu::query()->firstOrNew([
            'menu_id' => $menu['menu_id'],
            'slug' => $menu['slug'],
        ]);
        $storedMenu->fill($menu);
        $storedMenu->save();

        $childMenuIds = [];

        foreach ($children as $child) {
            $childMenuIds[] = $this->upsertMenu($child, $storedMenu)->id;
        }

        $this->deleteMissingMenus($storedMenu, $childMenuIds);

        return $storedMenu;
    }

    /**
     * @param  array<int, int>  $keptMenuIds
     */
    private function deleteMissingMenus(?Menu $parent, array $keptMenuIds): void
    {
        $menus = Menu::query()
            ->when($parent, fn ($query) => $query->where('menu_id', $parent->id), fn ($query) => $query->whereNull('menu_id'))
            ->get();

        foreach ($menus as $menu) {
            if (! in_array($menu->id, $keptMenuIds, true)) {
                $this->deleteMenuBranch($menu);
            }
        }
    }

    private function deleteMenuBranch(Menu $menu): void
    {
        foreach ($menu->children as $child) {
            $this->deleteMenuBranch($child);
        }

        $menu->delete();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function menus(): array
    {
        $all = ['administrador', 'coordinador', 'docente', 'estudiante'];
        $admin = ['administrador'];
        $adminCoordinator = ['administrador', 'coordinador'];

        return [
            ['name' => 'Dashboard', 'icon' => 'home', 'url' => 'route:dashboard', 'slug' => 'dashboard', 'current' => 'dashboard', 'priority' => 1, 'roles' => $all],
            ['name' => 'Evaluación Docente', 'icon' => 'clipboard-document-check', 'url' => '#', 'slug' => 'evaluacion-docente', 'current' => '', 'priority' => 2, 'roles' => ['estudiante', 'coordinador'], 'children' => [
                ['name' => 'Evaluaciones Pendientes', 'icon' => 'clock', 'url' => '/evaluaciones/pendientes', 'slug' => 'evaluaciones-pendientes', 'current' => '', 'priority' => 1, 'roles' => ['estudiante']],
                ['name' => 'Evaluar Docente', 'icon' => 'user-circle', 'url' => '/evaluaciones/docentes', 'slug' => 'evaluar-docente', 'current' => '', 'priority' => 2, 'roles' => ['coordinador']],
            ]],
            ['name' => 'Autoevaluación', 'icon' => 'pencil-square', 'url' => '#', 'slug' => 'autoevaluacion', 'current' => '', 'priority' => 3, 'roles' => ['docente'], 'children' => [
                ['name' => 'Realizar Autoevaluación', 'icon' => 'pencil-square', 'url' => '/autoevaluacion', 'slug' => 'realizar-autoevaluacion', 'current' => '', 'priority' => 1, 'roles' => ['docente']],
            ]],
            ['name' => 'Resultados', 'icon' => 'presentation-chart-bar', 'url' => '#', 'slug' => 'resultados', 'current' => '', 'priority' => 4, 'roles' => ['administrador', 'coordinador', 'docente'], 'children' => [
                ['name' => 'Mis Resultados', 'icon' => 'chart-bar', 'url' => '/mis-resultados', 'slug' => 'mis-resultados', 'current' => '', 'priority' => 1, 'roles' => ['docente']],
                ['name' => 'Resultados por Docente', 'icon' => 'academic-cap', 'url' => '/resultados/docentes', 'slug' => 'resultados-docentes', 'current' => '', 'priority' => 2, 'roles' => $adminCoordinator],
            ]],
            ['name' => 'Reportes', 'icon' => 'document-chart-bar', 'url' => '#', 'slug' => 'reportes', 'current' => '', 'priority' => 5, 'roles' => $adminCoordinator, 'children' => [
                ['name' => 'Exportar PDF', 'icon' => 'document-arrow-down', 'url' => '/reportes/pdf', 'slug' => 'reportes-pdf', 'current' => '', 'priority' => 1, 'roles' => $adminCoordinator],
                ['name' => 'Exportar Excel', 'icon' => 'table-cells', 'url' => '/reportes/excel', 'slug' => 'reportes-excel', 'current' => '', 'priority' => 2, 'roles' => $adminCoordinator],
            ]],
            ['name' => 'Administración', 'icon' => 'cog-6-tooth', 'url' => '#', 'slug' => 'administracion', 'current' => '', 'priority' => 6, 'roles' => $admin, 'children' => [
                ['name' => 'Personas', 'icon' => 'user-circle', 'url' => 'route:people.index', 'slug' => 'personas', 'current' => 'people.*', 'priority' => 1, 'roles' => $admin],
                ['name' => 'Docentes', 'icon' => 'academic-cap', 'url' => 'route:teachers.index', 'slug' => 'docentes', 'current' => 'teachers.*', 'priority' => 2, 'roles' => $admin],
                ['name' => 'Estudiantes', 'icon' => 'academic-cap', 'url' => 'route:students.index', 'slug' => 'estudiantes', 'current' => 'students.*', 'priority' => 3, 'roles' => $admin],
                ['name' => 'Coordinadores', 'icon' => 'user-group', 'url' => 'route:coordinators.index', 'slug' => 'coordinadores', 'current' => 'coordinators.*', 'priority' => 4, 'roles' => $admin],
                ['name' => 'Programas académicos', 'icon' => 'academic-cap', 'url' => 'route:academic-programs.index', 'slug' => 'programas-academicos', 'current' => 'academic-programs.*', 'priority' => 5, 'roles' => $admin],
                ['name' => 'Niveles de formación', 'icon' => 'bars-3-bottom-left', 'url' => 'route:education-levels.index', 'slug' => 'niveles-formacion', 'current' => 'education-levels.*', 'priority' => 6, 'roles' => $admin],
                ['name' => 'Áreas del conocimiento', 'icon' => 'rectangle-group', 'url' => 'route:knowledge-areas.index', 'slug' => 'areas-conocimiento', 'current' => 'knowledge-areas.*', 'priority' => 7, 'roles' => $admin],
                ['name' => 'Periodos', 'icon' => 'calendar-days', 'url' => 'route:periods.index', 'slug' => 'periodos', 'current' => 'periods.*', 'priority' => 8, 'roles' => $admin],
                ['name' => 'Cursos', 'icon' => 'book-open', 'url' => '#', 'slug' => 'cursos', 'current' => '', 'priority' => 9, 'roles' => $admin],
            ]],
            ['name' => 'Preguntas', 'icon' => 'question-mark-circle', 'url' => 'route:questions.index', 'slug' => 'preguntas', 'current' => 'questions.*', 'priority' => 7, 'roles' => $admin],
        ];
    }
}

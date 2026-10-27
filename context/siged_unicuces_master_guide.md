# 📘 SIGED-UniCUCES: Guía Maestra del Proyecto & System Prompt para IA

> **Sistema de Información para la Gestión de la Evaluación Docente (SIGED-UniCUCES)**  
> **Institución:** Corporación Centro Superior (UniCUCES)  
> **Stack Técnico:** Laravel (PHP 8.2+) | Livewire / Inertia.js | Tailwind CSS | MySQL/PostgreSQL | Redis Queues  
> **Autenticación:** Microsoft Entra ID (OAuth 2.0 / OpenID Connect)  

---

## 📋 ÍNDICE
1. [Visión y Objetivos del Sistema](#1-visión-y-objetivos-del-sistema)
2. [Arquitectura de Base de Datos y Modelos](#2-arquitectura-de-base-de-datos-y-modelos)
3. [Flujos de Autenticación y Anonimización (Garantía de Secreto Estadístico)](#3-flujos-de-autenticación-y-anonimización)
4. [Ciclo de Vida de Evaluaciones (State Machine)](#4-ciclo-de-vida-de-evaluaciones-state-machine)
5. [Guía de Diseño e Imagen Corporativa (Design System)](#5-guía-de-diseño-e-imagen-corporativa)
6. [Dashboard Analítico y Reportes PDF](#6-dashboard-analítico-y-reportes-pdf)
7. [Instrucciones Directas para la IA (System Prompt & Guidelines)](#7-instrucciones-directas-para-la-ia)

---

## 1. VISIÓN Y OBJETIVOS DEL SISTEMA

**SIGED-UniCUCES** es la plataforma institucional concebida para automatizar, anonimizar y consolidar la evaluación del desempeño docente en UniCUCES. 

### Objetivos Clave:
* **Estudiantes:** Evaluar de forma rápida, intuitiva y 100% anónima sus asignaturas matriculadas por período y semestre académico.
* **Coordinadores / Decanos:** Analizar métricas consolidadas, semáforos de desempeño, tendencias históricas y reportes cuantitativos/cualitativos por facultad, programa, semestre o docente.
* **Docentes:** Recibir retroalimentación constructiva mediante reportes en PDF generados automáticamente una vez que el período de evaluación ha finalizado oficialmente.
* **Seguridad:** Autenticación corporativa obligatoria a través de cuentas Microsoft `@unicuces.edu.co` sin almacenamiento local de contraseñas.

---

## 2. ARQUITECTURA DE BASE DE DATOS Y MODELOS

### Esquema Relacional de Tablas (ERD Concept)

```text
[users] (microsoft_id, email, role: student|coordinator|teacher|admin)
  │
  ├───> [students] (user_id, code, program_id)
  │        │
  │        └───> [enrollments] ───> [courses / sections] ───> [teachers]
  │                     │
  │                     └───> [evaluation_statuses] (has_evaluated: boolean)
  │
  └───> [academic_periods] (code: "2026-1", status: draft|active|closed|published)
           │
           └───> [evaluations] (period_id, course_id, teacher_id)  <-- TOTALMENTE DESVINCULADA DE STUDENT_ID
                    │
                    └───> [evaluation_responses] (question_id, score, comment)
```

### Definición Técnica de Modelos Principales

#### 1. `AcademicPeriod`
* `id`, `code` (ej: "2026-1"), `name`, `start_date`, `end_date`, `status` (`draft`, `active`, `closed`, `published`).

#### 2. `Program` / `Faculty`
* `id`, `name`, `code` (ej: "ING-SISTEMAS"), `department`.

#### 3. `Course` / `Subject`
* `id`, `program_id`, `semester` (1 a 10), `code`, `name`.

#### 4. `Section` / `CourseAssignment` (Asignación Académica)
* `id`, `academic_period_id`, `course_id`, `teacher_id` (relación a `User` con rol docente), `group_code`.

#### 5. `Enrollment` (Matrícula)
* `id`, `student_id`, `section_id`, `academic_period_id`.

#### 6. `EvaluationQuestion`
* `id`, `dimension` (`pedagogy`, `mastery`, `punctuality`, `relationship`, `fairness`), `question_text`, `type` (`scale_1_5`, `open_text`), `is_active`.

#### 7. `EvaluationCompletionStatus` (Control de Diligenciamiento)
* `id`, `student_id`, `section_id`, `academic_period_id`, `completed_at` (Garantiza que el estudiante no repita voto pero **no guarda la respuesta aquí**).

#### 8. `EvaluationResponse` (Respuestas Anónimas)
* `id`, `academic_period_id`, `section_id`, `teacher_id`, `course_id`, `question_id`, `rating` (1-5), `comment` (text, nullable), `created_at`.

---

## 3. FLUJOS DE AUTENTICACIÓN Y ANONIMIZACIÓN

### A. Autenticación Microsoft Entra ID (OAuth 2.0)
1. El usuario hace clic en **"Iniciar sesión con cuenta institucional"**.
2. Laravel Socialite redirige a `login.microsoftonline.com` con el Tenant ID de UniCUCES.
3. Se verifica que el correo termine en `@unicuces.edu.co`.
4. El sistema asigna los roles según el directorio (`student`, `teacher`, `coordinator`, `admin`).

### B. Módulo de Anonimización Absoluta (Secreto Estadístico)
Para evitar represalias o desconfianza en la comunidad estudiantil:
1. Al enviar la evaluación de un curso, se ejecuta un **DB Transaction**:
   * **Registro A:** Se crea una entrada en `evaluation_completion_statuses` vinculando al `student_id` con la `section_id` con `has_evaluated = true`.
   * **Registro B:** Se inserta el set de respuestas en `evaluation_responses` registrando únicamente `academic_period_id`, `section_id`, `teacher_id` y `question_id`.
2. **Resultado:** Es técnicamente imposible rastrear qué fila de la tabla `evaluation_responses` pertenece a qué `student_id`.

---

## 4. CICLO DE VIDA DE EVALUACIONES (STATE MACHINE)

```text
  [ DRAFT ] ────────> [ ACTIVE ] ────────> [ CLOSED ] ────────> [ PUBLISHED ]
Configuración      Evaluación Abierta     Cálculo & Jobs     Reportes Enviados
de Fechas &          para Estudiantes    Generación de PDF   y Visibles a Docentes
Cuestionarios                             y Semáforo
```

1. **DRAFT (Borrador):** Coordinadores configuran el calendario y asignaciones docentes.
2. **ACTIVE (Activa):**
   * Estudiantes ven banners y tarjetas de sus asignaturas pendientes en el portal.
   * *Estrategia optativa:* Bloqueo de consulta de calificaciones hasta completar encuestas.
3. **CLOSED (Cerrada):**
   * Se deshabilita el acceso a responder.
   * Se ejecuta un Scheduled Task / Queue Job de Laravel para consolidar promedios por dimensión, promedios generales y matriz semáfórica.
4. **PUBLISHED (Publicada):**
   * Se disparan los correos electrónicos masivos a los docentes con sus reportes PDF adjuntos.
   * Los coordinadores obtienen acceso a los dashboards comparativos.

---

## 5. GUÍA DE DISEÑO E IMAGEN CORPORATIVA (DESIGN SYSTEM)

### Configuración Tailwind CSS (`tailwind.config.js`)
```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        cuces: {
          blue: '#003366',        // Azul Primario Institucional
          'blue-dark': '#002244',   // Hover / Enfatizados
          gold: '#FFCC00',        // Dorado / Acento Institucional
          'gold-dark': '#E6B800',  // Hover Acento
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
```

### Paleta de Colores
* **Azul CUCES (Primario):** `#003366` (`bg-cuces-blue`, `text-cuces-blue`)
* **Dorado CUCES (Secundario/Acento):** `#FFCC00` (`bg-cuces-gold`, `text-cuces-blue`)
* **Fondo Neutro:** `#F8FAFC` (`bg-slate-50`)
* **Tarjetas:** `#FFFFFF` con borde `border-slate-200` y sombras suaves (`shadow-sm`).

### Semáforo de Desempeño
* **Sobresaliente (4.5 - 5.0):** Verde (`bg-emerald-500`, `text-emerald-700`)
* **Satisfactorio (3.8 - 4.4):** Azul (`bg-sky-500`, `text-sky-700`)
* **En Riesgo / Aceptable (3.0 - 3.7):** Amarillo (`bg-amber-500`, `text-amber-700`)
* **Crítico (< 3.0):** Rojo (`bg-red-500`, `text-red-700`)

### Identidad Gráfica y Assets
* Logo Header: `public/images/brand/logo-unicuces.png`
* Isotipo: `public/images/brand/isotipo-cuces.png`
* Logo Blanco: `public/images/brand/logo-unicuces-white.png`
* Microsoft Icon: `public/images/brand/ms-symbol.svg`

---

## 6. DASHBOARD ANALÍTICO Y REPORTES PDF

### Dashboard del Coordinador
* **KPIs Generales:** Tasa Global de Participación (%), Promedio Institucional, Total Docentes Evaluados, Alertas Críticas (Docentes < 3.0).
* **Filtros Dinámicos:** Período Académico, Facultad, Programa, Semestre, Asignatura y Docente.
* **Componentes Gráficos:**
  * Diagrama de Araña / Radar para evaluar las 5 dimensiones pedagógicas.
  * Gráfico de Barras Comparativo (Promedio del Docente vs. Promedio del Departamento).
  * Nube de Palabras / Listado de Comentarios cualitativos moderados.

### Reporte PDF Individual para Docentes
* **Librería:** `barryvdh/laravel-dompdf` o `spatie/laravel-browsershot`.
* **Estructura del PDF:**
  1. **Header:** Logo de UniCUCES a la izquierda, título *"SIGED-UniCUCES - Reporte Individual de Evaluación Docente"* y metadatos a la derecha.
  2. **Resumen Ejecutivo:** Calificación promedio ponderada y posición relativa en el programa.
  3. **Tabla de Desglose:** Puntaje obtenido por cada pregunta y dimensión.
  4. **Sección Cualitativa:** Comentarios anonimizados de los estudiantes.
  5. **Footer Legal:** Mensaje de confidencialidad y secreto estadístico institucional.

---

## 7. INSTRUCCIONES DIRECTAS PARA LA IA (SYSTEM PROMPT & GUIDELINES)

Copia este bloque en las reglas de tu editor (ej. `.cursorrules`, `.github/copilot-instructions.md` o la ventana de contexto de tu LLM):

```text
Eres un Arquitecto de Software Senior y Diseñador UX especializado en Laravel, Livewire, Tailwind CSS y sistemas académicos universitarios.

Al generar o modificar código para este proyecto, debes cumplir estrictamente con las siguientes reglas:

1. NOMBRE DEL SISTEMA: El proyecto se llama exclusivamente "SIGED-UniCUCES" (Sistema de Información para la Gestión de la Evaluación Docente).
2. COLORES E IMAGEN: Utiliza siempre los colores institucionales configurados en Tailwind: `cuces-blue` (#003366) como color principal y `cuces-gold` (#FFCC00) para acentos y elementos destacados. Las interfaces deben ser limpias, modernas y corporativas sobre fondo `slate-50`.
3. ARQUITECTURA DE DATOS Y ANONIMATO: Mantén la separación estricta entre la tabla de estado de cumplimiento del estudiante (`evaluation_completion_statuses`) y la tabla de respuestas (`evaluation_responses`). Nunca sugieras ni agregues un `student_id` en la tabla de respuestas de evaluaciones.
4. ROLES Y PERMISOS: Asegura el uso de Policies de Laravel y Middleware para restringir los accesos según los 4 roles: Estudiante, Docente, Coordinador y Administrador.
5. SEGURIDAD: La autenticación se realiza únicamente a través de Microsoft Entra ID con correo institucional `@unicuces.edu.co`. No crees formularios de registro de contraseñas locales a menos que se especifique para pruebas/seeders.
6. CÓDIGO LIMPIO: Sigue los estándares PSR-12, usa Type Hinting estricto en métodos PHP y asegura que las vistas Blade o Livewire tengan manejo adecuado de estados de carga (loading states) y responsividad móvil.
```
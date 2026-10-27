# 🎨 Guía de Diseño e Imagen Corporativa: SIGED-UniCUCES

Este documento define la guía de estilo visual, paleta de colores, tipografía e identidad de marca para el sistema **SIGED-UniCUCES** (*Sistema de Información para la Gestión de la Evaluación Docente* - Corporación Centro Superior).

Toda la generación de código Frontend (Blade, Livewire, Inertia/Vue/React, Tailwind CSS) debe alinearse estrictamente a estas especificaciones corporativas.

---

## 1. Identidad del Proyecto
* **Nombre del Sistema:** SIGED-UniCUCES
* **Descripción:** Sistema de Información para la Gestión de la Evaluación Docente de UniCUCES
* **Institución:** Corporación Centro Superior (UniCUCES)
* **Autenticación:** Microsoft Entra ID (Correo Institucional `@unicuces.edu.co`)

---

## 2. Paleta de Colores Institucionales

Configuración oficial para **Tailwind CSS**:

### Colores de Marca
| Rol | Nombre | Código HEX | Clase Tailwind Sugerida |
| :--- | :--- | :--- | :--- |
| **Primario** | Azul CUCES | `#003366` | `cuces-blue` / `bg-[#003366]` |
| **Primario Oscuro** | Azul Noche | `#002244` | `cuces-blue-dark` |
| **Secundario / Acento** | Amarillo / Dorado CUCES | `#FFCC00` | `cuces-gold` / `bg-[#FFCC00]` |
| **Secundario Oscuro** | Dorado Intenso | `#E6B800` | `cuces-gold-dark` |
| **Fondo Base** | Gris Slate Claro | `#F8FAFC` | `bg-slate-50` |
| **Texto Principal** | Slate Oscuro | `#1E293B` | `text-slate-800` |
| **Texto Secundario** | Slate Medio | `#64748B` | `text-slate-500` |

### Semáforo de Desempeño (Dashboard Coordinadores)
* **Excelente (4.5 – 5.0):** `#10B981` (`emerald-500`)
* **Bueno (3.8 – 4.4):** `#0284C7` (`sky-600`)
* **Aceptable / Alerta Amarilla (3.0 – 3.7):** `#F59E0B` (`amber-500`)
* **Crítico / Alerta Roja (< 3.0):** `#EF4444` (`red-500`)

---

## 3. Tipografía y Estilos

* **Fuente:** `Inter`, `Roboto` o `system-ui` (limpia, institucional, alta legibilidad).
* **Encabezados (`h1`, `h2`, `h3`):** Semibold/Bold, color **Azul CUCES** (`#003366`).
* **Métricas y Puntuaciones:** Bold con tipografía numérica destacada o tabular figures (`font-mono` o `font-semibold`).

---

## 4. Recursos de Marca y Logos

Los recursos gráficos se ubican en `public/images/brand/`:

* **Logo Principal (Header / Navbar):** `/images/brand/logo-unicuces.png`
* **Isotipo / Favicon:** `/images/brand/isotipo-cuces.png`
* **Logo Versión Blanca (Footer / Fondos Oscuros):** `/images/brand/logo-unicuces-white.png`
* **Icono Microsoft Login:** `/images/brand/ms-symbol.svg`

---

## 5. Patrones de Componentes UI

### Navbar / Header Principal
* Fondo **Azul CUCES** (`#003366`) o Blanco con borde inferior sutil (`border-slate-200`).
* Muestra el logo de UniCUCES a la izquierda, el texto **SIGED-UniCUCES** de subtítulo, y el perfil del usuario autenticado a la derecha.

### Pantalla de Autenticación (Login)
* Tarjeta centrada en pantalla con el logo de UniCUCES.
* Título: **SIGED-UniCUCES**.
* Subtítulo: *Sistema de Información para la Gestión de la Evaluación Docente*.
* Botón único de ingreso: **"Iniciar sesión con cuenta institucional Microsoft"** usando el icono de Microsoft y el dominio `@unicuces.edu.co`.

### Tarjetas de Evaluación (Vista Estudiante)
* Borde lateral izquierdo de acento en Azul CUCES: `border-l-4 border-[#003366]`.
* Muestra: Nombre de la asignatura, Semestre, Nombre del docente y Badge de estado (*Pendiente* en amarillo / *Evaluado* en verde).

### Dashboard Analítico (Vista Coordinador)
* Tarjetas KPI blancas (`bg-white rounded-xl shadow-sm border border-slate-200`).
* Filtros superiores por: **Período Académico**, **Semestre**, **Programa / Cátedra**, **Curso** y **Docente**.
* Gráficos dinámicos (Chart.js / ApexCharts) con los colores de la marca (`#003366`, `#FFCC00`, `#0284C7`).

---

## 6. Reporte PDF de Evaluación Docente

Al generar el informe en PDF con DomPDF o Browsershot al cerrar el período:

* **Encabezado:** Logo institucional de UniCUCES a la izquierda, título *"SIGED-UniCUCES - Reporte Consolidado de Evaluación Docente"*.
* **Encabezado de Tablas:** Fondo `#003366` con texto blanco en negrita.
* **Filas:** Alternadas entre `#FFFFFF` y `#F8FAFC`.
* **Pie de página:** *"Documento confidencial generado por el sistema SIGED-UniCUCES. Protegido por el principio de secreto estadístico."*

---

## 7. Directiva para la Inteligencia Artificial (System Prompt)

> "Al escribir o editar código Blade, Tailwind CSS, Livewire, React o Vue para este proyecto, asume el nombre oficial **SIGED-UniCUCES**. Utiliza estrictamente la paleta de colores institucional (`#003366` Azul CUCES y `#FFCC00` Dorado CUCES). Mantén una estética limpia, corporativa, moderna y académica."
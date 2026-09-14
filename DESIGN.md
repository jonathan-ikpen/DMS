# Design System Guidelines (Notion / Linear / Vercel Inspired)

This document outlines the core design principles, typography, CSS architecture, components, and strict constraints for replicating the minimalist, premium UI used in this project. You can apply this guide to any other project to achieve the exact same aesthetic.

## Core Philosophy
The design language is **minimalist, premium, and handcrafted**. It focuses on editorial-style layouts, flat surfaces, stark contrasts, and absolute structural clarity without relying on visual noise.

### The "Don'ts" (Strict Constraints)
- **NO Shadows**: Flat surfaces only. Do not use `box-shadow` (except specifically for absolutely positioned utility dropdowns).
- **NO Gradients**: Solid colors only for backgrounds, text, and buttons.
- **NO Glassmorphism**: No `backdrop-filter`, blur effects, or translucent overlays.
- **NO Heavy Borders**: All borders must be exactly `1px solid var(--line)`. Never use thick or dark borders for layout boundaries.
- **NO CSS Frameworks**: No Bootstrap or Tailwind utility soup. Custom, semantic, lightweight CSS only.
- **NO SaaS Clichés**: Avoid overly commercial "startup" visuals. Keep it academic, clean, and utilitarian if it's an internal tool.

### The "Dos" (Best Practices)
- **DO use 1px borders**: Define structure entirely through 1px solid borders (`var(--line)`).
- **DO use ample whitespace (No "Kissing" Components)**: Ensure generous gaps (`gap: 32px;`) and paddings between layout areas (e.g. sidebar and navbar). Components should never feel like they are touching.
- **DO use CSS Variables**: Centralize all theming (Light/Dark mode) in CSS custom properties at the `:root` level.
- **DO use CSS Grid**: Use grid for structured, responsive layouts (e.g. dashboards) over complex flex chains.
- **DO use Pure White Backgrounds (Light Mode)**: The main background and panels must use pure `#ffffff`. Do not use "weird grey" backgrounds for dashboard canvas areas.
- **DO use Iconography**: Utilize `.icon-box` components (`36px` bounding box, `var(--soft)` background, 1px border) to visually anchor text and actions instead of relying purely on text.

---

## Theming & Color Palette (CSS Variables)

The entire application relies on a dual-theme variable system (Light/Dark). The theme is toggled by setting `data-theme="dark"` on a parent element (e.g., `<html>` or `<body>`).

```css
/* Light Mode (Default) */
:root {
    --bg: #ffffff;         /* Main application background */
    --panel: #ffffff;      /* Card/Panel background */
    --text: #211c19;       /* Primary text color */
    --muted: #666666;      /* Secondary/Helper text color */
    --soft: #f8f8f8;       /* Subtle background for active states or slight contrast */
    --line: #e8e8e8;       /* Universal 1px border color */
    --ink: #211c19;        /* High contrast elements (black in light mode) */
    --accent: #E85D36;     /* Primary brand color (e.g., Burnt Orange/Red) */
    --warning: #a16207;
    --danger: #b91c1c;
    --radius: 8px;         /* Standard border-radius for cards/buttons */
    --pill-radius: 9999px; /* Pill border-radius for rounded elements/CTAs */
}

/* Dark Mode */
[data-theme="dark"] {
    --bg: #111111;         
    --panel: #181818;      
    --text: #f4f4f2;       
    --muted: #aaa69f;      
    --soft: #242424;       
    --line: #2b2b2b;       
    --ink: #f8f8f6;        
    --accent: #E85D36;     
    color-scheme: dark;
}
```

### Advanced Theming Tricks
- **Dynamic Assets**: Control visibility of light/dark mockup images using `.mockup-light` and `.mockup-dark` toggled via `display: none/block` tied to the `[data-theme="dark"]` parent.
- **Dynamic SVGs**: Use CSS `-webkit-mask` to render complex inline SVGs (like the theme toggle moon/sun icon) that inherit the current text color natively.

---

## Typography

The design uses a stark contrast between bold, uppercase display headers and clean, highly readable sans-serif body text with varying weights for hierarchy.

- **Base Body Text**: `font-family: 'Inter', -apple-system, sans-serif; font-weight: 400;`
- **Subtext, Intros, and Labels**: `font-weight: 300;` (Crucial for achieving the premium "light" editorial aesthetic on long paragraphs, section intros, and form labels).
- **Headings (H1-H6)**: `font-family: 'Anton', sans-serif; font-weight: 400; text-transform: uppercase; letter-spacing: 0.02em;`
- **Strong / Emphasis / Table Headers**: `font-weight: 600;`
- **Eyebrows / Micro-labels**: `font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--accent);`

---

## Forms & Inputs

Inputs are spacious, rounded, and rely on borders for definition, eschewing native browser defaults.

### Styling Rules
- **Labels**: Subdued, thin, and muted.
  ```css
  label {
      display: grid;
      gap: 10px;
      color: var(--muted);
      font-size: 14px;
      font-weight: 300; /* Specifically light weight */
  }
  ```
- **Text Inputs, Selects, Textareas**: 
  - Standard radius MUST be fully rounded `var(--pill-radius)` for all form fields.
  - Border: `1px solid var(--line)`.
  - Padding: generous padding like `14px 24px`.
  - Focus State: `outline: none; border-color: var(--accent);` (No thick focus rings).
- **Select Dropdowns**: Strip native styling (`appearance: none`) and inject a custom SVG chevron via `background-image` that adapts its stroke color based on light/dark mode.
- **File Uploads**: `input[type="file"]` uses a dashed border (`1px dashed var(--line)`) and customizes the `::file-selector-button` to match standard buttons.
- **Checkboxes & Radios**: Force dimensions (`width: 20px; height: 20px;`), strip native backgrounds, and apply `accent-color: #8ab4f8` for standard high-contrast legibility.

---

## Buttons

Buttons are flat, highly structured, and lack dimensional effects. The primary design language dictates that **all main interactive buttons must be fully rounded (pill-shaped)** to match form fields.

- **All Action Buttons & CTAs (`.cta-button`, form submits, dashboard actions)**: 
  - `border-radius: var(--pill-radius); padding: 14px 28px; font-weight: 600;`
  - Primary actions use `background: var(--accent); color: #fff; border: none;`
- **Dark Buttons (`.button-dark`)**: 
  - Used for secondary high-contrast actions (like "View Document").
  - Must also explicitly use `border-radius: var(--pill-radius);`.
  - Uses `var(--ink)` for background and `var(--bg)` for text.
- **Ghost/Light Button (`.button-ghost`)**: 
  - Transparent background, `var(--text)` color, transparent border.

*Note: The standard 8px `var(--radius)` is strictly reserved for structural containers (Panels, Cards, Modals) and should NOT be used for primary action buttons.*

---

## Layout & Components

### Application Layouts (Dashboards)
The backend interface relies on strict CSS Grid templating:
- **Standard Dashboard (`.app-layout`)**: `grid-template-columns: 240px minmax(0, 1fr); gap: 32px;`
- **Dense/3-Pane Dashboard (`.app-layout-3pane`)**: `grid-template-columns: 240px minmax(320px, 1fr) minmax(320px, 1.2fr);` (Ideal for list/detail views).

### Panels & Cards
Panels define content areas in the app layout.
- `background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius);`
- Always flush, flat, and strictly bound by 1px borders.

### Tables & Lists
- **Tables**: `width: 100%; border-collapse: collapse; min-width: 720px;` (wrapped in a `.table-panel` with `overflow-x: auto` for mobile).
- **Table Cells**: `padding: 13px; border-bottom: 1px solid var(--line);`
- **Table Headers**: `font-weight: 600; color: var(--muted); font-size: 13px;`

### Boards (No Horizontal Scrolling)
- **Timetable/Kanban Boards**: Absolutely no horizontal scrolling. Use `display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));` to auto-wrap cards cleanly on all viewports.

### Footer
- **Footer Links**: Links must strictly maintain a horizontal layout (`flex-direction: row; align-items: center; gap: 16px;`) even on mobile devices, avoiding vertical stacking.

### Badges & Statuses
- **Pill Labels (Marketing)**: `background: var(--soft); border-radius: var(--pill-radius); font-size: 12px; font-weight: 600;`
- **Status Indicators (Dashboard)**: `border: 1px solid var(--line); border-radius: 999px; padding: 4px 8px; font-size: 12px;`

### Flash Notifications
- Fixed positioned at `top: 24px; right: 24px; z-index: 1000;`.
- Floating card using `var(--panel)` background and `1px solid var(--line)` border. Variations change the border color (`.flash-error` uses `--danger`, `.flash-success` uses `--accent`).

---

## Summary Checklist for New Projects

1. [ ] Setup CSS variables at `:root` identical to the above schema.
2. [ ] Import `Inter` and `Anton` fonts.
3. [ ] Apply a global `box-sizing: border-box`.
4. [ ] Set `body` to use `var(--bg)`, `var(--text)`, and `font-weight: 400`. Ensure Light Mode background is purely `#ffffff`.
5. [ ] **Set subtext, hero intros, and labels to `font-weight: 300`**.
6. [ ] Define all structure rigorously with `1px solid var(--line)` and ensure components are spaced heavily so they never visually "kiss" or touch.
7. [ ] Eradicate all `box-shadow` and `linear-gradient` declarations from your codebase.
8. [ ] Build dashboards directly with CSS Grid (`grid-template-columns: 240px 1fr`).
9. [ ] Ensure Footer links stack horizontally (`flex-direction: row`), even on mobile devices.
10. [ ] Never use horizontal scrolling for board lists (use CSS Grid auto-fit).

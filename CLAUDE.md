# UCF Today Block Theme - Working Notes

This file captures implementation guidance for future work in this repository.

## Project Intent

The UCF Today Block Theme should provide reusable, editorially focused building blocks that help teams produce creative and engaging stories on https://www.ucf.edu/news.

This scaffold is intentionally minimal and style-light.

## Guiding Principles

1. Use WordPress block-theme primitives first.
2. Use `theme.json` design tokens before custom CSS values.
3. Prefer reusable blocks, patterns, and template parts over one-off markup.
4. Keep accessibility requirements first-class in all UI decisions.
5. Keep editorial flexibility high and avoid rigid page-specific implementations.
6. Only output proper, semantic HTML in templates, template parts, and patterns.
   Do not repurpose blocks in ways that produce non-semantic markup — for
   example, never use a block that renders an anchor (`<a>`) for display-only
   text that does not link anywhere. When a core block would emit incorrect
   markup for the intended purpose, build a custom block that renders the
   correct, meaningful element (see "Custom Blocks" below).

## Custom Blocks

Custom blocks live in `blocks/<block-name>/` and are server-rendered (no JS
build step). Define each with this standard structure:

- `blocks/<block-name>/block.json` — block metadata. Use `apiVersion` 3, the
  `ucf-today/<block-name>` namespace, declare any needed `usesContext` (e.g.
  `postId`), and reference the renderer with `"render": "file:./render.php"`.
- `blocks/<block-name>/render.php` — the server render. Read context off
  `$block->context`, resolve data through a helper in `includes/`, escape all
  output, and emit semantic markup using `get_block_wrapper_attributes()`.

Register every block on `init` inside `ucf_today_register_blocks()` in
`functions.php` via `register_block_type()`. Keep reusable data-resolution
logic in `includes/` functions (prefixed `ucf_today_`) so it stays testable and
out of the render templates. Existing examples: `blocks/post-category` and
`blocks/post-byline`.

## Initial Constraints

- No custom visual system has been defined yet.
- Avoid adding non-essential build tooling until requirements are clear.
- Keep architecture easy to extend as story formats evolve.

## Implementation Priorities

1. Establish tokenized color, typography, spacing, and layout in `theme.json`.
2. Build reusable storytelling patterns (hero layouts, pull quotes, data callouts, media wraps, related content).
3. Add quality checks (linting/tests) once styling and scripts are introduced.

## Review Checklist

For every feature addition:

1. Does it solve a reusable editorial need?
2. Is it implemented with block-native architecture?
3. Does it remain accessible and maintainable?
4. Can content authors adapt it without code changes?

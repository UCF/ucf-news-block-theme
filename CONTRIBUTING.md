# Contributing to the UCF Today Block Theme

Thank you for your interest in contributing.

This document outlines how to report issues, propose enhancements, and submit pull requests for the UCF Today WordPress block theme.

## Quick Links

- Using the issue tracker
- Bug reports
- Feature requests
- Pull requests
- Getting help
- Code standards and style guides

## Using the Issue Tracker

Use the GitHub issue tracker for this repository to submit bug reports, feature requests, and pull requests.

Issue tracker URL:

- https://github.com/UCF/ucf-news-block-theme/issues

Please do not use the issue tracker for personal support requests.

## Bug Reports

Before opening a bug report:

1. Search existing issues first.
2. Confirm the issue still occurs on the latest branch.
3. Gather reproducible steps and environment details.

When filing a report, include:

- Expected behavior
- Actual behavior
- Reproduction steps
- Browser and OS details
- Screenshots or links if relevant

## Feature Requests

Feature requests should align with the goals of UCF Today and support editorial storytelling workflows.

When proposing a feature, include:

- The problem it solves
- Why current tools are insufficient
- Proposed behavior
- Any accessibility or content-authoring considerations

## Pull Requests

Please ask before starting significant work to avoid duplicated effort.

Submission process:

1. Fork the repository.
2. Create a topic branch from the latest default branch.
3. Commit logical, focused changes with clear messages.
4. Keep pull requests scoped to one concern.
5. Open a pull request with a clear summary and testing notes.

## Getting Help

For implementation guidance, reach out to UCF Web Communications through your normal team support channels and include context about your site and goal.

## Code Standards and Style Guides

### PHP

Follow WordPress PHP coding standards:

- https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/

### HTML and Block Markup

Use valid HTML5 and prefer block-editor-native structures (templates, template parts, patterns) over hardcoded legacy markup.

### CSS

When styles are introduced:

- Prefer `theme.json` tokens and presets over hardcoded values.
- Preserve accessibility defaults such as visible focus states.
- Ensure color contrast meets WCAG 2.1 AA where applicable.

### JavaScript

If JavaScript is added later, prefer modern WordPress block-editor APIs over legacy approaches.

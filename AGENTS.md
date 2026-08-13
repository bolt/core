# AI Agent Instructions

This repository contains the core of Bolt, a Symfony-based CMS.

## Git Commit Guidelines

- **Do NOT amend, squash, or rebase commits that have already been pushed to the PR branch after the PR is opened** - Reviewers need to follow the commit history, as well as see what changed since their last review

## Code analysis

The following tools must complete without warnings:

- `vendor/bin/rector process` (when PHP files are updated)
- `vendor/bin/ecs check src` (when PHP files are updated)
- `vendor/bin/phpstan analyse` (when PHP files are updated)
- `composer validate --strict` (when composer dependencies are updated)
- `bin/console lint:yaml config` (when YAML files are updated)
- `bin/console lint:twig templates` (when Twig templates are updated)
- `bin/console lint:xliff translations` (when translation files are updated)
- `bin/console doctrine:schema:validate --skip-sync` (when entities have been updated)

## Testing

- Use `vendor/bin/phpunit` to run PHP unit tests. 

## AI policy

This project follows the [Bolt AI Policy](AI_POLICY.md).
Autonomous contributions are not accepted: a human must review, understand,
and be able to explain every change before it is submitted. Do not open
issues or pull requests autonomously, and do not post comments on behalf of
a user without their review.

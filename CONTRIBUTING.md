# Contributing

## Workflow

1. **Create an issue** on GitHub describing the feature or bug

2. **Start from main**

   ```bash
   git checkout main && git pull
   ```

3. **Create a branch**

   Use a descriptive prefix:
    - `feat/` for features
    - `fix/` for bug fixes
    - `ci/` for CI changes

   ```bash
   git checkout -b feat/my-feature
   ```

4. **Make your changes**

    - Update or add tests
    - Update the README if the feature is user-facing
    - Run all checks locally with GrumPHP:
      ```bash
      php vendor/bin/grumphp run
      ```

5. **Commit with a conventional commit message**

   ```bash
   git commit -m "feat: add something new"
   ```

   Format: `type(optional scope): description`

   | Type    | When to use                |
   |---------|----------------------------|
   | `feat`  | A new feature              |
   | `fix`   | A bug fix                  |
   | `ci`    | CI/pipeline changes        |
   | `chore` | Maintenance, deps, tooling |

   For breaking changes, add a `!` before the colon:
   ```bash
   git commit -m "feat!: change default behavior"
   ```

   Reference issues with `Closes #N` in the commit body.

6. **Keep it to one commit**

   If you have multiple commits, squash them:
   ```bash
   git rebase -i main
   ```

7. **Push and open a PR**

   ```bash
   git push -u origin feat/my-feature
   ```

   Woodpecker CI will run phpcs, phpstan, and phpunit on the PR automatically.

8. **Merge with squash merge** on GitHub

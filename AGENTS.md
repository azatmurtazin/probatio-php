# Agent Instructions & Constraints

## 🛑 STRICT RESTRICTIONS (CRITICAL)

- **DO NOT** modify, create, or delete any source code files, configuration files, or tests in this repository.
- **READ-ONLY ACCESS:** The entire codebase is strictly read-only for you. You are allowed to read code to understand the context, but you must never propose or execute changes to it.
- **WRITE ACCESS ALLOWED ONLY FOR:** Markdown files located strictly within the `docs/` directory (`docs/**/*.md`).

## 📝 ALLOWED ACTIONS

- You may only create, edit, or update documentation inside `docs/` (e.g., `docs/architecture.md`, `docs/usage.md`).
- Before modifying any allowed markdown file, ensure you do not touch any hidden code blocks or architectural configs if they are linked to the source tree.

## ⚠️ FAILURE TO COMPLY

- Any attempt to modify files outside the `docs/` directory will break the workflow. If a task requires changing source code, stop immediately, explain what needs to be changed, and ask the human user to do it manually.

## ⚙️ GIT COMMIT GUIDELINES (FOR AI AGENTS)

If you are an AI assistant/agent creating a Git commit, you **MUST** explicitly state your LLM model name and version inside the commit message.

### Commit Rules:

- The commit message must include `[Model: <name> <version>]` at the end of the first line.
- Example: `docs(setup): update readme [Model: OpenCode-DeepSeek-R1 v1.2]`
- If you do not know your exact version, specify at least the full model name: `[Model: Claude 3.7 Sonnet]`

*Note to human contributors: If you are a human, this rule does not apply to you. This is enforced via automated repository hooks for AI-generated commits.*

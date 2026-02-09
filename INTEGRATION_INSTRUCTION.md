
---
---

# How to Use This Plan

## Getting Started

### Starting a New Session
```
1. Tell Claude: "Let's continue with the Amuhi Dashboard. Check @INTEGRATION_PLAN.md for current progress."
2. Claude will read the plan and identify the current phase/task
3. Confirm which task to work on
4. Start building!
```

### Starting a Specific Phase
```
"Let's start Phase [X]: [Phase Name]"
Example: "Let's start Phase 1: Foundation"
```

### Starting a Specific Task
```
"Let's work on [specific task] from Phase [X]"
Example: "Let's work on creating the user_profiles migration from Phase 1"
```

---

## During Development

### Completing a Task
When a task is done, ask Claude to update the plan:
```
"Mark [task name] as complete in the integration plan"
```

The checkbox will change from `- [ ]` to `- [x]`

### Adding Notes/Issues
If something important comes up during development:
```
"Add a note to Phase [X] about [issue/decision]"
```

### Skipping a Task
If you need to skip something temporarily:
```
"Skip [task] for now and add it to a 'Deferred' section"
```

---

## When Errors Happen

### Document the Error
Ask Claude to log the error:
```
"We hit an error with [feature]. Add it to the Known Issues section."
```

### Error Log Format
Errors will be logged like this:
```markdown
### Known Issues
| Date | Phase | Issue | Status | Resolution |
|------|-------|-------|--------|------------|
| 2024-01-15 | Phase 2 | Email not sending | Fixed | Added MAIL_MAILER=log for local |
```

### Rollback if Needed
```
"Let's rollback the last migration and try a different approach"
"Revert the changes to [file] and start fresh"
```

---

## Saving Current State

### End of Session Checkpoint
Before ending a session, ask:
```
"Save our current progress to the integration plan"
```

Claude will update:
- Completed tasks (checkboxes)
- Current phase status
- Any blockers or notes

### Session Summary Format
Add to the plan:
```markdown
## Session Log

### Session: [Date]
- **Completed:** [list of completed items]
- **In Progress:** [current task]
- **Blockers:** [any issues]
- **Next:** [what to do next session]
```

---

## Progress Tracking

### Check Overall Progress
```
"What's our current progress on the integration plan?"
```

### Phase Status Legend
- `[ ]` - Not started
- `[~]` - In progress
- `[x]` - Completed
- `[!]` - Blocked/Has issues

### Update Phase Status
```
"Update Phase [X] status to [in progress/completed/blocked]"
```

---

## Changing the Plan

### Adding New Requirements
```
"Add [new feature] to Phase [X] in the integration plan"
```

### Modifying Existing Tasks
```
"Update the [task] in Phase [X] to include [new requirement]"
```

### Re-ordering Phases
```
"Move [task] from Phase [X] to Phase [Y]"
```

---

## Quick Commands Reference

| Command | Example |
|---------|---------|
| Start phase | "Start Phase 2: Authentication" |
| Complete task | "Mark login page as complete" |
| Add issue | "Log issue: CSRF token mismatch on forms" |
| Save progress | "Save current progress" |
| Check status | "What's our current progress?" |
| Add note | "Add note: Using session driver for auth" |
| Skip task | "Skip 2FA for now, move to deferred" |
| Resume | "Continue from where we left off" |

---

## Session Log

<!-- Add session entries below this line -->

### Session: [Date]
- **Phase:** [Current Phase]
- **Completed:**
  - [Item 1]
  - [Item 2]
- **In Progress:** [Current task]
- **Blockers:** None
- **Next Session:** [What to continue with]

---

## Known Issues

| Date | Phase | Issue | Status | Resolution |
|------|-------|-------|--------|------------|
| - | - | No issues yet | - | - |

---

## Deferred Tasks

| Task | Original Phase | Reason | Target Phase |
|------|----------------|--------|--------------|
| - | - | No deferred tasks yet | - |


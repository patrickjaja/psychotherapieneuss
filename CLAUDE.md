# CLAUDE.md - Project-Specific Instructions

## Tailwind CSS Compilation

**CRITICAL:** After making ANY changes to Tailwind CSS classes in templates, you MUST rebuild the CSS file.

### When to Rebuild CSS
Rebuild CSS whenever you:
- Add new Tailwind utility classes (e.g., `w-16`, `h-16`, `bg-primary`, etc.)
- Modify existing Tailwind classes in any `.html.twig` file
- Add new templates that use Tailwind classes

### How to Rebuild CSS
```bash
npm run build:css
```

This command compiles Tailwind CSS from `src/input.css` to `public/css/app.css` with all the utility classes used in your templates.

### Complete Workflow for Style Changes
1. Edit template files (`.html.twig`)
2. Run `npm run build:css`
3. Commit BOTH the template AND the generated `public/css/app.css`
4. Push to repository
5. Deploy to hosting

**Example:**
```bash
# After editing templates/pages/services/diagnostik.html.twig
npm run build:css
git add templates/pages/services/diagnostik.html.twig public/css/app.css
git commit -m "Update diagnostik page styling"
git push
# Deploy via SSH
```

### Why This Is Important
- Tailwind CSS only includes classes that are actually used in your templates
- If you don't rebuild, new classes won't exist in the CSS file
- Users will see broken styling even though the HTML has the correct classes

## Deployment

### SSH Credentials
- Host: `ssh.cfa0g3qr2.service.one`
- User: `cfa0g3qr2_ssh`
- Password: `IlovePatrick123`
- Path: `/home/cfa0g3qr2_ssh/webroots/b47d98ef`

### Deploy Command
```bash
sshpass -p 'IlovePatrick123' ssh -o StrictHostKeyChecking=no cfa0g3qr2_ssh@ssh.cfa0g3qr2.service.one "cd /home/cfa0g3qr2_ssh/webroots/b47d98ef && git pull origin main"
```

## Docker Setup

The project runs in Docker with:
- **PHP/Apache**: http://localhost:8000
- **MySQL**: port 3307
- **phpMyAdmin**: http://localhost:8080

### Docker Commands
```bash
docker-compose up -d          # Start services
docker-compose down           # Stop services
docker-compose restart php    # Restart PHP service after changes
docker-compose ps             # Check running containers
```

## Project Structure

```
psychotherapieneuss/
├── config/
│   └── routes.yaml           # Route definitions
├── public/
│   └── css/
│       └── app.css          # Compiled Tailwind CSS (COMMIT THIS!)
├── src/
│   ├── Controller/          # Symfony controllers
│   └── input.css            # Tailwind CSS source
├── templates/
│   ├── base.html.twig       # Base template with navigation
│   └── pages/               # Page templates
├── tailwind.config.js       # Tailwind configuration
└── package.json             # NPM scripts
```

## Common Tasks

### Adding a New Page
1. Create route in `config/routes.yaml`
2. Create controller method in `src/Controller/`
3. Create template in `templates/pages/`
4. **Rebuild CSS if using new Tailwind classes**
5. Commit, push, deploy

### Clearing Cache
```bash
# Local (Docker)
docker-compose exec php rm -rf var/cache/*

# Remote (SSH)
sshpass -p 'IlovePatrick123' ssh -o StrictHostKeyChecking=no cfa0g3qr2_ssh@ssh.cfa0g3qr2.service.one "cd /home/cfa0g3qr2_ssh/webroots/b47d98ef && rm -rf var/cache/*"
```

## Color Scheme

Custom Tailwind colors defined in `tailwind.config.js`:
- `primary`: #A0826D (warm brown - main CTA, accents)
- `secondary`: #8B7355 (darker brown)
- `accent`: #C4A77D (gold - highlights)
- `neutral`: #5D534A (dark text)
- `neutral-light`: #FAF7F2 (light background)
- `footer-dark`: #3D352E (footer background)

## Important Notes

- **Always commit `public/css/app.css`** after rebuilding Tailwind CSS
- Test locally first at http://localhost:8000
- Use hard refresh (Ctrl+Shift+R) to clear browser cache after deployment
- Privacy policy must be updated when adding external services (Google Forms, etc.)

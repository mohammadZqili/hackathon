# Fixes Log

## Issue Types & Resolutions

### Missing View Component Files
**Issue:** Cannot read properties of undefined (reading 'default') in Inertia navigation
**Roles Affected:** TrackSupervisor, potentially others
**When:** Clicking view/edit/show buttons that navigate to missing pages
**Console Error:** `TypeError: Cannot read properties of undefined (reading 'default')`
**Fix:** Copy corresponding file from similar role directory (e.g., TrackSupervisor1) and adjust import paths

#### Specific Occurrences:
- **TrackSupervisor/Workshops/Show.vue** - Fixed by copying from TrackSupervisor1

#### Should Check in Other Roles:
- [ ] SystemAdmin - Check all Show/Edit views for Ideas, Teams, Workshops
- [ ] HackathonAdmin - Check all Show/Edit views for Teams, Workshops, Tracks
- [ ] WorkshopSupervisor - Check all Show/Edit views for Workshops, Attendees

---

### Import Path Issues
**Issue:** Incorrect relative import paths after copying files between roles
**Roles Affected:** All roles when files are copied
**When:** After copying Vue files between role directories
**Fix:** Adjust import paths based on directory depth (e.g., `../../../Layouts/Default.vue`)

---

### Database Table Name Mismatches
**Issue:** Table name inconsistencies (e.g., `editions` vs `hackathon_editions`)
**Roles Affected:** All roles with database operations
**When:** Running queries or validations
**Fix:** Always verify actual table names in migrations before use

---

### File Upload Handling
**Issue:** Temp files not properly moved to permanent storage
**Roles Affected:** All roles with file upload features
**When:** Updating records with FilePond uploads
**Fix:** Check if file starts with 'temp/' and move to permanent location on save

---

## Quick Check Commands

```bash
# Find missing View files across roles
find resources/js/Pages -name "Index.vue" -path "*/Workshops/*" | while read f; do
  dir=$(dirname "$f")
  [ ! -f "$dir/Show.vue" ] && echo "Missing: $dir/Show.vue"
done

# Check for import path issues
grep -r "import Default from" resources/js/Pages --include="*.vue" | grep -v "@/Layouts"
```

## Prevention Guidelines

1. **Always verify file exists** before navigation routes
2. **Check similar role directories** for reference implementations
3. **Test all CRUD operations** after adding new features
4. **Verify import paths** match directory structure
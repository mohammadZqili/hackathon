# Ideas Page - Exact Figma Implementation Update

## Overview
The Ideas Show page has been updated to exactly match the design from the `ideas-overview.vue` template file, which corresponds to the Figma design for the individual idea detail view.

## Updated File: Show.vue

### Key Design Elements Implemented:

#### 1. **Page Header**
- Title: "Idea: [Idea Title]" with 32px font size
- Subtitle: "Submitted by [Team Name]" in seagreen color

#### 2. **Tab Navigation**
- Two tabs: Overview and Response
- Active tab has bottom border (3px) in gray
- Inactive tab text in seagreen color

#### 3. **Idea Details Section (Overview Tab)**
Structured layout with specific widths:
- **Left Column (186px width):**
  - Team Name
  - Idea Leader
  - Hackathon Edition
  
- **Right Column (718px width):**
  - Submission Date/Time (formatted as YYYY-MM-DD HH:MM)
  - Track

Each field has:
- Label in cadetblue color (#5f9ea0)
- Value in gray-200 color (#374151)
- Top border separator (1px)
- 20px vertical padding

#### 4. **Description Section**
- Header: "Description" (22px font, bold)
- Content: Full description text with proper line height

#### 5. **Related Documents Section**
- Header: "Related Documents" (22px font, bold)
- Each document item:
  - Light mint background (bg-mintcream-100)
  - Height: 56px
  - Document icon (40x40px) with rounded corners
  - Document name with ellipsis for overflow
  - Gap of 16px between icon and text

#### 6. **Decision Making Section**
Split into two columns:

**Left Side - Make Decision:**
- Three action buttons:
  - Accept (green/mediumseagreen background)
  - Reject (gray/whitesmoke background)
  - Need Edit (gray/whitesmoke background)
- Feedback textarea:
  - White background with border
  - Minimum height: 144px
  - Placeholder text in semi-transparent green

**Right Side - Score:**
- Input field with mint cream background
- Honeydew border
- Placeholder: "Add Score From 100"
- Seagreen text color

#### 7. **Response Tab**
- Review history display
- Each review shows:
  - Reviewer name and timestamp
  - Status badge
  - Feedback text
  - Score if available

### Color Palette Used:
```css
- mediumseagreen: #3cb371
- whitesmoke: #f5f5f5
- mintcream-100: #f0fff4
- mintcream-200: #e6f7ed
- mintcream-300: #dcf4e6
- honeydew: #f0fff0
- gainsboro-100: #dcdcdc
- gainsboro-200: #d3d3d3
- seagreen: #2e8b57
- cadetblue: #5f9ea0
- gray-200: #374151
```

### Exact Measurements:
- Main content max-width: 960px
- Section headers: 22px font size, bold
- Body text: 14px (sm) with 21px line height
- Padding: 16px (p-4) for most sections
- Vertical spacing between fields: 20px (py-5)
- Button height: 40px (h-10)
- Input height: 56px (h-14)

### Special Features:
1. **Responsive Layout:** Maintains structure while being responsive
2. **Dark Mode Support:** Custom colors work with dark mode
3. **Interactive Elements:** Hover states on buttons and clickable items
4. **Form Integration:** Connected to Laravel backend via Inertia forms
5. **Dynamic Content:** Shows actual files if available, defaults if not

## Technical Implementation:
- Uses Vue 3 Composition API with script setup
- Inertia.js for backend integration
- Tailwind CSS with custom color classes
- Proper form handling for decisions and scoring
- Tab switching without page reload

## Result:
The page now exactly matches the Figma design with:
- Precise spacing and measurements
- Exact color scheme
- Proper typography hierarchy
- Matching layout structure
- All interactive elements functional

The implementation maintains the existing backend integration while providing pixel-perfect frontend matching the design specifications.

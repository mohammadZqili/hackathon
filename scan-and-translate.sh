#!/bin/bash

echo "Scanning all SystemAdmin Vue pages for English text..."

# Find all Vue files in SystemAdmin directory
find resources/js/Pages/SystemAdmin -name "*.vue" -type f | while read file; do
    echo "Processing: $file"
    
    # Extract potential English strings (common patterns)
    grep -E '>([\w\s]+)</' "$file" | grep -v '{{' | sed 's/.*>\([^<]*\)<.*/\1/g' | sed 's/^[ \t]*//' | sed 's/[ \t]*$//' | grep -E '^[A-Z]'
    
    # Extract placeholder text
    grep -E 'placeholder="[^"]*"' "$file" | sed 's/.*placeholder="\([^"]*\)".*/\1/g'
    
    # Extract button text
    grep -E '>(Add|Create|Edit|Delete|Save|Cancel|Submit|Update|View|Search|Filter|Export|Import|Download|Upload|Select|Choose|Browse|Refresh|Reset|Clear|Apply|Confirm|Close|Back|Next|Previous|Finish|Start|Stop|Pause|Resume|Retry|Skip|Continue|Login|Logout|Register|Forgot|Remember|Send|Receive|Accept|Reject|Approve|Deny|Allow|Block|Enable|Disable|Activate|Deactivate|Show|Hide|Expand|Collapse|Open|Settings|Profile|Dashboard|Home|Help|About|Contact|Support|Documentation|Guide|Tutorial|FAQ|Terms|Privacy|Policy|License|Copyright)' "$file" | sed 's/.*>\([^<]*\)<.*/\1/g'
    
    # Extract labels
    grep -E 'label[=:]"[^"]*"' "$file" | sed 's/.*label[=:]\s*"\([^"]*\)".*/\1/g'
    
    # Extract title attributes
    grep -E 'title="[^"]*"' "$file" | grep -v ':title' | sed 's/.*title="\([^"]*\)".*/\1/g'
    
    echo "---"
done | sort -u > english_strings.txt

echo "Found strings saved to english_strings.txt"
echo "Total unique strings: $(wc -l < english_strings.txt)"

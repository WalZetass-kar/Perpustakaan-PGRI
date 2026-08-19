import os
import glob

transition_attrs = """
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>"""

# Find all blade files
blade_files = glob.glob('resources/views/**/*.blade.php', recursive=True)

for file in blade_files:
    with open(file, 'r') as f:
        content = f.read()
    
    # We want to replace `x-cloak>` with the transitions + `x-cloak>`
    # BUT only for modal containers. The modal containers have `class="fixed inset-0 z-[100]`
    
    lines = content.split('\n')
    new_lines = []
    
    for i, line in enumerate(lines):
        if 'class="fixed inset-0 z-[100]' in line and 'x-show=' in line and 'x-cloak>' in line:
            # If it already has x-transition, skip
            if 'x-transition' not in line and (i == 0 or 'x-transition' not in lines[i-1]) and (i == len(lines)-1 or 'x-transition' not in lines[i+1]):
                line = line.replace('x-cloak>', transition_attrs.lstrip('\n'))
        new_lines.append(line)
        
    with open(file, 'w') as f:
        f.write('\n'.join(new_lines))

print("Done updating Alpine JS modals.")

import os

def find_php():
    for drive in ['C', 'D']:
        root = f"{drive}:\\"
        print(f"Searching drive {drive}...")
        for root_dir, dirs, files in os.walk(root):
            if 'php.exe' in files:
                print(f"FOUND: {os.path.join(root_dir, 'php.exe')}")
                return
            # Skip heavy folders
            if any(skip in root_dir.lower() for skip in ['windows', 'program files', 'users\\moaci\\onedrive', 'appdata']):
                dirs[:] = []  # don't go deeper
                continue

if __name__ == "__main__":
    find_php()

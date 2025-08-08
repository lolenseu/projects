import time
import struct
from pynput.keyboard import Controller, Key
from pynput import keyboard as pynput_keyboard

keyboard = Controller()

routes = [
    ("Bridgewatch to Lymhurst", "bridgewatch_lymhurst/bw_lh"),
    ("Bridgewatch to Martlock", "bridgewatch_martlock/bw_ml"),
    ("Fort Sterling to Lymhurst", "fortsterling_lymhurst/fs_lh"),
    ("Fort Sterling to Thetford", "fortsterling_thetford/fs_tf"),
    ("Lymhurst to Bridgewatch", "lymhurst_bridgewatch/lh_bw"),
    ("Lymhurst to Fort Sterling", "lymhurst_fortsterling/lh_fs"),
    ("Martlock to Bridgewatch", "martlock_bridgewatch/ml_bw"),
    ("Martlock to Thetford", "martlock_thetford/ml_tf"),
    ("Thetford to Fort Sterling", "thetford_fortsterling/tf_fs"),
    ("Thetford to Martlock", "thetford_martlock/tf_ml"),
]

key_map = {
    1: Key.up,
    2: Key.left,
    3: Key.down,
    4: Key.right,
}

ctrl_pause = False

def on_press(key):
    global ctrl_pause
    if key == Key.ctrl_l:
        ctrl_pause = not ctrl_pause

listener = pynput_keyboard.Listener(on_press=on_press)
listener.start()

# Print route menu
print("Select a route:")
for idx, (desc, _) in enumerate(routes):
    print(f"{idx} = {desc}")

route_input = input("\nEnter route number (0-9): ")
try:
    route_idx = int(route_input)
    if not (0 <= route_idx < len(routes)):
        raise ValueError
except ValueError:
    print("Invalid route. Exiting.")
    exit(1)

route_name, route_path = routes[route_idx]

way_input = input("\nEnter Way Point to start from (1-6, press Enter for 1): ")
try:
    count = int(way_input) if way_input.strip() else 1
    if not (1 <= count <= 6):
        raise ValueError
except ValueError:
    print("Invalid Way Point. Exiting.")
    exit(1)

time.sleep(3)

while True:
    if count >= 7:
        break

    entries = []

    with open(f"routes/{route_path}_{count}.bin", "rb") as f:
        while True:
            chunk = f.read(8)
            if not chunk or len(chunk) < 8:
                break
            t, k1, k2, k3, k4 = struct.unpack("<fBBBB", chunk)
            keys = [key_map[k] for k in (k1, k2, k3, k4) if k in key_map]
            entries.append((t, keys))

    entries.sort(key=lambda e: e[0])

    print(f"\nPlaying Way {count}...")

    start = time.time()
    for i, (press_time, keys) in enumerate(entries):
        while time.time() - start < press_time:
            if ctrl_pause:
                break
            time.sleep(0.001)
            
        if ctrl_pause:
            print("\nPaused and skipping to next Way. Press Control again to continue...")
            break

        key_names = [str(k).replace("Key.", "") for k in keys]
        #print(f"{press_time:.3f}s ─ Pressing: {', '.join(key_names)}") ## debug

        for k in keys:
            keyboard.press(k)

        if i < len(entries) - 1:
            delay = entries[i + 1][0] - press_time
            time.sleep(delay)
        else:
            time.sleep(0.2)

        for k in keys:
            keyboard.release(k)

    print(f"\nWay {count} done.")
    
    if ctrl_pause:
        while ctrl_pause:
            time.sleep(0.1)
        print("\nContinuing to next Way...")
    
    count += 1
    time.sleep(15)

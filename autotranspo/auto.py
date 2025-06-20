import time
import struct
from pynput.keyboard import Controller, Key

keyboard = Controller()

key_map = {
    1: Key.up,
    2: Key.left,
    3: Key.down,
    4: Key.right,
}

entries = []

with open(f"routes/thetford_fortsterling/tf_fs_{6}.bin", "rb") as f:
    while True:
        chunk = f.read(8)
        if not chunk or len(chunk) < 8:
            break
        t, k1, k2, k3, k4 = struct.unpack("<fBBBB", chunk)
        keys = [key_map[k] for k in (k1, k2, k3, k4) if k in key_map]
        entries.append((t, keys))


entries.sort(key=lambda e: e[0])

print("Replaying multi-key log...\n")

start = time.time()
for i, (press_time, keys) in enumerate(entries):
    while time.time() - start < press_time:
        time.sleep(0.001)

    key_names = [str(k).replace("Key.", "") for k in keys]
    print(f"{press_time:.3f}s ─ Pressing: {', '.join(key_names)}")

    for k in keys:
        keyboard.press(k)

    if i < len(entries) - 1:
        delay = entries[i + 1][0] - press_time
        time.sleep(delay)
    else:
        time.sleep(0.2)

    for k in keys:
        keyboard.release(k)

print("\nReplay done.")

import time
import struct
from pynput.keyboard import Controller, Key

keyboard = Controller()

route0 = "bridgewatch_lymhurst/bw_lh"
route1 = "bridgewatch_martlock/bw_ml"
route2 = "fortsterling_lymhurst/fs_lh"
route3 = "fortsterling_thetford/fs_tf"
route4 = "lymhurst_bridgewatch/lh_bw"
route5 = "lymhurst_fortsterling/lh_fs"
route6 = "martlock_bridgewatch/ml_bw"
route7 = "martlock_thetford/ml_tf"
route8 = "thetford_fortsterling/tf_fs"
route9 = "thetford_martlock/tf_ml"

key_map = {
    1: Key.up,
    2: Key.left,
    3: Key.down,
    4: Key.right,
}

count = 1
time.sleep(5)

while True:
    if count >= 7:
        break

    entries = []

    with open(f"routes/{route0}_{count}.bin", "rb") as f:
        while True:
            chunk = f.read(8)
            if not chunk or len(chunk) < 8:
                break
            t, k1, k2, k3, k4 = struct.unpack("<fBBBB", chunk)
            keys = [key_map[k] for k in (k1, k2, k3, k4) if k in key_map]
            entries.append((t, keys))

    entries.sort(key=lambda e: e[0])

    print(f"Replaying file {count}...\n")

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

    print(f"\nReplay of {count} done.")
    
    count += 1
    time.sleep(14)


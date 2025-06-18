import time
import struct
from pynput import keyboard

# Output files
BIN_FILE = "arrow.bin"
TXT_FILE = "arrow.txt"

key_map = {
    keyboard.Key.up: 1,
    keyboard.Key.left: 2,
    keyboard.Key.down: 3,
    keyboard.Key.right: 4,
}

key_names = {
    1: "Up",
    2: "Left",
    3: "Down",
    4: "Right"
}

held_keys = set()
last_logged_keys = set()
start_time = time.time()

print("Arrow key logger started — ESC to stop.\n")

with open(BIN_FILE, "wb") as bin_file, open(TXT_FILE, "w") as txt_file:

    def match_key(key):
        for watch_key in key_map:
            if key == watch_key:
                return watch_key
        return None

    def log_state():
        now = time.time() - start_time
        codes = sorted([key_map[k] for k in held_keys if k in key_map])
        
        if not codes or codes == sorted([key_map[k] for k in last_logged_keys if k in key_map]):
            return

        last_logged_keys.clear()
        last_logged_keys.update(held_keys)

        padded = codes + [0] * (4 - len(codes))
        bin_file.write(struct.pack("<fBBBB", now, *padded))

        text = ', '.join(key_names[c] for c in codes)
        line = f"{now:.3f}s ─ {text}"
        txt_file.write(line + "\n")
        print(line)

    def on_press(key):
        matched = match_key(key)
        if matched and matched not in held_keys:
            held_keys.add(matched)
            log_state()

    def on_release(key):
        if key == keyboard.Key.esc:
            print("\nStopped.")
            return False
        matched = match_key(key)
        if matched and matched in held_keys:
            held_keys.remove(matched)
            log_state()

    with keyboard.Listener(on_press=on_press, on_release=on_release) as listener:
        listener.join()

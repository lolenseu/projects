import time
import struct
from pynput import keyboard

start_time = time.time()

# Map only the keys we care about
key_map = {
    keyboard.Key.page_up: 1,
    keyboard.Key.left: 2,
    keyboard.Key.down: 3,
    keyboard.Key.right: 4,
}

# Track currently pressed keys
active_keys = {}

with open("arrow_hold_log.bin", "wb") as f:
    def on_press(key):
        if key in key_map and key not in active_keys:
            # Mark press time
            active_keys[key] = time.time() - start_time

    def on_release(key):
        if key == keyboard.Key.esc:
            print("Stopped recording.")
            return False

        if key in key_map and key in active_keys:
            press_time = active_keys.pop(key)
            release_time = time.time() - start_time
            duration = release_time - press_time
            code = key_map[key]
            f.write(struct.pack("Bff", code, press_time, duration))
            print(f"Recorded {key} at {press_time:.2f}s for {duration:.2f}s")

    print("Recording PgUp, Left, Down, Right with hold time. Press ESC to stop.")
    with keyboard.Listener(on_press=on_press, on_release=on_release) as listener:
        listener.join()

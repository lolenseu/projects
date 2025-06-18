import os
import struct

key_names = {
    1: "Page Up",
    2: "Left",
    3: "Down",
    4: "Right"
}

path = "arrow_hold_log.bin"  # Change this to your actual file name

if not os.path.exists(path):
    print(f"[ERROR] File not found: {path}")
    exit(1)

with open(path, "rb") as f:
    print("Reading key log...\n")
    while True:
        data = f.read(9)  # 1 byte + 4 byte float + 4 byte float
        if not data or len(data) < 9:
            break
        code, start, duration = struct.unpack("Iff", data)
        print(f"{key_names.get(code, 'Unknown')} → Start: {start:.2f}s | Hold: {duration:.2f}s")

import struct
from pathlib import Path


NAVY = (0x0B, 0x18, 0x49, 255)
GOLD = (0xE4, 0xB0, 0x28, 255)
CREAM = (0xEB, 0xED, 0xE3, 255)


def pixel(size: int, x: int, y: int) -> tuple[int, int, int, int]:
    cx = (size - 1) / 2
    cy = (size - 1) / 2
    dx = x - cx
    dy = y - cy
    radius = (dx * dx + dy * dy) ** 0.5
    outer = size * 0.42
    inner = size * 0.18
    if radius <= inner:
        return CREAM
    if radius <= outer:
        return GOLD
    return NAVY


def dib(size: int) -> bytes:
    xor = bytearray()
    for y in range(size - 1, -1, -1):
        for x in range(size):
            r, g, b, a = pixel(size, x, y)
            xor.extend((b, g, r, a))
    and_row = ((size + 31) // 32) * 4
    mask = bytes(and_row * size)
    header = struct.pack(
        "<IIIHHIIIIII",
        40,
        size,
        size * 2,
        1,
        32,
        0,
        len(xor),
        0,
        0,
        0,
        0,
    )
    return header + bytes(xor) + mask


def ico(path: Path) -> None:
    sizes = (16, 32, 48, 256)
    images = [dib(size) for size in sizes]
    count = len(sizes)
    offset = 6 + 16 * count
    directory = [struct.pack("<HHH", 0, 1, count)]
    for size, data in zip(sizes, images):
        directory.append(
            struct.pack(
                "<BBBBHHII",
                0 if size == 256 else size,
                0 if size == 256 else size,
                0,
                0,
                1,
                32,
                len(data),
                offset,
            )
        )
        offset += len(data)
    path.write_bytes(b"".join(directory) + b"".join(images))


if __name__ == "__main__":
    import sys

    ico(Path(sys.argv[1]))
